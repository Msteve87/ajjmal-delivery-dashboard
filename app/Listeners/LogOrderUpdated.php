<?php

namespace App\Listeners;

use App\Events\OrderUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogOrderUpdated
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
    public function handle(OrderUpdated $event): void
    {
        activity(__('activitylogs.names.order_updated'))
            ->performedOn($event->subOrder)
            ->causedBy($event->user)
            ->log("قام المستخدم {$event->user->name} بتحديث حالة الطلبية #{$event->subOrder->tracking_id}");
    }
}
