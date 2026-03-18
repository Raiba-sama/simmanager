<x-filament-panels::page>
    @php
        $stats = $this->getFilteredStats();
        $statusLabels = \App\Filament\Pages\InventoryPage::statusLabels();
        $statusColors = \App\Filament\Pages\InventoryPage::statusColors();
        $conditionLabels = \App\Filament\Pages\InventoryPage::conditionLabels();
        $conditionColors = \App\Filament\Pages\InventoryPage::conditionColors();
    @endphp

    <style>
        .inv-progress { height: 6px; border-radius: 3px; background: #e5e7eb; overflow: hidden; }
        .inv-progress-bar { height: 100%; border-radius: 3px; transition: width .4s ease; }
        .inv-legend { display: inline-flex; align-items: center; gap: 4px; font-size: 0.75rem; color: #64748b; }
        .inv-legend-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
        .inv-detail-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 0.75rem; }
        .inv-detail-card { border: 1px solid #e5e7eb; border-radius: 10px; padding: 0.85rem 1rem; background: #fff; transition: box-shadow .2s; }
        .inv-detail-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .inv-detail-card .inv-title { font-weight: 600; font-size: 0.92rem; color: #1e293b; margin-bottom: 0.15rem; }
        .inv-detail-card .inv-subtitle { font-size: 0.75rem; color: #94a3b8; }
        .inv-detail-card .inv-big { font-size: 1.35rem; font-weight: 700; color: #0f172a; }
        .inv-mini-table { width: 100%; font-size: 0.78rem; border-collapse: collapse; margin-top: 0.4rem; }
        .inv-mini-table td { padding: 2px 4px; color: #475569; }
        .inv-mini-table td:last-child { text-align: right; font-weight: 600; color: #1e293b; }
        .inv-tag { display: inline-block; padding: 1px 7px; border-radius: 4px; font-size: 0.7rem; font-weight: 500; }

        .dark .inv-detail-card { background: rgb(30 41 59); border-color: rgb(51 65 85); }
        .dark .inv-detail-card .inv-title { color: #e2e8f0; }
        .dark .inv-detail-card .inv-subtitle { color: #64748b; }
        .dark .inv-detail-card .inv-big { color: #f1f5f9; }
        .dark .inv-mini-table td { color: #94a3b8; }
        .dark .inv-mini-table td:last-child { color: #e2e8f0; }
        .dark .inv-progress { background: #334155; }
    </style>

    <div class="space-y-6">

        <p class="text-sm text-gray-500 dark:text-gray-400">
            Les chiffres ci-dessous correspondent aux équipements affichés dans le tableau (filtres appliqués).
        </p>

        {{-- ── KPI Cards ── --}}
        <x-filament::grid :default="2" :lg="4" class="fi-wi gap-6">
            <x-filament::grid.column>
                <x-filament::section class="!p-4">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total équipements</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                        </div>
                        <div class="p-2 rounded-full bg-primary-100 dark:bg-primary-900/30 shrink-0">
                            <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                        </div>
                    </div>
                    @if($stats['total'] > 0)
                    <div class="inv-progress mt-2">
                        <div class="inv-progress-bar" style="width:{{ round($stats['available'] / $stats['total'] * 100) }}%; background:{{ $statusColors['available'] }};"></div>
                    </div>
                    <div class="flex gap-3 mt-1.5 flex-wrap">
                        <span class="inv-legend"><span class="inv-legend-dot" style="background:{{ $statusColors['available'] }}"></span>Libres {{ $stats['available'] }}</span>
                        <span class="inv-legend"><span class="inv-legend-dot" style="background:{{ $statusColors['assigned'] }}"></span>Attribués {{ $stats['assigned'] }}</span>
                        @if($stats['maintenance'] > 0)<span class="inv-legend"><span class="inv-legend-dot" style="background:{{ $statusColors['maintenance'] }}"></span>Maint. {{ $stats['maintenance'] }}</span>@endif
                    </div>
                    @endif
                </x-filament::section>
            </x-filament::grid.column>

            <x-filament::grid.column>
                <x-filament::section class="!p-4">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Disponibles</p>
                            <p class="text-2xl font-bold text-success-600 dark:text-success-400">{{ $stats['available'] }}</p>
                            @if($stats['total'] > 0)
                                <p class="text-xs text-gray-400">{{ round($stats['available'] / $stats['total'] * 100) }}% du parc</p>
                            @endif
                        </div>
                        <div class="p-2 rounded-full bg-success-100 dark:bg-success-900/30 shrink-0">
                            <x-filament::icon icon="heroicon-o-check-circle" class="w-6 h-6 text-success-600 dark:text-success-400" />
                        </div>
                    </div>
                </x-filament::section>
            </x-filament::grid.column>

            <x-filament::grid.column>
                <x-filament::section class="!p-4">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Attribués</p>
                            <p class="text-2xl font-bold text-info-600 dark:text-info-400">{{ $stats['assigned'] }}</p>
                            @if($stats['total'] > 0)
                                <p class="text-xs text-gray-400">{{ round($stats['assigned'] / $stats['total'] * 100) }}% du parc</p>
                            @endif
                        </div>
                        <div class="p-2 rounded-full bg-info-100 dark:bg-info-900/30 shrink-0">
                            <x-filament::icon icon="heroicon-o-user-plus" class="w-6 h-6 text-info-600 dark:text-info-400" />
                        </div>
                    </div>
                </x-filament::section>
            </x-filament::grid.column>

            <x-filament::grid.column>
                <x-filament::section class="!p-4">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valeur totale</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_value'], 0, ',', ' ') }} XOF</p>
                        </div>
                        <div class="p-2 rounded-full bg-gray-100 dark:bg-gray-700 shrink-0">
                            <x-filament::icon icon="heroicon-o-banknotes" class="w-6 h-6 text-gray-600 dark:text-gray-300" />
                        </div>
                    </div>
                </x-filament::section>
            </x-filament::grid.column>

            <x-filament::grid.column>
                <x-filament::section class="!p-4">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">En maintenance</p>
                            <p class="text-xl font-bold text-warning-600 dark:text-warning-400">{{ $stats['maintenance'] }}</p>
                        </div>
                        <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-8 h-8 text-warning-500 dark:text-warning-400 shrink-0" />
                    </div>
                </x-filament::section>
            </x-filament::grid.column>

            <x-filament::grid.column>
                <x-filament::section class="!p-4">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Retirés / Perdus / Endommagés</p>
                            <p class="text-xl font-bold text-danger-600 dark:text-danger-400">{{ $stats['retired'] }}</p>
                        </div>
                        <x-filament::icon icon="heroicon-o-archive-box" class="w-8 h-8 text-danger-500 dark:text-danger-400 shrink-0" />
                    </div>
                </x-filament::section>
            </x-filament::grid.column>

            <x-filament::grid.column>
                <x-filament::section class="!p-4">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Garantie expirant sous 3 mois</p>
                            <p class="text-xl font-bold text-warning-600 dark:text-warning-400">{{ $stats['warranty_expiring'] }}</p>
                        </div>
                        <x-filament::icon icon="heroicon-o-calendar-days" class="w-8 h-8 text-warning-500 dark:text-warning-400 shrink-0" />
                    </div>
                </x-filament::section>
            </x-filament::grid.column>

            <x-filament::grid.column>
                <x-filament::section class="!p-4">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Types utilisés</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['types_count'] ?? 0 }}</p>
                        </div>
                        <x-filament::icon icon="heroicon-o-squares-2x2" class="w-8 h-8 text-gray-500 dark:text-gray-400 shrink-0" />
                    </div>
                </x-filament::section>
            </x-filament::grid.column>

            @if(($stats['no_sn'] ?? 0) > 0)
            <x-filament::grid.column>
                <x-filament::section class="!p-4">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Sans SN / Tag</p>
                            <p class="text-xl font-bold text-warning-600 dark:text-warning-400">{{ $stats['no_sn'] }}</p>
                            <p class="text-xs text-gray-400">À compléter (ajout en masse)</p>
                        </div>
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-8 h-8 text-warning-500 dark:text-warning-400 shrink-0" />
                    </div>
                </x-filament::section>
            </x-filament::grid.column>
            @endif
        </x-filament::grid>

        {{-- ══════════════════════════════════════════
             DÉTAIL PAR TYPE D'ÉQUIPEMENT
             ══════════════════════════════════════════ --}}
        @php $byType = $this->getStatsByType(); @endphp
        @if($byType->isNotEmpty())
            <x-filament::section collapsible>
                <x-slot name="heading">
                    <x-filament::icon icon="heroicon-o-squares-2x2" class="w-5 h-5 inline-block -mt-0.5 me-1" />
                    Détail par type d'équipement
                </x-slot>
                <x-slot name="description">
                    Pour chaque type : nombre total, répartition libre/attribué, condition, marques principales et valeur.
                </x-slot>

                <div class="inv-detail-grid">
                    @foreach($byType as $row)
                        <div class="inv-detail-card">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div>
                                    <div class="inv-title">{{ $row['name'] }}</div>
                                    @if($row['no_sn'] > 0)
                                        <span class="inv-tag" style="background:#fef3c7; color:#92400e;">{{ $row['no_sn'] }} sans SN</span>
                                    @endif
                                </div>
                                <div class="inv-big">{{ $row['count'] }}</div>
                            </div>

                            {{-- Barre de statut --}}
                            @if($row['count'] > 0)
                            <div class="inv-progress" style="height:8px; display:flex; overflow:hidden;">
                                @foreach($row['by_status'] as $s => $c)
                                    <div style="width:{{ round($c / $row['count'] * 100, 1) }}%; background:{{ $statusColors[$s] ?? '#94a3b8' }};"></div>
                                @endforeach
                            </div>
                            <div class="flex gap-2 mt-1 flex-wrap">
                                @foreach($row['by_status'] as $s => $c)
                                    <span class="inv-legend">
                                        <span class="inv-legend-dot" style="background:{{ $statusColors[$s] ?? '#94a3b8' }}"></span>
                                        {{ $statusLabels[$s] ?? $s }} {{ $c }}
                                    </span>
                                @endforeach
                            </div>
                            @endif

                            {{-- Condition --}}
                            @if(!empty($row['by_condition']))
                            <table class="inv-mini-table">
                                <tr><td colspan="2" style="font-weight:600; padding-top:6px; color:#64748b; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.5px;">Condition</td></tr>
                                @foreach($row['by_condition'] as $cond => $c)
                                <tr>
                                    <td>
                                        <span class="inv-legend-dot" style="background:{{ $conditionColors[$cond] ?? '#94a3b8' }}; display:inline-block; margin-right:4px;"></span>
                                        {{ $conditionLabels[$cond] ?? $cond }}
                                    </td>
                                    <td>{{ $c }}</td>
                                </tr>
                                @endforeach
                            </table>
                            @endif

                            {{-- Marques --}}
                            @if(!empty($row['brands']))
                            <table class="inv-mini-table">
                                <tr><td colspan="2" style="font-weight:600; padding-top:6px; color:#64748b; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.5px;">Marques</td></tr>
                                @foreach($row['brands'] as $brand => $c)
                                <tr><td>{{ $brand }}</td><td>{{ $c }}</td></tr>
                                @endforeach
                            </table>
                            @endif

                            {{-- Valeur --}}
                            @if($row['value'] > 0)
                            <div style="margin-top:6px; font-size:0.75rem; color:#64748b;">
                                Valeur : <strong style="color:#0f172a;">{{ number_format($row['value'], 0, ',', ' ') }} XOF</strong>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        {{-- ══════════════════════════════════════════
             DÉTAIL PAR MARQUE
             ══════════════════════════════════════════ --}}
        @php $byBrand = $this->getStatsByBrand(); @endphp
        @if($byBrand->isNotEmpty())
            <x-filament::section collapsible collapsed>
                <x-slot name="heading">
                    <x-filament::icon icon="heroicon-o-tag" class="w-5 h-5 inline-block -mt-0.5 me-1" />
                    Détail par marque
                </x-slot>
                <x-slot name="description">
                    Les 15 marques les plus représentées avec répartition statut, types et valeur.
                </x-slot>

                <div class="inv-detail-grid">
                    @foreach($byBrand as $row)
                        <div class="inv-detail-card">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="inv-title">{{ $row['brand'] }}</div>
                                <div class="inv-big">{{ $row['count'] }}</div>
                            </div>

                            @if($row['count'] > 0)
                            <div class="inv-progress" style="height:8px; display:flex; overflow:hidden;">
                                @foreach($row['by_status'] as $s => $c)
                                    <div style="width:{{ round($c / $row['count'] * 100, 1) }}%; background:{{ $statusColors[$s] ?? '#94a3b8' }};"></div>
                                @endforeach
                            </div>
                            <div class="flex gap-2 mt-1 flex-wrap">
                                @foreach($row['by_status'] as $s => $c)
                                    <span class="inv-legend">
                                        <span class="inv-legend-dot" style="background:{{ $statusColors[$s] ?? '#94a3b8' }}"></span>
                                        {{ $statusLabels[$s] ?? $s }} {{ $c }}
                                    </span>
                                @endforeach
                            </div>
                            @endif

                            @if(!empty($row['types']))
                            <table class="inv-mini-table">
                                <tr><td colspan="2" style="font-weight:600; padding-top:6px; color:#64748b; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.5px;">Types</td></tr>
                                @foreach($row['types'] as $typeName => $c)
                                <tr><td>{{ $typeName }}</td><td>{{ $c }}</td></tr>
                                @endforeach
                            </table>
                            @endif

                            @if($row['value'] > 0)
                            <div style="margin-top:6px; font-size:0.75rem; color:#64748b;">
                                Valeur : <strong style="color:#0f172a;">{{ number_format($row['value'], 0, ',', ' ') }} XOF</strong>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        {{-- ══════════════════════════════════════════
             DÉTAIL PAR ZONE
             ══════════════════════════════════════════ --}}
        @php $byZone = $this->getStatsByZone(); @endphp
        @if($byZone->isNotEmpty())
            <x-filament::section collapsible collapsed>
                <x-slot name="heading">
                    <x-filament::icon icon="heroicon-o-map" class="w-5 h-5 inline-block -mt-0.5 me-1" />
                    Détail par zone
                </x-slot>
                <x-slot name="description">
                    Équipements attribués par zone, avec répartition statut et valeur.
                </x-slot>

                <div class="inv-detail-grid">
                    @foreach($byZone as $row)
                        <div class="inv-detail-card">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div>
                                    <div class="inv-title">{{ $row['name'] }}</div>
                                    @if($row['code'])<div class="inv-subtitle">{{ $row['code'] }}</div>@endif
                                </div>
                                <div class="inv-big">{{ $row['count'] }}</div>
                            </div>

                            @if($row['count'] > 0)
                            <div class="inv-progress" style="height:8px; display:flex; overflow:hidden;">
                                @foreach($row['by_status'] as $s => $c)
                                    <div style="width:{{ round($c / $row['count'] * 100, 1) }}%; background:{{ $statusColors[$s] ?? '#94a3b8' }};"></div>
                                @endforeach
                            </div>
                            <div class="flex gap-2 mt-1 flex-wrap">
                                @foreach($row['by_status'] as $s => $c)
                                    <span class="inv-legend">
                                        <span class="inv-legend-dot" style="background:{{ $statusColors[$s] ?? '#94a3b8' }}"></span>
                                        {{ $statusLabels[$s] ?? $s }} {{ $c }}
                                    </span>
                                @endforeach
                            </div>
                            @endif

                            @if($row['value'] > 0)
                            <div style="margin-top:6px; font-size:0.75rem; color:#64748b;">
                                Valeur : <strong style="color:#0f172a;">{{ number_format($row['value'], 0, ',', ' ') }} XOF</strong>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        {{-- ══════════════════════════════════════════
             DÉTAIL PAR AGENCE
             ══════════════════════════════════════════ --}}
        @php $byAgency = $this->getStatsByAgency(); @endphp
        @if($byAgency->isNotEmpty())
            <x-filament::section collapsible collapsed>
                <x-slot name="heading">
                    <x-filament::icon icon="heroicon-o-building-office-2" class="w-5 h-5 inline-block -mt-0.5 me-1" />
                    Détail par agence
                </x-slot>
                <x-slot name="description">
                    Équipements attribués par agence, avec répartition statut et valeur.
                </x-slot>

                <div class="inv-detail-grid">
                    @foreach($byAgency as $row)
                        <div class="inv-detail-card">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div>
                                    <div class="inv-title">{{ $row['name'] }}</div>
                                    <div class="inv-subtitle">
                                        {{ $row['zone_name'] ? "Zone {$row['zone_name']}" : ($row['code'] ?? '') }}
                                    </div>
                                </div>
                                <div class="inv-big">{{ $row['count'] }}</div>
                            </div>

                            @if($row['count'] > 0)
                            <div class="inv-progress" style="height:8px; display:flex; overflow:hidden;">
                                @foreach($row['by_status'] as $s => $c)
                                    <div style="width:{{ round($c / $row['count'] * 100, 1) }}%; background:{{ $statusColors[$s] ?? '#94a3b8' }};"></div>
                                @endforeach
                            </div>
                            <div class="flex gap-2 mt-1 flex-wrap">
                                @foreach($row['by_status'] as $s => $c)
                                    <span class="inv-legend">
                                        <span class="inv-legend-dot" style="background:{{ $statusColors[$s] ?? '#94a3b8' }}"></span>
                                        {{ $statusLabels[$s] ?? $s }} {{ $c }}
                                    </span>
                                @endforeach
                            </div>
                            @endif

                            @if($row['value'] > 0)
                            <div style="margin-top:6px; font-size:0.75rem; color:#64748b;">
                                Valeur : <strong style="color:#0f172a;">{{ number_format($row['value'], 0, ',', ' ') }} XOF</strong>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        {{-- ══════════════════════════════════════════
             RÉPARTITION PAR CONDITION (global)
             ══════════════════════════════════════════ --}}
        @php $byCondition = $this->getStatsByCondition(); @endphp
        @if(!empty($byCondition))
            <x-filament::section collapsible collapsed>
                <x-slot name="heading">
                    <x-filament::icon icon="heroicon-o-heart" class="w-5 h-5 inline-block -mt-0.5 me-1" />
                    État / Condition du parc
                </x-slot>
                <x-slot name="description">
                    Répartition globale des équipements selon leur condition physique.
                </x-slot>

                @php $condTotal = array_sum(array_column($byCondition, 'count')); @endphp
                <div class="inv-progress mb-3" style="height:12px; display:flex; overflow:hidden; border-radius:6px;">
                    @foreach($byCondition as $key => $info)
                        <div style="width:{{ round($info['count'] / max($condTotal,1) * 100, 1) }}%; background:{{ $conditionColors[$key] ?? '#94a3b8' }};" title="{{ $info['label'] }} : {{ $info['count'] }}"></div>
                    @endforeach
                </div>
                <div class="flex gap-4 flex-wrap">
                    @foreach($byCondition as $key => $info)
                        <div class="flex items-center gap-2">
                            <span class="inv-legend-dot" style="background:{{ $conditionColors[$key] ?? '#94a3b8' }}; width:10px; height:10px;"></span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $info['label'] }}</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $info['count'] }}</span>
                            <span class="text-xs text-gray-400">({{ round($info['count'] / max($condTotal,1) * 100) }}%)</span>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        {{-- ══════════════════════════════════════════
             TABLEAU COMPLET
             ══════════════════════════════════════════ --}}
        <x-filament::section>
            <x-slot name="heading">
                Liste des équipements
            </x-slot>
            {{ $this->table }}
        </x-filament::section>
    </div>
</x-filament-panels::page>
