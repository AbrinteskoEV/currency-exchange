<?php

namespace Application\Listeners;

use Application\Events\ExampleEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ExampleListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Application\Events\ExampleEvent  $event
     * @return void
     */
    public function handle(ExampleEvent $event)
    {
        //
    }
}
