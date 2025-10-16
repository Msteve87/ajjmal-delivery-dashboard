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
        activity()
            ->performedOn($event->subOrder)
            ->causedBy($event->driver)
            ->event('accepted_order')
            ->withProperties([
                'driver_id' => $event->driver->id,
                'order_id' => $event->subOrder->id,
            ])
            ->log("قام السائق {$event->driver->first_name} {$event->driver->last_name} بقبول الطلبية #{$event->subOrder->tracking_id}");
    }
}
