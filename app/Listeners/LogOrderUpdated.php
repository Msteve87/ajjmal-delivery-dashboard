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
        $messages = [
            'order_update' => "قام المستخدم {$event->user->name} بتحديث حالة الطلبية #{$event->subOrder->tracking_id}",
            'withdraw_order' => "قام المستخدم {$event->user->name} بإلغاء تعين سائق لطلبية#{$event->subOrder->tracking_id}",
        ];


        $description = $messages[$event->action] ?? "قام المستخدم {$event->user->name} بتنفيذ إجراء على الطلبية #{$event->subOrder->tracking_id}";

        $actionName = __("activitylogs.names.{$event->action}", [], 'ar');

        activity($actionName)
            ->performedOn($event->subOrder)
            ->causedBy($event->user)
            ->log($description);
    }
}
