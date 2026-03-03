<?php

namespace App\Filament\Resources\EquipmentResource\RelationManagers;

use App\Models\EquipmentRepairSendout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('markReturned')
                    ->label('Retourné')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
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
                            'status' => EquipmentRepairSendout::STATUS_RETURNED,
                            'notes' => $data['notes'] ?? $record->notes,
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
