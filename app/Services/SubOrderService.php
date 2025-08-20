<?php

namespace App\Services;

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
                'order_status_id' => 2,
                'order_id' => $order->id,
                'driver_id' => $order->driver_id
            ]);
        }
    }

}
