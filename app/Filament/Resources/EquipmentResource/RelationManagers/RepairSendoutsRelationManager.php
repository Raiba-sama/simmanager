<?php

namespace App\Filament\Resources\EquipmentResource\RelationManagers;

use App\Models\EquipmentRepairSendout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Barryvdh\DomPDF\Facade\Pdf;

class RepairSendoutsRelationManager extends RelationManager
{
    protected static string $relationship = 'repairSendouts';

    protected static ?string $title = 'Envois en réparation (fournisseur externe)';

    protected static ?string $recordTitleAttribute = 'supplier_name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('supplier_name')
                    ->label('Fournisseur externe')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Nom du fournisseur / réparateur'),
                Forms\Components\TextInput::make('supplier_reference')
                    ->label('Référence / N° bon')
                    ->maxLength(255)
                    ->placeholder('Ex: BL-2026-001'),
                Forms\Components\DatePicker::make('sent_at')
                    ->label('Date d\'envoi')
                    ->required()
                    ->default(now())
                    ->displayFormat('d/m/Y'),
                Forms\Components\DatePicker::make('expected_return_at')
                    ->label('Date de retour prévue')
                    ->displayFormat('d/m/Y'),
                Forms\Components\Select::make('status')
                    ->label('Statut')
                    ->options([
                        EquipmentRepairSendout::STATUS_SENT => 'Envoyé',
                        EquipmentRepairSendout::STATUS_IN_REPAIR => 'En réparation',
                        EquipmentRepairSendout::STATUS_RETURNED => 'Retourné',
                    ])
                    ->default(EquipmentRepairSendout::STATUS_SENT)
                    ->required()
                    ->visible(fn (?EquipmentRepairSendout $record) => $record !== null),
                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('supplier_name')
            ->columns([
                Tables\Columns\TextColumn::make('supplier_name')
                    ->label('Fournisseur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier_reference')
                    ->label('Référence')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Envoyé le')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_return_at')
                    ->label('Retour prévu')
                    ->date('d/m/Y')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('returned_at')
                    ->label('Retourné le')
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        EquipmentRepairSendout::STATUS_SENT => 'Envoyé',
                        EquipmentRepairSendout::STATUS_IN_REPAIR => 'En réparation',
                        EquipmentRepairSendout::STATUS_RETURNED => 'Retourné',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        EquipmentRepairSendout::STATUS_SENT => 'warning',
                        EquipmentRepairSendout::STATUS_IN_REPAIR => 'info',
                        EquipmentRepairSendout::STATUS_RETURNED => 'success',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('sent_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Envoyer en réparation')
                    ->modalHeading('Nouvel envoi en réparation (fournisseur externe)')
                    ->after(function (EquipmentRepairSendout $record): void {
                        $record->equipment->update(['status' => 'maintenance']);
                    }),
                Tables\Actions\Action::make('exportCsv')
                    ->label('Exporter CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (RelationManager $livewire) {
                        $equipment = $livewire->ownerRecord->load(['equipmentType']);
                        $sendouts  = $equipment->repairSendouts()->with('creator')->orderBy('sent_at', 'desc')->get();

                        $cols = ['Tag actif', 'Type', 'Marque', 'Modèle', 'N° Série', 'Fournisseur', 'Référence', 'Date envoi', 'Retour prévu', 'Date retour', 'Statut', 'Envoyé par', 'Notes'];
                        $csvWrap = fn($v) => '"' . str_replace('"', '""', $v ?? '') . '"';

                        $csv  = "\xEF\xBB\xBF"; // BOM UTF-8
                        $csv .= implode(';', $cols) . "\n";

                        foreach ($sendouts as $s) {
                            $csv .= implode(';', array_map($csvWrap, [
                                $equipment->asset_tag,
                                $equipment->equipmentType?->name,
                                $equipment->brand,
                                $equipment->model,
                                $equipment->serial_number,
                                $s->supplier_name,
                                $s->supplier_reference,
                                $s->sent_at?->format('d/m/Y'),
                                $s->expected_return_at?->format('d/m/Y'),
                                $s->returned_at?->format('d/m/Y'),
                                $s->status_label,
                                $s->creator?->full_name,
                                $s->notes,
                            ])) . "\n";
                        }

                        $filename = 'reparations_' . ($equipment->asset_tag ?? $equipment->id) . '_' . now()->format('Ymd') . '.csv';
                        return response()->streamDownload(
                            fn() => print($csv),
                            $filename,
                            ['Content-Type' => 'text/csv; charset=UTF-8']
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('bordereauEnvoi')
                    ->label('Bordereau envoi')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->action(function (EquipmentRepairSendout $record) {
                        $equipment = $record->equipment->load(['equipmentType']);
                        $creator   = $record->creator;

                        $pdf = Pdf::loadView('equipment.repair-bordereau', [
                            'sendout'   => $record,
                            'equipment' => $equipment,
                            'creator'   => $creator,
                            'isReturn'  => false,
                        ]);
                        $pdf->setOption('encoding', 'utf-8');
                        $pdf->setOption('defaultFont', 'DejaVu Sans');
                        $pdf->setPaper('a4', 'portrait');

                        $slug     = preg_replace('/[^a-zA-Z0-9_-]/', '_', $equipment->asset_tag ?? $equipment->serial_number ?? $equipment->id);
                        $filename = 'bordereau_envoi_' . $slug . '_' . $record->sent_at->format('Ymd') . '.pdf';

                        return response()->streamDownload(fn() => print($pdf->output()), $filename, ['Content-Type' => 'application/pdf']);
                    }),
                Tables\Actions\Action::make('bordereauRetour')
                    ->label('Bordereau retour')
                    ->icon('heroicon-o-document-check')
                    ->color('success')
                    ->visible(fn (EquipmentRepairSendout $record): bool => $record->isReturned())
                    ->action(function (EquipmentRepairSendout $record) {
                        $equipment = $record->equipment->load(['equipmentType']);
                        $creator   = $record->creator;

                        $pdf = Pdf::loadView('equipment.repair-bordereau', [
                            'sendout'   => $record,
                            'equipment' => $equipment,
                            'creator'   => $creator,
                            'isReturn'  => true,
                        ]);
                        $pdf->setOption('encoding', 'utf-8');
                        $pdf->setOption('defaultFont', 'DejaVu Sans');
                        $pdf->setPaper('a4', 'portrait');

                        $slug     = preg_replace('/[^a-zA-Z0-9_-]/', '_', $equipment->asset_tag ?? $equipment->serial_number ?? $equipment->id);
                        $filename = 'bordereau_retour_' . $slug . '_' . ($record->returned_at?->format('Ymd') ?? now()->format('Ymd')) . '.pdf';

                        return response()->streamDownload(fn() => print($pdf->output()), $filename, ['Content-Type' => 'application/pdf']);
                    }),
                Tables\Actions\Action::make('markReturned')
                    ->label('Retourné')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->visible(fn (EquipmentRepairSendout $record): bool => !$record->isReturned())
                    ->form([
                        Forms\Components\DatePicker::make('returned_at')
                            ->label('Date de retour')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes (optionnel)')
                            ->rows(2),
                    ])
                    ->action(function (EquipmentRepairSendout $record, array $data): void {
                        $record->update([
                            'returned_at' => $data['returned_at'],
                            'status'      => EquipmentRepairSendout::STATUS_RETURNED,
                            'notes'       => $data['notes'] ?? $record->notes,
                        ]);
                        $record->equipment->update(['status' => 'available']);
                    })
                    ->successNotificationTitle('Équipement marqué comme retourné. Statut repassé à Disponible.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Envois en réparation (fournisseur externe)';
    }
}
