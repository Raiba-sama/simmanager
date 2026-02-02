<?php

namespace App\Filament\Pages;

use App\Models\Mission;
use Carbon\Carbon;
use Filament\Pages\Page;

class MissionsCalendarPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.pages.missions-calendar';

    protected static ?string $navigationLabel = 'Calendrier des missions';

    protected static ?string $title = 'Calendrier des missions';

    protected static ?string $navigationGroup = 'Gestion';

    protected static ?int $navigationSort = 6;

    public ?int $calendarYear = null;

    public ?int $calendarMonth = null;

    public function mount(): void
    {
        if ($this->calendarYear === null) {
            $this->calendarYear = (int) now()->format('Y');
        }
        if ($this->calendarMonth === null) {
            $this->calendarMonth = (int) now()->format('n');
        }
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function getViewData(): array
    {
        $start = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $daysInMonth = $start->daysInMonth;
        $firstDayOfWeek = (int) $start->format('N'); // 1 = Monday

        // Missions qui chevauchent le mois
        $missions = Mission::with('agency')
            ->where('start_date', '<=', $end)
            ->where(function ($query) use ($start) {
                $query->where('end_date', '>=', $start)->orWhereNull('end_date');
            })
            ->orderBy('start_date')
            ->get();

        $missionsByDay = [];
        foreach ($missions as $mission) {
            $missionStart = Carbon::parse($mission->start_date);
            $missionEnd = $mission->end_date ? Carbon::parse($mission->end_date) : $missionStart;
            $day = $missionStart->copy();
            while ($day->lte($missionEnd)) {
                if ($day->month === $this->calendarMonth && $day->year === $this->calendarYear) {
                    $key = $day->format('Y-m-d');
                    if (!isset($missionsByDay[$key])) {
                        $missionsByDay[$key] = [];
                    }
                    $missionsByDay[$key][] = $mission;
                }
                $day->addDay();
            }
        }

        $weeks = [];
        $week = [];
        // Jours vides avant le 1er
        for ($i = 1; $i < $firstDayOfWeek; $i++) {
            $week[] = null;
        }
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, $d);
            $key = $date->format('Y-m-d');
            $dayMissions = collect($missionsByDay[$key] ?? [])->unique('id')->values()->all();
            $week[] = [
                'day' => $d,
                'date' => $date,
                'missions' => $dayMissions,
                'isToday' => $date->isToday(),
            ];
            if (count($week) === 7) {
                $weeks[] = $week;
                $week = [];
            }
        }
        if (!empty($week)) {
            while (count($week) < 7) {
                $week[] = null;
            }
            $weeks[] = $week;
        }

        $prevMonth = $start->copy()->subMonth();
        $nextMonth = $start->copy()->addMonth();

        return [
            'weeks' => $weeks,
            'monthName' => $start->translatedFormat('F Y'),
            'prevYear' => $prevMonth->year,
            'prevMonth' => $prevMonth->month,
            'nextYear' => $nextMonth->year,
            'nextMonth' => $nextMonth->month,
        ];
    }

    public function goPrevMonth(): void
    {
        if ($this->calendarMonth <= 1) {
            $this->calendarMonth = 12;
            $this->calendarYear--;
        } else {
            $this->calendarMonth--;
        }
    }

    public function goNextMonth(): void
    {
        if ($this->calendarMonth >= 12) {
            $this->calendarMonth = 1;
            $this->calendarYear++;
        } else {
            $this->calendarMonth++;
        }
    }
}
