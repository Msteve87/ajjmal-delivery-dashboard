<?php

namespace App\Services;

use App\Models\SubOrder;
use App\Enums\JmOrderStatus;
use App\Models\SubOrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SubOrderService
{
    /**
     * Create a new class instance.
     */
    protected $apiToken;

    public function __construct(
        protected AjjmalMarketApiService $ajjmalMarketApiService
    ) {
        $this->apiToken = env('JM_API_KEY');
    }

    public function storeSubOrder($parentOrder, $subOrder, $driverId)
    {
        return SubOrder::create([
            'total' => $subOrder['total_paid'],
            'tracking_id' => $subOrder['id_order'],
            'base_price' => $subOrder['total_paid'] - $subOrder['total_shipping'],
            'shipping_price' => $subOrder['total_shipping'],
            'products' => json_encode($subOrder['products']),
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
            SubOrder::upsert(
                [
                    [
                        'total' => $subOrder['total_paid'],
                        'tracking_id' => $subOrder['id_order'],
                        'base_price' => $subOrder['total_paid'] - $subOrder['total_shipping'],
                        'shipping_price' => $subOrder['total_shipping'],
                        'total_discounts' => $subOrder['total_discounts'],
                        'products' => json_encode($subOrder['products']),
                        'sub_order_status_id' => SubOrderStatus::where('name', $subOrder['current_state_name'])->first()->id,
                        'order_id' => $order->id,
                        'date_add' => $subOrder['date_add'],
                        'date_upd' => $subOrder['date_upd'],
                    ]
                ],
                ['tracking_id'],
                [
                    'total',
                    'base_price',
                    'shipping_price',
                    'total_discounts',
                    'products',
                    'sub_order_status_id',
                    'order_id',
                    'date_add',
                    'date_upd',
                ]
            );
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
                    'driver_id' => $order->driver_id,
                    'date_add' => $subOrder['date_add'],
                    'date_upd' => $subOrder['date_upd'],
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
            ->whereIn('sub_order_status_id', [
                JmOrderStatus::processingInProgress->value,
                JmOrderStatus::awaitingCashOnDelivery->value
            ])
            ->whereNull('driver_id')
            ->with('order')
            ->orderByDesc('date_add')
            ->get();
    }

    public function addDiscount(array $data)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken
        ])->post(
                env('JM_BASE_URL') . '/module/orderdiscount/api',
                $data
            );

        if (!$response->json()['success'] ?? false) {
            throw new HttpException(400, 'Failed to add order discount');
        }

        return $response->json()['order_details'];
    }
}
