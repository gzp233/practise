<?php

namespace Modules\Label\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Label\Events\CalculateIsReady;

class InoviceAndArrivalWasImported
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CalculateIsReady $event)
    {
        $event->calculate();
    }
}
