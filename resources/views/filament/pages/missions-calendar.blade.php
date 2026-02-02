<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Navigation par mois --}}
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $monthName }}</h2>
            <div class="flex gap-2">
                <x-filament::button wire:click="goPrevMonth" size="sm" color="gray">
                    <x-heroicon-o-chevron-left class="w-4 h-4" />
                    Mois précédent
                </x-filament::button>
                <x-filament::button wire:click="goNextMonth" size="sm" color="gray">
                    Mois suivant
                    <x-heroicon-o-chevron-right class="w-4 h-4" />
                </x-filament::button>
            </div>
        </div>

        {{-- Légende --}}
        <div class="flex gap-6 px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700 text-xs text-gray-600 dark:text-gray-400">
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-primary-500"></span> Planifiée</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-amber-500"></span> En cours</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-gray-500"></span> Terminée</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-gray-400 opacity-60"></span> Annulée</span>
        </div>

        {{-- Timeline : en-têtes = chaque jour du mois --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full" style="min-width: {{ 240 + $daysInMonth * 24 }}px;">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase w-60 sticky left-0 bg-gray-50 dark:bg-gray-900/50 z-10">Mission / Agence</th>
                            @foreach($days as $d)
                            <th class="py-2 px-0.5 text-center text-xs font-medium text-gray-500 dark:text-gray-400 w-6 min-w-6">
                                {{ $d }}
                            </th>
                            @endforeach
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
                            <td class="py-2.5 px-4 align-middle sticky left-0 bg-white dark:bg-gray-800 z-10">
                                <a href="{{ \App\Filament\Resources\MissionResource::getUrl('edit', ['record' => $m]) }}" class="block group">
                                    <span class="font-medium text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 text-sm">
                                        {{ \Illuminate\Support\Str::limit($m->title, 32) }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block mt-0.5">
                                        {{ $m->agency ? $m->agency->code . ' – ' . \Illuminate\Support\Str::limit($m->agency->name, 18) : '—' }}
                                        · {{ \App\Models\Mission::TYPES[$m->type] ?? $m->type }}
                                    </span>
                                </a>
                            </td>
                            <td class="py-2 px-1 align-middle" colspan="{{ $daysInMonth }}">
                                <div class="relative h-8 flex items-center">
                                    {{-- Grille : un segment par jour --}}
                                    <div class="absolute inset-0 flex">
                                        @foreach($days as $d)
                                        <div class="flex-1 min-w-0 border-r border-gray-100 dark:border-gray-700/70 last:border-r-0"></div>
                                        @endforeach
                                    </div>
                                    {{-- Barre mission : de la date de début à la date de fin --}}
                                    <div
                                        class="absolute h-6 rounded {{ $barColor }} flex items-center justify-center text-white text-xs font-medium shadow-sm pointer-events-none"
                                        style="left: {{ $row['left_percent'] }}%; width: {{ max($row['width_percent'], 3) }}%;"
                                        title="{{ $row['start_label'] }} – {{ $row['end_label'] }}"
                                    >
                                        @if($row['width_percent'] >= 12)
                                        <span class="truncate px-1">{{ $row['start_label'] }} – {{ $row['end_label'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 1 }}" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                Aucune mission sur ce mois.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
