<?php

namespace App\Listeners;

use App\Models\Order;
use App\Services\OrderService;
use App\Events\JmOrderStatusUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Resources\Api\v1\ProductResource;

class UpdateJmOrderStatus
{
    /**
     * Create the event listener.
     */
    public function __construct(protected OrderService $orderService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(JmOrderStatusUpdated $event)
    {
        $parentOrder = Order::whereJsonContains('products', ['jm_order_id' => (int) $event->jmOrderId])->first();

        $items = $this->orderService->getJmOrderByReference($parentOrder->reference);

        $subOrders = (new ProductResource($items))->resolve();

        $statuses = collect($subOrders['products_by_seller'])->pluck('current_state')->toArray();
        // TODO: check sub orders stauts
    }
}
