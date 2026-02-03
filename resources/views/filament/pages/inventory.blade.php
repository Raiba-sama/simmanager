<x-filament-panels::page>
    <div class="space-y-6">
        @php
            $stats = $this->getFilteredStats();
        @endphp

        <p class="text-sm text-gray-500 dark:text-gray-400">
            Les chiffres ci-dessous correspondent aux équipements affichés dans le tableau (filtres appliqués).
        </p>

        {{-- Ligne 1 : 4 KPIs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total équipements</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-primary-100 dark:bg-primary-900/30">
                        <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Disponibles</p>
                        <p class="text-2xl font-bold text-success-600 dark:text-success-400">{{ $stats['available'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-success-100 dark:bg-success-900/30">
                        <x-filament::icon icon="heroicon-o-check-circle" class="w-6 h-6 text-success-600 dark:text-success-400" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Attribués</p>
                        <p class="text-2xl font-bold text-info-600 dark:text-info-400">{{ $stats['assigned'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-info-100 dark:bg-info-900/30">
                        <x-filament::icon icon="heroicon-o-user-plus" class="w-6 h-6 text-info-600 dark:text-info-400" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valeur totale</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_value'], 0, ',', ' ') }} XOF</p>
                    </div>
                    <div class="p-3 rounded-full bg-gray-100 dark:bg-gray-700">
                        <x-filament::icon icon="heroicon-o-banknotes" class="w-6 h-6 text-gray-600 dark:text-gray-300" />
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- Ligne 2 : 4 KPIs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">En maintenance</p>
                        <p class="text-xl font-bold text-warning-600 dark:text-warning-400">{{ $stats['maintenance'] }}</p>
                    </div>
                    <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-8 h-8 text-warning-500 dark:text-warning-400" />
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Retirés / Perdus / Endommagés</p>
                        <p class="text-xl font-bold text-danger-600 dark:text-danger-400">{{ $stats['retired'] }}</p>
                    </div>
                    <x-filament::icon icon="heroicon-o-archive-box" class="w-8 h-8 text-danger-500 dark:text-danger-400" />
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Garantie expirant sous 3 mois</p>
                        <p class="text-xl font-bold text-warning-600 dark:text-warning-400">{{ $stats['warranty_expiring'] }}</p>
                    </div>
                    <x-filament::icon icon="heroicon-o-calendar-days" class="w-8 h-8 text-warning-500 dark:text-warning-400" />
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Types utilisés</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['types_count'] ?? 0 }}</p>
                    </div>
                    <x-filament::icon icon="heroicon-o-squares-2x2" class="w-8 h-8 text-gray-500 dark:text-gray-400" />
                </div>
            </x-filament::section>
        </div>

        {{-- Répartition par Zone : grille 4 colonnes --}}
        @php $byZone = $this->getStatsByZone(); @endphp
        @if($byZone->isNotEmpty())
            <x-filament::section>
                <x-slot name="heading">
                    Répartition par zone
                </x-slot>
                <x-slot name="description">
                    Équipements actuellement attribués à une agence de chaque zone.
                </x-slot>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach($byZone as $row)
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-600 px-4 py-3">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $row['name'] }}</p>
                                @if($row['code'])
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $row['code'] }}</p>
                                @endif
                            </div>
                            <span class="text-lg font-bold text-primary-600 dark:text-primary-400">{{ $row['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        {{-- Répartition par Agence : grille 4 colonnes --}}
        @php $byAgency = $this->getStatsByAgency(); @endphp
        @if($byAgency->isNotEmpty())
            <x-filament::section>
                <x-slot name="heading">
                    Répartition par agence
                </x-slot>
                <x-slot name="description">
                    Équipements actuellement attribués à chaque agence.
                </x-slot>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach($byAgency as $row)
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-600 px-4 py-3">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $row['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $row['zone_name'] ? "Zone {$row['zone_name']}" : ($row['code'] ?? '') }}
                                </p>
                            </div>
                            <span class="text-lg font-bold text-primary-600 dark:text-primary-400">{{ $row['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        {{-- Répartition par type d'équipement : grille 4 colonnes --}}
        @php $byType = $this->getStatsByType(); @endphp
        @if($byType->isNotEmpty())
            <x-filament::section>
                <x-slot name="heading">
                    Répartition par type d'équipement
                </x-slot>
                <x-slot name="description">
                    Nombre d'équipements par type (tout statut confondu).
                </x-slot>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach($byType as $row)
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-600 px-4 py-3">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $row['name'] }}</p>
                            <span class="text-lg font-bold text-primary-600 dark:text-primary-400">{{ $row['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        {{-- Tableau --}}
        <x-filament::section>
            <x-slot name="heading">
                Liste des équipements
            </x-slot>
            {{ $this->table }}
        </x-filament::section>
    </div>
</x-filament-panels::page>
