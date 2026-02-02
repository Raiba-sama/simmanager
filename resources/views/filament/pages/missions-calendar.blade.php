<x-filament-panels::page>
    <div class="fi-ta-content grid gap-6">
        {{-- En-tête : mois + navigation (style Filament) --}}
        <x-filament::section>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-xl font-semibold tracking-tight dark:text-white">
                    {{ $monthName }}
                </h2>
                <div class="flex gap-2">
                    <x-filament::button wire:click="goPrevMonth" size="sm" color="gray">
                        <x-heroicon-o-chevron-left class="h-4 w-4" />
                        Précédent
                    </x-filament::button>
                    <x-filament::button wire:click="goNextMonth" size="sm" color="gray">
                        Suivant
                        <x-heroicon-o-chevron-right class="h-4 w-4" />
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        {{-- Légende (style Filament) --}}
        <x-filament::section class="py-3">
            <div class="flex flex-wrap gap-6 text-sm text-gray-600 dark:text-gray-400">
                <span class="flex items-center gap-2">
                    <span class="fi-badge relative grid h-4 w-4 rounded bg-primary-500 dark:bg-primary-600"></span>
                    Planifiée
                </span>
                <span class="flex items-center gap-2">
                    <span class="fi-badge relative grid h-4 w-4 rounded bg-amber-500 dark:bg-amber-600"></span>
                    En cours
                </span>
                <span class="flex items-center gap-2">
                    <span class="fi-badge relative grid h-4 w-4 rounded bg-emerald-500 dark:bg-emerald-600"></span>
                    Terminée
                </span>
                <span class="flex items-center gap-2">
                    <span class="fi-badge relative grid h-4 w-4 rounded bg-gray-400 dark:bg-gray-500 opacity-70"></span>
                    Annulée
                </span>
            </div>
        </x-filament::section>

        {{-- Grille timeline (style Filament table) --}}
        <x-filament::section>
            <div class="overflow-x-auto -mx-6 -mb-6">
                <table class="w-full table-fixed fi-ta-table" style="min-width: {{ 260 + $daysInMonth * 30 }}px;">
                    <colgroup>
                        <col style="width: 260px;">
                        @foreach($days as $d)
                        <col style="width: 30px;">
                        @endforeach
                    </colgroup>
                    <thead>
                        <tr class="fi-ta-header-row border-b border-gray-200 dark:border-white/5 bg-gray-50 dark:bg-white/5">
                            <th class="fi-ta-header-cell py-3.5 px-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-950 dark:text-white sticky left-0 z-10 bg-gray-50 dark:bg-white/5 border-r border-gray-200 dark:border-white/5">
                                Mission / Agence
                            </th>
                            @foreach($days as $d)
                            <th class="fi-ta-header-cell py-2.5 px-0 text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                                {{ $d }}
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                        @forelse($rows as $row)
                        @php
                            $m = $row['mission'];
                            $barColor = match($m->status) {
                                'completed' => 'bg-emerald-500 dark:bg-emerald-600 hover:bg-emerald-600 dark:hover:bg-emerald-500',
                                'in_progress' => 'bg-amber-500 dark:bg-amber-600 hover:bg-amber-600 dark:hover:bg-amber-500',
                                'cancelled' => 'bg-gray-400 dark:bg-gray-500 opacity-60',
                                default => 'bg-primary-500 dark:bg-primary-600 hover:bg-primary-600 dark:hover:bg-primary-500',
                            };
                            $startDay = $row['start_day'];
                            $endDay = $row['end_day'];
                            $durationDays = $row['duration_days'];
                        @endphp
                        <tr class="fi-ta-row hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="fi-ta-cell py-3 px-4 sticky left-0 z-10 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-white/5 group">
                                <a href="{{ \App\Filament\Resources\MissionResource::getUrl('edit', ['record' => $m]) }}" class="block">
                                    <span class="font-medium text-gray-950 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 text-sm">
                                        {{ \Illuminate\Support\Str::limit($m->title, 32) }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block mt-0.5">
                                        {{ $m->agency ? $m->agency->code . ' – ' . \Illuminate\Support\Str::limit($m->agency->name, 18) : '—' }}
                                        <span class="text-gray-400 dark:text-gray-500">·</span>
                                        {{ \App\Models\Mission::TYPES[$m->type] ?? $m->type }}
                                    </span>
                                </a>
                            </td>
                            @for($d = 1; $d < $startDay; $d++)
                            <td class="fi-ta-cell p-0.5 align-middle border-r border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5"></td>
                            @endfor
                            <td class="fi-ta-cell p-1 align-middle border-r border-gray-100 dark:border-white/5" colspan="{{ $durationDays }}">
                                <a href="{{ \App\Filament\Resources\MissionResource::getUrl('edit', ['record' => $m]) }}" class="block h-8 rounded-lg {{ $barColor }} flex items-center justify-center text-white text-xs font-medium shadow-sm transition-colors border border-white/10" title="{{ $row['start_label'] }} – {{ $row['end_label'] }}">
                                    <span class="truncate px-2">{{ $row['start_label'] }} – {{ $row['end_label'] }}</span>
                                </a>
                            </td>
                            @for($d = $endDay + 1; $d <= $daysInMonth; $d++)
                            <td class="fi-ta-cell p-0.5 align-middle border-r border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5"></td>
                            @endfor
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 1 }}" class="fi-ta-cell py-12 text-center">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Aucune mission sur ce mois.</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Utilisez les boutons ci-dessus pour changer de mois.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
