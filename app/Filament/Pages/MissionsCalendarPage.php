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

    public function mount(): void
    {
        if ($this->calendarYear === null) {
            $this->calendarYear = (int) now()->format('Y');
        }
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function getViewData(): array
    {
        $yearStart = Carbon::createFromDate($this->calendarYear, 1, 1)->startOfDay();
        $yearEnd = Carbon::createFromDate($this->calendarYear, 12, 31)->endOfDay();
        $daysInYear = $yearStart->diffInDays($yearEnd) + 1;

        $missions = Mission::with('agency')
            ->where('start_date', '<=', $yearEnd)
            ->where(function ($query) use ($yearStart) {
                $query->where('end_date', '>=', $yearStart)->orWhereNull('end_date');
            })
            ->orderBy('start_date')
            ->get();

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[] = Carbon::createFromDate($this->calendarYear, $m, 1)->translatedFormat('M');
        }

        $rows = [];
        foreach ($missions as $mission) {
            $start = Carbon::parse($mission->start_date);
            $end = $mission->end_date ? Carbon::parse($mission->end_date) : $start;
            if ($start->lt($yearStart)) {
                $start = $yearStart->copy();
            }
            if ($end->gt($yearEnd)) {
                $end = $yearEnd->copy();
            }
            $startDayOfYear = $yearStart->copy()->diffInDays($start);
            $durationDays = $start->diffInDays($end) + 1;
            $leftPercent = ($startDayOfYear / $daysInYear) * 100;
            $widthPercent = ($durationDays / $daysInYear) * 100;

            $rows[] = [
                'mission' => $mission,
                'left_percent' => round($leftPercent, 2),
                'width_percent' => round(min($widthPercent, 100 - $leftPercent), 2),
                'start_label' => $start->format('d/m'),
                'end_label' => $end->format('d/m'),
            ];
        }

        return [
            'year' => $this->calendarYear,
            'months' => $months,
            'rows' => $rows,
        ];
    }

    public function goPrevYear(): void
    {
        $this->calendarYear--;
    }

    public function goNextYear(): void
    {
        $this->calendarYear++;
    }
}
