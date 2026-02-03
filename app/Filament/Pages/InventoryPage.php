<?php

namespace App\Filament\Pages;

use App\Models\Agency;
use App\Models\Equipment;
use App\Models\EquipmentType;
use App\Models\Zone;
use App\Exports\EquipmentInventoryExport;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class InventoryPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string $view = 'filament.pages.inventory';
    protected static ?string $navigationLabel = 'Inventaire';
    protected static ?string $title = 'Inventaire des Équipements';
    protected static ?string $navigationGroup = 'Parc Informatique';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\InventoryStatusChartWidget::class,
            \App\Filament\Widgets\InventoryTypeChartWidget::class,
            \App\Filament\Widgets\InventoryZoneChartWidget::class,
            \App\Filament\Widgets\InventoryAgencyChartWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 4;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Equipment::query()->with(['equipmentType', 'assignments.assignedToUser', 'assignments.assignedToAgency']))
            ->columns([
                Tables\Columns\TextColumn::make('asset_tag')
                    ->label('Tag / N° Inventaire')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('equipmentType.name')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand')
                    ->label('Marque')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('model')
                    ->label('Modèle')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('N° Série')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'available' => 'success',
                        'assigned' => 'info',
                        'maintenance' => 'warning',
                        'retired' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'available' => 'Disponible',
                        'assigned' => 'Attribué',
                        'maintenance' => 'En maintenance',
                        'retired' => 'Retiré',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('condition')
                    ->label('Condition')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'new' => 'success',
                        'excellent' => 'success',
                        'good' => 'info',
                        'fair' => 'warning',
                        'poor' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'new' => 'Neuf',
                        'excellent' => 'Excellent',
                        'good' => 'Bon',
                        'fair' => 'Moyen',
                        'poor' => 'Mauvais',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_assignment')
                    ->label('Assigné à')
                    ->getStateUsing(function (Equipment $record) {
                        $assignment = $record->assignments()->whereNull('returned_at')->first();
                        if ($assignment) {
                            return $assignment->assignedToUser?->full_name ?? $assignment->assignedToAgency?->name ?? '-';
                        }
                        return '-';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('assignments.assignedToUser', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhere('first_name', 'like', "%{$search}%");
                        })->orWhereHas('assignments.assignedToAgency', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    }),
                Tables\Columns\TextColumn::make('agency_zone')
                    ->label('Agence / Zone')
                    ->getStateUsing(function (Equipment $record) {
                        $assignment = $record->assignments()->whereNull('returned_at')->first();
                        if ($assignment && $assignment->assignedToAgency) {
                            $agency = $assignment->assignedToAgency;
                            $zone = $agency->zone;
                            return $zone
                                ? "{$agency->name} ({$zone->name})"
                                : $agency->name;
                        }
                        return '-';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('assignments.assignedToAgency', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhereHas('zone', function ($q2) use ($search) {
                                    $q2->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
                                });
                        });
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->label('Prix d\'achat')
                    ->money('XOF')
                    ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', ' ') . ' XOF' : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de création')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('zone_id')
                    ->label('Zone')
                    ->options(fn () => Zone::query()->orderBy('name')->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        return $query->whereHas('assignments', function ($q) use ($data) {
                            $q->whereNull('returned_at')
                                ->whereHas('assignedToAgency', function ($q2) use ($data) {
                                    $q2->where('zone_id', $data['value']);
                                });
                        });
                    })
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('agency_id')
                    ->label('Agence')
                    ->options(fn () => Agency::query()->orderBy('name')->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        return $query->whereHas('assignments', function ($q) use ($data) {
                            $q->whereNull('returned_at')->where('assigned_to_agency_id', $data['value']);
                        });
                    })
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('equipment_type_id')
                    ->label('Type d\'équipement')
                    ->relationship('equipmentType', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'available' => 'Disponible',
                        'assigned' => 'Attribué',
                        'maintenance' => 'En maintenance',
                        'retired' => 'Retiré',
                        'lost' => 'Perdu',
                        'damaged' => 'Endommagé',
                    ]),
                Tables\Filters\SelectFilter::make('condition')
                    ->label('Condition')
                    ->options([
                        'new' => 'Neuf',
                        'excellent' => 'Excellent',
                        'good' => 'Bon',
                        'fair' => 'Moyen',
                        'poor' => 'Mauvais',
                    ]),
                Tables\Filters\Filter::make('assigned')
                    ->label('Attribués uniquement')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'assigned')),
                Tables\Filters\Filter::make('available')
                    ->label('Disponibles uniquement')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'available')),
                Tables\Filters\Filter::make('warranty_expiring')
                    ->label('Garantie expirant bientôt')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('warranty_expires_at')
                        ->where('warranty_expires_at', '<=', now()->addMonths(3))
                        ->where('warranty_expires_at', '>=', now())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export_excel')
                        ->label('Exporter Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($records) {
                            $export = new EquipmentInventoryExport($records);
                            $filename = 'inventaire_equipements_' . now()->format('Y-m-d_His') . '.xlsx';
                            return Excel::download($export, $filename);
                        }),
                    Tables\Actions\BulkAction::make('export_pdf')
                        ->label('Exporter PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('danger')
                        ->action(function ($records) {
                            $equipment = $records->load(['equipmentType', 'assignments.assignedToUser', 'assignments.assignedToAgency']);
                            $pdf = Pdf::loadView('inventory.pdf', ['equipment' => $equipment]);
                            $pdf->setOption('encoding', 'utf-8');
                            $pdf->setOption('defaultFont', 'DejaVu Sans');
                            $pdf->setPaper('a4', 'landscape');
                            $filename = 'inventaire_equipements_' . now()->format('Y-m-d_His') . '.pdf';
                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, $filename);
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('export_all_excel')
                ->label('Exporter tout (Excel)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Exporter l\'inventaire (Excel)')
                ->modalDescription('Exporte les équipements selon les filtres actifs du tableau.')
                ->action(function () {
                    $equipment = $this->getFilteredTableQuery()
                        ->with(['equipmentType', 'assignments.assignedToUser', 'assignments.assignedToAgency'])
                        ->get();
                    $export = new EquipmentInventoryExport($equipment);
                    $filename = 'inventaire_' . now()->format('Y-m-d_His') . '.xlsx';
                    return Excel::download($export, $filename);
                }),
            \Filament\Actions\Action::make('export_all_pdf')
                ->label('Exporter tout (PDF)')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Exporter l\'inventaire (PDF)')
                ->modalDescription('Génère un PDF des équipements selon les filtres actifs du tableau.')
                ->action(function () {
                    $equipment = $this->getFilteredTableQuery()
                        ->with(['equipmentType', 'assignments.assignedToUser', 'assignments.assignedToAgency'])
                        ->get();
                    $pdf = Pdf::loadView('inventory.pdf', ['equipment' => $equipment]);
                    $pdf->setOption('encoding', 'utf-8');
                    $pdf->setOption('defaultFont', 'DejaVu Sans');
                    $pdf->setPaper('a4', 'landscape');
                    $filename = 'inventaire_' . now()->format('Y-m-d_His') . '.pdf';
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, $filename);
                }),
        ];
    }

    protected function getStats(): array
    {
        return $this->computeStats(Equipment::query());
    }

    /** Statistiques basées sur les filtres actuels du tableau */
    protected function getFilteredStats(): array
    {
        try {
            $query = $this->getFilteredTableQuery();
            return $this->computeStats($query);
        } catch (\Throwable $e) {
            return $this->getStats();
        }
    }

    protected function computeStats(Builder $query): array
    {
        $baseQuery = (clone $query)->select('equipment.id');
        $total = (clone $baseQuery)->count();
        $available = (clone $baseQuery)->where('equipment.status', 'available')->count();
        $assigned = (clone $baseQuery)->where('equipment.status', 'assigned')->count();
        $maintenance = (clone $baseQuery)->where('equipment.status', 'maintenance')->count();
        $retired = (clone $baseQuery)->whereIn('equipment.status', ['retired', 'lost', 'damaged'])->count();
        $totalValue = (clone $query)->whereNotNull('equipment.purchase_price')->sum('equipment.purchase_price');
        $warrantyExpiring = (clone $baseQuery)
            ->whereNotNull('equipment.warranty_expires_at')
            ->where('equipment.warranty_expires_at', '<=', now()->addMonths(3))
            ->where('equipment.warranty_expires_at', '>=', now())
            ->count();

        $typesCount = (clone $query)->distinct()->count('equipment.equipment_type_id');

        return [
            'total' => $total,
            'available' => $available,
            'assigned' => $assigned,
            'maintenance' => $maintenance,
            'retired' => $retired,
            'total_value' => $totalValue,
            'warranty_expiring' => $warrantyExpiring,
            'types_count' => $typesCount,
        ];
    }

    /** Répartition par zone (équipements attribués à une agence de la zone) */
    public function getStatsByZone(): \Illuminate\Support\Collection
    {
        return Zone::query()
            ->orderBy('name')
            ->get()
            ->map(function (Zone $zone) {
                $count = Equipment::query()
                    ->whereHas('assignments', function ($q) {
                        $q->whereNull('returned_at');
                    })
                    ->whereHas('assignments.assignedToAgency', function ($q) use ($zone) {
                        $q->where('zone_id', $zone->id);
                    })
                    ->count();
                return [
                    'id' => $zone->id,
                    'name' => $zone->name,
                    'code' => $zone->code,
                    'count' => $count,
                ];
            })
            ->filter(fn ($row) => $row['count'] > 0);
    }

    /** Répartition par agence (équipements attribués à l'agence) */
    public function getStatsByAgency(): \Illuminate\Support\Collection
    {
        return Agency::query()
            ->orderBy('name')
            ->with('zone')
            ->get()
            ->map(function (Agency $agency) {
                $count = Equipment::query()
                    ->whereHas('assignments', function ($q) use ($agency) {
                        $q->whereNull('returned_at')->where('assigned_to_agency_id', $agency->id);
                    })
                    ->count();
                return [
                    'id' => $agency->id,
                    'name' => $agency->name,
                    'code' => $agency->code,
                    'zone_name' => $agency->zone?->name,
                    'count' => $count,
                ];
            })
            ->filter(fn ($row) => $row['count'] > 0);
    }

    /** Répartition par type d'équipement */
    public function getStatsByType(): \Illuminate\Support\Collection
    {
        return EquipmentType::query()
            ->withCount('equipment')
            ->orderBy('equipment_count', 'desc')
            ->get()
            ->map(fn (EquipmentType $type) => [
                'id' => $type->id,
                'name' => $type->name,
                'count' => $type->equipment_count,
            ])
            ->filter(fn ($row) => $row['count'] > 0);
    }
}

