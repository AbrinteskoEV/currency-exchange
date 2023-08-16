<?php

namespace Application\Console;

use Application\Console\Commands\BinanceCommonDataRefreshingCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        BinanceCommonDataRefreshingCommand::class,
    ];

    /**
     * @param  Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('refresh_binance_common_data')
            ->runInBackground()
            ->dailyAt('01:00')
            ->name('refresh_binance_common_data')
            ->withoutOverlapping();
    }
}
