<?php

if (config('stelladend.enable_realtime_data_sync')) {
    Schedule::command('app:fetch-latest-market-date')->dailyAt('09:31');

    Schedule::command('app:fetch-latest-market-quotes')
        ->weekdays()
        ->at('06:00');

//    Schedule::command('app:fetch-latest-market-quotes')
//        ->everyFifteenMinutes()
//        ->weekdays()
//        ->when(function () {
//            $now = now();
//            return $now->between(
//                $now->copy()->setTime(9, 0),
//                $now->copy()->setTime(16, 30)
//            );
//        });

    Schedule::command('app:fetch-latest-dividends')
        ->weekdays()
        ->at('10:00');

    Schedule::command('app:fetch-latest-dividends')
        ->weekdays()
        ->at('16:30');

    Schedule::command('app:fetch-latest-fx-rates')
        ->weekdays()
        ->at('18:00');

//    Schedule::command('app:fetch-latest-fx-rates')
//        ->weekdays()
//        ->at('18:00');
}
