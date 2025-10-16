<?php

namespace App\Listeners;

use App\Models\SubOrder;
use App\Events\DriverActionOnOrder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogDriverOrderAction
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
    public function handle(DriverActionOnOrder $event): void
    {
        $subOrder = SubOrder::where('tracking_id', $event->trackingId)->first();

        $messages = [
            'order_accepted' => "قام السائق {$event->driver->first_name} {$event->driver->last_name} بقبول الطلبية #{$subOrder->tracking_id}",
            'status_updated' => "قام السائق {$event->driver->first_name} {$event->driver->last_name} بتحديث حالة الطلبية #{$subOrder->tracking_id} إلى {$subOrder->sub_order_status_id}",
        ];

        $description = $messages[$event->action] ?? "قام السائق {$event->driver->first_name} {$event->driver->last_name}بتنفيذ إجراء على الطلبية #{$subOrder->tracking_id}";

        $actionName = __("activitylogs.names.{$event->action}", [], 'ar');

        activity($actionName)
            ->performedOn($subOrder)
            ->causedBy($event->driver)
            ->event($event->action)
            ->withProperties([
                'driver_id' => $event->driver->id,
                'order_id' => $subOrder->id,
                'action' => $event->action,
            ])
            ->log($description);
    }
}
