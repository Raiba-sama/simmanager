<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Navigation mois --}}
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

        {{-- Grille calendrier --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800 shadow-sm">
            <table class="w-full min-w-[600px] table-fixed">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <th class="py-2 px-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-[14%]">Lun</th>
                        <th class="py-2 px-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-[14%]">Mar</th>
                        <th class="py-2 px-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-[14%]">Mer</th>
                        <th class="py-2 px-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-[14%]">Jeu</th>
                        <th class="py-2 px-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-[14%]">Ven</th>
                        <th class="py-2 px-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-[14%]">Sam</th>
                        <th class="py-2 px-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-[14%]">Dim</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($weeks as $week)
                    <tr class="border-b border-gray-100 dark:border-gray-700/50 last:border-0">
                        @foreach($week as $cell)
                        <td class="align-top p-2 min-h-[100px] border-r border-gray-100 dark:border-gray-700/50 last:border-r-0">
                            @if($cell === null)
                                <span class="text-gray-200 dark:text-gray-600">—</span>
                            @else
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1">
                                        <span class="text-sm font-medium {{ $cell['isToday'] ? 'bg-primary-500 text-white rounded-full w-7 h-7 flex items-center justify-center' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ $cell['day'] }}
                                        </span>
                                    </div>
                                    @foreach($cell['missions'] as $mission)
                                    <a href="{{ \App\Filament\Resources\MissionResource::getUrl('edit', ['record' => $mission]) }}" 
                                       class="block text-xs rounded px-2 py-1 truncate {{ $mission->status === 'completed' ? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' : ($mission->status === 'in_progress' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200' : 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300') }}"
                                       title="{{ $mission->title }} — {{ $mission->agency?->code ?? '-' }}">
                                        {{ \Illuminate\Support\Str::limit($mission->title, 18) }}
                                    </a>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
