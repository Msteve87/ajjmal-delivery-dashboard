<?php

namespace App\Listeners;

use App\Events\DriverAcceptedOrder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogDriverAcceptedOrder
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DriverAcceptedOrder $event): void
    {
        activity('قبول الطلبية')
            ->performedOn($event->subOrder)
            ->causedBy($event->driver)
            ->event('order_accepted')
            ->withProperties([
                'driver_id' => $event->driver->id,
                'tracking_id' => $event->subOrder->tracking_id,
            ])
            ->log("قام السائق {$event->driver->first_name} {$event->driver->last_name} بقبول الطلبية #{$event->subOrder->tracking_id}");
    }
}
