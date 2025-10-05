<?php

namespace App\Helpers;

use Carbon\Carbon;
use DateTime;
use DateTimeImmutable;

class CalendarHelper
{
    public static function generateCalendarDays($period)
    {
        $now = new DateTimeImmutable('first day of this month');
        $min = $now->modify('-12 months');
        $max = $now->modify('+12 months');

        $requested = DateTimeImmutable::createFromFormat('Y-m', $period) ?: $now;

        if ($requested < $min || $requested > $max) {
            $requested = $now;
        }

        $startOfMonth = new DateTime($requested->format('Y-m-01'));
        $targetMonth = (int)$startOfMonth->format('m');

        $dayOfWeek = (int)$startOfMonth->format('w');
        $startDate = clone $startOfMonth;
        $startDate->modify("-$dayOfWeek days");

        $calendarDays = [];
        $today = (new DateTime())->format('Y-m-d');

        for ($i = 0; $i < 42; $i++) {
            $currentDate = clone $startDate;
            $dateYmd = $currentDate->format('Y-m-d');
            $dateFormatted = $currentDate->format('F j, Y');

            $calendarDays[] = [
                'date'           => $dateYmd,
                'dateFormatted'  => $dateFormatted,
                'day'            => (int)$currentDate->format('j'),
                'isCurrentMonth' => ((int)$currentDate->format('m') === $targetMonth),
                'isToday'        => ($dateYmd === $today)
            ];

            $startDate->modify('+1 day');
        }

        return $calendarDays;
    }

    public static function generatePeriods()
    {
        $now = Carbon::now()->startOfMonth();
        $start = $now->copy()->subMonths(12);
        $end = $now->copy()->addMonths(12);

        $periods = [];

        while ($start <= $end) {
            $period = $start->format('Y-m');

            $periods[] = [
                'period'           => $period,
                'label'            => $start->format('F Y'),
                'isCurrentPeriod'  => $period === $now->format('Y-m'),
            ];

            $start->addMonth();
        }

        return $periods;
    }
}
