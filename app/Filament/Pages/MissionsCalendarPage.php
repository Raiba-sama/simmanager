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
        $start = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();
        $daysInMonth = $start->daysInMonth;

        $missions = Mission::with('agency')
            ->where('start_date', '<=', $end)
            ->where(function ($query) use ($start) {
                $query->where('end_date', '>=', $start)->orWhereNull('end_date');
            })
            ->orderBy('start_date')
            ->get();

        $days = range(1, $daysInMonth);

        $rows = [];
        foreach ($missions as $mission) {
            $missionStart = Carbon::parse($mission->start_date);
            $missionEnd = $mission->end_date ? Carbon::parse($mission->end_date) : $missionStart;

            if ($missionStart->lt($start)) {
                $missionStart = $start->copy();
            }
            if ($missionEnd->gt($end)) {
                $missionEnd = $end->copy();
            }

            $startDay = (int) $missionStart->format('j');
            $endDay = (int) $missionEnd->format('j');
            $durationDays = $endDay - $startDay + 1;

            $leftPercent = (($startDay - 1) / $daysInMonth) * 100;
            $widthPercent = ($durationDays / $daysInMonth) * 100;

            $rows[] = [
                'mission' => $mission,
                'left_percent' => round($leftPercent, 2),
                'width_percent' => round($widthPercent, 2),
                'start_label' => $missionStart->format('d/m'),
                'end_label' => $missionEnd->format('d/m'),
            ];
        }

        return [
            'monthName' => $start->translatedFormat('F Y'),
            'daysInMonth' => $daysInMonth,
            'days' => $days,
            'rows' => $rows,
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
