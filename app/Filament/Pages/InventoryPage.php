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
                Tables\Columns\TextColumn::make('assigned_matricule')
                    ->label('Matricule')
                    ->getStateUsing(function (Equipment $record) {
                        $assignment = $record->assignments()->whereNull('returned_at')->first();
                        return $assignment?->assignedToUser?->matricule ?? '-';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('assignments.assignedToUser', function ($q) use ($search) {
                            $q->where('matricule', 'like', "%{$search}%");
                        });
                    })
                    ->toggleable(),
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
                Tables\Filters\SelectFilter::make('disponibilite')
                    ->label('Disponibilité')
                    ->options([
                        'libre' => 'Libre (disponible)',
                        'attribue' => 'Attribué',
                        'sans_sn' => 'Sans SN / Tag (à compléter)',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) return $query;
                        return match ($data['value']) {
                            'libre' => $query->where('status', 'available'),
                            'attribue' => $query->where('status', 'assigned'),
                            'sans_sn' => $query->where(function ($q) {
                                $q->whereNull('serial_number')->orWhere('serial_number', '');
                            }),
                            default => $query,
                        };
                    }),
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

        $noSn = (clone $baseQuery)->where(function ($q) {
            $q->whereNull('equipment.serial_number')->orWhere('equipment.serial_number', '');
        })->count();

        return [
            'total' => $total,
            'available' => $available,
            'assigned' => $assigned,
            'maintenance' => $maintenance,
            'retired' => $retired,
            'total_value' => $totalValue,
            'warranty_expiring' => $warrantyExpiring,
            'types_count' => $typesCount,
            'no_sn' => $noSn,
        ];
    }

    /** Répartition par zone avec détails statut */
    public function getStatsByZone(): \Illuminate\Support\Collection
    {
        $statusLabels = self::statusLabels();

        return Zone::query()
            ->orderBy('name')
            ->get()
            ->map(function (Zone $zone) use ($statusLabels) {
                $base = Equipment::query()
                    ->whereHas('assignments', fn ($q) => $q->whereNull('returned_at'))
                    ->whereHas('assignments.assignedToAgency', fn ($q) => $q->where('zone_id', $zone->id));

                $total = (clone $base)->count();
                if ($total === 0) return null;

                $byStatus = [];
                foreach (array_keys($statusLabels) as $s) {
                    $c = (clone $base)->where('equipment.status', $s)->count();
                    if ($c > 0) $byStatus[$s] = $c;
                }

                $value = (clone $base)->whereNotNull('equipment.purchase_price')->sum('equipment.purchase_price');

                return [
                    'id' => $zone->id,
                    'name' => $zone->name,
                    'code' => $zone->code,
                    'count' => $total,
                    'by_status' => $byStatus,
                    'value' => $value,
                ];
            })
            ->filter();
    }

    /** Répartition par agence avec détails statut */
    public function getStatsByAgency(): \Illuminate\Support\Collection
    {
        $statusLabels = self::statusLabels();

        return Agency::query()
            ->orderBy('name')
            ->with('zone')
            ->get()
            ->map(function (Agency $agency) use ($statusLabels) {
                $base = Equipment::query()
                    ->whereHas('assignments', fn ($q) => $q->whereNull('returned_at')->where('assigned_to_agency_id', $agency->id));

                $total = (clone $base)->count();
                if ($total === 0) return null;

                $byStatus = [];
                foreach (array_keys($statusLabels) as $s) {
                    $c = (clone $base)->where('equipment.status', $s)->count();
                    if ($c > 0) $byStatus[$s] = $c;
                }

                $value = (clone $base)->whereNotNull('equipment.purchase_price')->sum('equipment.purchase_price');

                return [
                    'id' => $agency->id,
                    'name' => $agency->name,
                    'code' => $agency->code,
                    'zone_name' => $agency->zone?->name,
                    'count' => $total,
                    'by_status' => $byStatus,
                    'value' => $value,
                ];
            })
            ->filter();
    }

    /** Répartition par type d'équipement avec détails statut, condition, marques, valeur */
    public function getStatsByType(): \Illuminate\Support\Collection
    {
        $statusLabels = self::statusLabels();
        $conditionLabels = self::conditionLabels();

        return EquipmentType::query()
            ->withCount('equipment')
            ->orderBy('equipment_count', 'desc')
            ->get()
            ->map(function (EquipmentType $type) use ($statusLabels, $conditionLabels) {
                if ($type->equipment_count === 0) return null;

                $base = Equipment::query()->where('equipment_type_id', $type->id);

                $byStatus = [];
                foreach (array_keys($statusLabels) as $s) {
                    $c = (clone $base)->where('status', $s)->count();
                    if ($c > 0) $byStatus[$s] = $c;
                }

                $byCondition = [];
                foreach (array_keys($conditionLabels) as $cond) {
                    $c = (clone $base)->where('condition', $cond)->count();
                    if ($c > 0) $byCondition[$cond] = $c;
                }

                $brands = (clone $base)
                    ->select('brand', DB::raw('COUNT(*) as cnt'))
                    ->whereNotNull('brand')
                    ->where('brand', '!=', '')
                    ->groupBy('brand')
                    ->orderByDesc('cnt')
                    ->limit(5)
                    ->pluck('cnt', 'brand')
                    ->toArray();

                $value = (clone $base)->whereNotNull('purchase_price')->sum('purchase_price');

                $noSn = (clone $base)->where(function ($q) {
                    $q->whereNull('serial_number')->orWhere('serial_number', '');
                })->count();

                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'count' => $type->equipment_count,
                    'by_status' => $byStatus,
                    'by_condition' => $byCondition,
                    'brands' => $brands,
                    'value' => $value,
                    'no_sn' => $noSn,
                ];
            })
            ->filter();
    }

    /** Répartition par marque */
    public function getStatsByBrand(): \Illuminate\Support\Collection
    {
        $statusLabels = self::statusLabels();

        return Equipment::query()
            ->select('brand', DB::raw('COUNT(*) as total'))
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->groupBy('brand')
            ->orderByDesc('total')
            ->limit(15)
            ->get()
            ->map(function ($row) use ($statusLabels) {
                $base = Equipment::query()->where('brand', $row->brand);

                $byStatus = [];
                foreach (array_keys($statusLabels) as $s) {
                    $c = (clone $base)->where('status', $s)->count();
                    if ($c > 0) $byStatus[$s] = $c;
                }

                $types = (clone $base)
                    ->join('equipment_types', 'equipment.equipment_type_id', '=', 'equipment_types.id')
                    ->select('equipment_types.name', DB::raw('COUNT(*) as cnt'))
                    ->groupBy('equipment_types.name')
                    ->orderByDesc('cnt')
                    ->limit(3)
                    ->pluck('cnt', 'name')
                    ->toArray();

                $value = (clone $base)->whereNotNull('purchase_price')->sum('purchase_price');

                return [
                    'brand' => $row->brand,
                    'count' => $row->total,
                    'by_status' => $byStatus,
                    'types' => $types,
                    'value' => $value,
                ];
            });
    }

    /** Répartition par condition */
    public function getStatsByCondition(): array
    {
        $conditionLabels = self::conditionLabels();
        $result = [];
        foreach ($conditionLabels as $key => $label) {
            $count = Equipment::where('condition', $key)->count();
            if ($count > 0) {
                $result[$key] = [
                    'label' => $label,
                    'count' => $count,
                ];
            }
        }
        return $result;
    }

    public static function statusLabels(): array
    {
        return [
            'available' => 'Disponible',
            'assigned' => 'Attribué',
            'maintenance' => 'En maintenance',
            'retired' => 'Retiré',
            'lost' => 'Perdu',
            'damaged' => 'Endommagé',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'available' => '#198754',
            'assigned' => '#0d6efd',
            'maintenance' => '#ffc107',
            'retired' => '#6c757d',
            'lost' => '#dc3545',
            'damaged' => '#fd7e14',
        ];
    }

    public static function conditionLabels(): array
    {
        return [
            'new' => 'Neuf',
            'excellent' => 'Excellent',
            'good' => 'Bon',
            'fair' => 'Moyen',
            'poor' => 'Mauvais',
        ];
    }

    public static function conditionColors(): array
    {
        return [
            'new' => '#198754',
            'excellent' => '#20c997',
            'good' => '#0d6efd',
            'fair' => '#ffc107',
            'poor' => '#dc3545',
        ];
    }
}

