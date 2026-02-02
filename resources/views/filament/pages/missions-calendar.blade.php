<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Navigation année --}}
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $year }}</h2>
            <div class="flex gap-2">
                <x-filament::button wire:click="goPrevYear" size="sm" color="gray">
                    <x-heroicon-o-chevron-left class="w-4 h-4" />
                    Année précédente
                </x-filament::button>
                <x-filament::button wire:click="goNextYear" size="sm" color="gray">
                    Année suivante
                    <x-heroicon-o-chevron-right class="w-4 h-4" />
                </x-filament::button>
            </div>
        </div>

        {{-- Partie supérieure : barre des mois (style timeline) --}}
        <div class="rounded-xl overflow-hidden shadow-sm">
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-700 dark:to-primary-800 h-12 flex">
                @foreach($months as $month)
                <div class="flex-1 flex items-center justify-center text-white text-sm font-medium border-r border-white/20 last:border-r-0">
                    {{ $month }}
                </div>
                @endforeach
            </div>
            <div class="h-1 bg-primary-500/30 dark:bg-primary-600/30"></div>
            <div class="flex gap-6 px-2 py-2 bg-gray-50 dark:bg-gray-900/30 border border-t-0 border-gray-200 dark:border-gray-700 rounded-b-xl text-xs text-gray-600 dark:text-gray-400">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-primary-500"></span> Planifiée</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-amber-500"></span> En cours</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-gray-500"></span> Terminée</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-gray-400 opacity-60"></span> Annulée</span>
            </div>
        </div>

        {{-- Partie inférieure : Gantt – missions avec barres --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px]">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase w-64">Mission / Agence</th>
                            <th class="py-3 px-0 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Planning</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                        @php
                            $m = $row['mission'];
                            $barColor = match($m->status) {
                                'completed' => 'bg-gray-500 dark:bg-gray-600',
                                'in_progress' => 'bg-amber-500 dark:bg-amber-600',
                                'cancelled' => 'bg-gray-400 dark:bg-gray-500 opacity-60',
                                default => 'bg-primary-500 dark:bg-primary-600',
                            };
                        @endphp
                        <tr class="border-b border-gray-100 dark:border-gray-700/50 last:border-0 hover:bg-gray-50/50 dark:hover:bg-gray-700/20">
                            <td class="py-3 px-4 align-middle">
                                <a href="{{ \App\Filament\Resources\MissionResource::getUrl('edit', ['record' => $m]) }}" class="block group">
                                    <span class="font-medium text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                        {{ \Illuminate\Support\Str::limit($m->title, 35) }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block mt-0.5">
                                        {{ $m->agency ? $m->agency->code . ' – ' . \Illuminate\Support\Str::limit($m->agency->name, 20) : '—' }}
                                        · {{ \App\Models\Mission::TYPES[$m->type] ?? $m->type }}
                                    </span>
                                </a>
                            </td>
                            <td class="py-3 px-2 align-middle">
                                <div class="relative h-9 flex items-center" style="min-width: 600px;">
                                    <div class="absolute inset-0 flex">
                                        @foreach($months as $i => $monthLabel)
                                        <div class="flex-1 border-r border-gray-100 dark:border-gray-700 last:border-r-0"></div>
                                        @endforeach
                                    </div>
                                    <div
                                        class="absolute h-7 rounded {{ $barColor }} flex items-center justify-center text-white text-xs font-medium shadow-sm"
                                        style="left: {{ $row['left_percent'] }}%; width: {{ max($row['width_percent'], 2) }}%; min-width: 24px;"
                                        title="{{ $row['start_label'] }} – {{ $row['end_label'] }}"
                                    >
                                        @if($row['width_percent'] >= 8)
                                        <span class="truncate px-1">{{ $row['start_label'] }} – {{ $row['end_label'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                Aucune mission sur cette année.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
