<x-filament-panels::page>
    <div class="space-y-5">
        {{-- En-tête : mois + navigation --}}
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl bg-gradient-to-r from-primary-500/10 to-primary-600/5 dark:from-primary-600/20 dark:to-primary-700/10 border border-primary-200/50 dark:border-primary-800/50 px-5 py-4">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                {{ $monthName }}
            </h2>
            <div class="flex gap-2">
                <x-filament::button wire:click="goPrevMonth" size="sm" color="primary" outlined>
                    <x-heroicon-o-chevron-left class="w-4 h-4" />
                    Précédent
                </x-filament::button>
                <x-filament::button wire:click="goNextMonth" size="sm" color="primary" outlined>
                    Suivant
                    <x-heroicon-o-chevron-right class="w-4 h-4" />
                </x-filament::button>
            </div>
        </div>

        {{-- Légende --}}
        <div class="flex flex-wrap gap-4 px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700">
            <span class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <span class="w-4 h-4 rounded-md bg-primary-500 shadow-sm"></span>
                <span>Planifiée</span>
            </span>
            <span class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <span class="w-4 h-4 rounded-md bg-amber-500 shadow-sm"></span>
                <span>En cours</span>
            </span>
            <span class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <span class="w-4 h-4 rounded-md bg-emerald-600 shadow-sm"></span>
                <span>Terminée</span>
            </span>
            <span class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <span class="w-4 h-4 rounded-md bg-gray-400 dark:bg-gray-500 opacity-70"></span>
                <span>Annulée</span>
            </span>
        </div>

        {{-- Grille timeline --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/5">
            <div class="overflow-x-auto">
                <table class="w-full table-fixed" style="min-width: {{ 260 + $daysInMonth * 30 }}px;">
                    <colgroup>
                        <col style="width: 260px;">
                        @foreach($days as $d)
                        <col style="width: 30px;">
                        @endforeach
                    </colgroup>
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-900/80 border-b-2 border-gray-200 dark:border-gray-700">
                            <th class="py-3.5 px-5 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400 sticky left-0 z-10 bg-gray-100 dark:bg-gray-900/80">
                                Mission / Agence
                            </th>
                            @foreach($days as $d)
                            <th class="py-2.5 px-0 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">
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
                                'completed' => 'bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500',
                                'in_progress' => 'bg-amber-500 hover:bg-amber-600 dark:bg-amber-500 dark:hover:bg-amber-400',
                                'cancelled' => 'bg-gray-400 dark:bg-gray-500 opacity-60',
                                default => 'bg-primary-500 hover:bg-primary-600 dark:bg-primary-600 dark:hover:bg-primary-500',
                            };
                            $startDay = $row['start_day'];
                            $endDay = $row['end_day'];
                            $durationDays = $row['duration_days'];
                        @endphp
                        <tr class="border-b border-gray-100 dark:border-gray-700/60 hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="py-3 px-5 align-middle sticky left-0 z-10 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 group">
                                <a href="{{ \App\Filament\Resources\MissionResource::getUrl('edit', ['record' => $m]) }}" class="block">
                                    <span class="font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 text-sm transition-colors">
                                        {{ \Illuminate\Support\Str::limit($m->title, 32) }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block mt-1">
                                        {{ $m->agency ? $m->agency->code . ' – ' . \Illuminate\Support\Str::limit($m->agency->name, 18) : '—' }}
                                        <span class="text-gray-400 dark:text-gray-500">·</span>
                                        {{ \App\Models\Mission::TYPES[$m->type] ?? $m->type }}
                                    </span>
                                </a>
                            </td>
                            @for($d = 1; $d < $startDay; $d++)
                            <td class="p-0.5 align-middle border-r border-gray-100 dark:border-gray-700/50 bg-gray-50/30 dark:bg-gray-800/30"></td>
                            @endfor
                            <td class="p-1 align-middle border-r border-gray-100 dark:border-gray-700/50" colspan="{{ $durationDays }}">
                                <a href="{{ \App\Filament\Resources\MissionResource::getUrl('edit', ['record' => $m]) }}" class="block h-8 rounded-lg {{ $barColor }} flex items-center justify-center text-white text-xs font-medium shadow-md transition-all hover:shadow-lg {{ $m->status !== 'cancelled' ? 'border border-white/20' : '' }}" title="{{ $row['start_label'] }} – {{ $row['end_label'] }}">
                                    <span class="truncate px-2">{{ $row['start_label'] }} – {{ $row['end_label'] }}</span>
                                </a>
                            </td>
                            @for($d = $endDay + 1; $d <= $daysInMonth; $d++)
                            <td class="p-0.5 align-middle border-r border-gray-100 dark:border-gray-700/50 bg-gray-50/30 dark:bg-gray-800/30"></td>
                            @endfor
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 1 }}" class="py-16 text-center">
                                <p class="text-gray-500 dark:text-gray-400 font-medium">Aucune mission sur ce mois.</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Utilisez les boutons ci-dessus pour changer de mois.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
