<?php

namespace Application\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        //
    ];

    /**
     * @param  Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        //
    }
}
