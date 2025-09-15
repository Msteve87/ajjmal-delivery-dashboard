<?php

namespace App\Services;

use App\Enums\JmOrderStatus;
use App\Models\SubOrder;
use App\Models\SubOrderStatus;
use Illuminate\Support\Facades\DB;

class SubOrderService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected AjjmalMarketApiService $ajjmalMarketApiService
    ) {
        //
    }

    public function storeSubOrder($parentOrder, $subOrder, $driverId)
    {
        return SubOrder::create([
            'total' => $subOrder['total_paid'],
            'tracking_id' => $subOrder['id_order'],
            'base_price' => $subOrder['total_paid'] - $subOrder['total_shipping'],
            'shipping_price' => $subOrder['total_shipping'],
            'products' => $subOrder['products'],
            'sub_order_status_id' => JmOrderStatus::processingInProgress->value,
            'order_id' => $parentOrder->id,
            'date_add' => $subOrder['date_add'],
            'date_upd' => $subOrder['date_upd'],
            'driver_id' => $driverId
        ]);
    }

    public function storeSubOrders($order, $subOrders)
    {
        foreach ($subOrders as $subOrder) {
            SubOrder::create([
                'total' => $subOrder['total_paid'],
                'tracking_id' => $subOrder['id_order'],
                'base_price' => $subOrder['total_paid'] - $subOrder['total_shipping'],
                'shipping_price' => $subOrder['total_shipping'],
                'total_discounts' => $subOrder['total_discounts'],
                'products' => $subOrder['products'],
                'sub_order_status_id' => JmOrderStatus::processingInProgress->value,
                'order_id' => $order->id,
                'date_add' => $subOrder['date_add'],
                'date_upd' => $subOrder['date_upd'],
                'driver_id' => $order->driver_id
            ]);
        }
    }

    public function updateSubOrders($order, $subOrders)
    {
        foreach ($subOrders as $subOrder) {
            SubOrder::where('tracking_id', $subOrder['id_order'])->first()
                ->update([
                    'total' => $subOrder['total_paid'],
                    'tracking_id' => $subOrder['id_order'],
                    'base_price' => $subOrder['total_paid'] - $subOrder['total_shipping'],
                    'shipping_price' => $subOrder['total_shipping'],
                    'total_discounts' => $subOrder['total_discounts'],
                    'products' => $subOrder['products'],
                    'sub_order_status_id' => SubOrderStatus::where('name', $subOrder['current_state_name'])->first()->id,
                    'order_id' => $order->id,
                    'date_add' => $subOrder['date_add'],
                    'date_upd' => $subOrder['date_upd'],
                    'driver_id' => $order->driver_id
                ]);
        }
    }

    public function getSubOrderByTrackingId(string $trackingId)
    {
        return $this->ajjmalMarketApiService->getJmOrderById($trackingId);
    }

    public function getUnassignedSubOrders()
    {
        return SubOrder::query()
            ->whereNull('driver_id')
            ->with('order')
            ->get();
    }
}
