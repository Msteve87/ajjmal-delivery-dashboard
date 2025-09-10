<?php

namespace App\Services;

use App\Enums\JmOrderStatus;
use App\Models\SubOrder;
use Illuminate\Support\Facades\DB;

class SubOrderService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function storeSubOrder($order, $subOrders)
    {
        foreach ($subOrders as $subOrder) {
            SubOrder::create([
                'total' => $subOrder['total_paid'] + $subOrder['total_shipping'],
                'tracking_id' => $subOrder['id_order'],
                'base_price' => $subOrder['total_paid'] - $subOrder['total_shipping'],
                'shipping_price' => $subOrder['total_shipping'],
                'products' => $subOrder['products'],
                'sub_order_status_id' => JmOrderStatus::processingInProgress->value,
                'order_id' => $order->id,
                'date_add' => $subOrder['date_add'],
                'date_upd' => $subOrder['date_upd'],
                'driver_id' => $order->driver_id
            ]);
        }
    }

}
