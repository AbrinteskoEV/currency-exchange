<?php

namespace Application\Console;

use Application\Console\Commands\Market\Binance\BinanceCommonDataRefreshingCommand;
use Application\Console\Commands\Market\Binance\BinanceUsedApiWeightRefreshingCommand;
use Application\Console\Commands\Market\MarketInfoRefreshingCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        BinanceCommonDataRefreshingCommand::class,
        MarketInfoRefreshingCommand::class,
        BinanceUsedApiWeightRefreshingCommand::class,
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

        $schedule->command('refresh_market_info')
            ->runInBackground()
            ->everyMinute()
            ->name('refresh_market_info')
            ->withoutOverlapping();

        $schedule->command('refresh_binance_used_weight')
            ->runInBackground()
            ->everyMinute()
            ->name('refresh_binance_used_weight')
            ->withoutOverlapping();
    }
}
