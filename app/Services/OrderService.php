<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SubOrder;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected SubOrderService $subOrderService,
        protected AjjmalMarketApiService $ajjmalMarketApiService
    ) {
        //
    }

    public function getJmOrdersByStatus($status)
    {
        $params = [
            'state' => $status,
            'order' => 'desc',
            'limit' => '1000'
        ];

        $response = Http::get(env('JM_API_URL_STANDALONE') . '/delivery', $params);

        if ($response->successful()) {
            return $response->json()['data'];
        } else {
            throw new \Exception('Error while fetching JM orders by status');
        }
    }

    public function getJmOrders()
    {
        $processinginProgress = $this->getJmOrdersByStatus('Processing in Progress');

        $awaitingCashOnDeliveryValidation = $this->getJmOrdersByStatus('Awaiting Cash On Delivery validation');

        $awaitingPaymentOnDeliveryValidation = $this->getJmOrdersByStatus('Awaiting Payment On Delivery validation');

        $items = collect($processinginProgress)
            ->merge($awaitingCashOnDeliveryValidation)
            ->merge($awaitingPaymentOnDeliveryValidation);

        $orderReferences = Order::whereHas('orderStatus', function ($query) {
            $query->where('name', '!=', 'pending');
        })->pluck('reference');

        $mergedItems = collect($items)
            ->groupBy('reference')
            ->filter(function ($group, $reference) use ($orderReferences) {
                return !$orderReferences->contains($reference);
            })
            ->map(function ($group) {
                return [
                    'delivery_date' => $group->first()['delivery_date'] ?? null,
                    'start_time' => $group->first()['start_time'] ?? null,
                    'end_time' => $group->first()['end_time'] ?? null,
                    'reference' => $group->first()['reference'],
                    'payment_method' => $group->first()['payment'],
                    'total_paid' => number_format((float) $group->sum('total_paid'), 2, '.', ''),
                    'total_shipping' => number_format((float) $group->max('total_shipping'), 2, '.', ''),
                    'total_discounts' => number_format((float) $group->max('total_discounts'), 2, '.', ''),
                    'status' => 'pending',
                    'status_ar' => 'جديدة',
                    'current_state_name' => $group->first()['current_state_name'],
                    'customer_name' => $group->first()['customer']['firstname'] . ' ' . $group->first()['customer']['lastname'],
                    'address' => $group->first()['customer']['address'],
                    'customer_phone' => $group->first()['customer']['phone'] ?? $group->first()['customer']['mobile'],
                    'latitude' => $group->first()['location']['latitude'],
                    'longitude' => $group->first()['location']['longitude'],
                    'products' => $group->map(function ($item) {
                        return array_map(function ($product) use ($item) {
                            $product['details']['description'] = sanitize_html_string($product['details']['description']);
                            $product['jm_order_id'] = $item['id_order'];
                            $product['current_state_name'] = $item['current_state_name'];
                            $product['details']['price'] = $product['price_now'];
                            return $product;

                        }, $item['products']);
                    })->flatten(1)->toArray(),

                    'items' => $group->sum(function ($item) {
                        return count($item['products']);
                    }),
                ];
            })
            ->values()
            ->toArray();
        return $mergedItems;
    }

    public function getJmOrderByReference($reference)
    {
        $response = Http::get(env('JM_API_URL_STANDALONE') . "/delivery?reference={$reference}&order=desc");
        if (empty(json_decode($response, associative: true)['data'])) {
            throw new HttpException(404, 'Order not found');
        }

        $items = $response->json()['data'];

        $mergedItem = collect($items)
            ->groupBy('reference')
            ->map(function ($group) {
                return [
                    'delivery_date' => $group->first()['delivery_date'] ?? null,
                    'start_time' => $group->first()['start_time'] ?? null,
                    'end_time' => $group->first()['end_time'] ?? null,
                    'id_order' => $group->first()['id_order'],
                    'reference' => $group->first()['reference'],
                    'payment' => $group->first()['payment'],
                    'total_paid' => $group->sum('total_paid'),
                    'total_shipping' => $group->max()['total_shipping'],
                    'total_discounts' => number_format((float) $group->max('total_discounts'), 2, '.', ''),
                    'current_state_name' => $group->first()['current_state_name'],
                    'address' => $group->first()['customer']['address'],
                    'customer_name' => $group->first()['customer']['firstname'] . ' ' . $group->first()['customer']['lastname'],
                    'customer_phone' => $group->first()['customer']['phone'] ?? $group->first()['customer']['mobile'],
                    'latitude' => $group->first()['location']['latitude'],
                    'longitude' => $group->first()['location']['longitude'],
                    'products' => $group->map(function ($item) {
                        return array_map(function ($product) use ($item) {
                            $product['details']['description'] = sanitize_html_string($product['details']['description']);
                            $product['jm_order_id'] = $item['id_order'];
                            $product['current_state_name'] = $item['current_state_name'];
                            $product['details']['price'] = $product['price_now'];
                            return $product;
                        }, $item['products']);
                    })->flatten(1)->toArray(),
                    'items' => $group->sum(function ($item) {
                        return count($item['products']);
                    }),
                ];
            })
            ->values()
            ->toArray()[0];

        return $mergedItem;
    }

    public function storeNewJmOrderByRef($ref)
    {
        try {
            $item = $this->getJmOrderByReference($ref);

            $existingOrder = Order::where('reference', $item['reference'])->first();

            if (!empty($item['latitude']) && !empty($item['longitude'])) {
                $location = \App\Models\Location::create([
                    'latitude' => $item['latitude'],
                    'longitude' => $item['longitude'],
                ]);
            }

            if (!$existingOrder) {
                $order = Order::create([
                    'reference' => $item['reference'],
                    'price' => $item['total_paid'] - $item['total_shipping'],
                    'total_paid' => $item['total_paid'],
                    'total_shipping' => $item['total_shipping'],
                    'total_discounts' => $item['total_discounts'],
                    'payment_method' => $item['payment'],
                    'order_status_id' => 1,
                    'items' => $item['items'],
                    'address' => $item['address'],
                    'customer_name' => $item['customer_name'],
                    'customer_phone' => $item['customer_phone'],
                    'products' => $item['products'],
                    'start_time' => empty($item['start_time']) ? null : $item['start_time'],
                    'end_time' => empty($item['end_time']) ? null : $item['end_time'],
                    'location_id' => $location->id ?? null,
                ]);
            } else {
                $order = $existingOrder;
            }

            $subOrders = $item
                ->filter(function ($order) use ($item) {
                    return $order['reference'] === $item['reference'];
                })
                ->values()
                ->toArray();

            $this->subOrderService->storeSubOrders($order, $subOrders);


        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function storeNewJmOrders()
    {
        try {
            $processinginProgress = $this->getJmOrdersByStatus('Processing in Progress');

            $beingCollected = $this->getJmOrdersByStatus('being collected');

            $paymentAccepted = $this->getJmOrdersByStatus('Payment accepted');

            $awaitingCashOnDeliveryValidation = $this->getJmOrdersByStatus('Awaiting Cash On Delivery validation');

            $awaitingPaymentOnDeliveryValidation = $this->getJmOrdersByStatus('Awaiting Payment On Delivery validation');

            $items = collect($processinginProgress)
                ->merge($beingCollected)
                ->merge($paymentAccepted)
                ->merge($awaitingCashOnDeliveryValidation)
                ->merge($awaitingPaymentOnDeliveryValidation);

            $mergedItems = $this->getMeargedItems($items);

            DB::transaction(function () use ($mergedItems, $items) {
                foreach ($mergedItems as $item) {
                    $existingOrder = Order::where('reference', $item['reference'])->first();

                    if (!empty($item['latitude']) && !empty($item['longitude'])) {
                        $location = \App\Models\Location::create([
                            'latitude' => $item['latitude'],
                            'longitude' => $item['longitude'],
                        ]);
                    }

                    if (!$existingOrder) {
                        $order = Order::create([
                            'delivery_date' => $item['delivery_date'],
                            'reference' => $item['reference'],
                            'price' => $item['total_paid'] - $item['total_shipping'],
                            'total_paid' => $item['total_paid'],
                            'total_shipping' => $item['total_shipping'],
                            'total_discounts' => $item['total_discounts'],
                            'payment_method' => $item['payment'],
                            'order_status_id' => 1,
                            'items' => $item['items'],
                            'address' => $item['address'],
                            'customer_name' => $item['customer_name'],
                            'customer_phone' => $item['customer_phone'],
                            'products' => $item['products'],
                            'start_time' => empty($item['start_time']) ? null : $item['start_time'],
                            'end_time' => empty($item['end_time']) ? null : $item['end_time'],
                            'location_id' => $location->id ?? null,
                        ]);
                    } else {
                        $order = $existingOrder;
                    }

                    $subOrders = $items
                        ->filter(function ($order) use ($item) {
                            return $order['reference'] === $item['reference'];
                        })
                        ->values()
                        ->toArray();

                    $this->subOrderService->storeSubOrders($order, $subOrders);
                }
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function storeOrder($data)
    {
        return Order::create(
            [
                'reference' => $data['reference'],
                'price' => $data['total_paid'] - $data['total_shipping'],
                'total_paid' => $data['total_paid'],
                'total_shipping' => $data['total_shipping'],
                'total_discounts' => $data['total_discounts'],
                'payment_method' => $data['payment'],
                'order_status_id' => OrderStatus::where('slug', 'awaiting')->first()->id,
                'items' => $data['items'],
                'address' => $data['address'],
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'products' => $data['products'],
                'driver_id' => Auth::id(),
                'delivery_date' => $data['delivery_date'],
                'start_time' => empty($data['start_time']) ? null : $data['start_time'],
                'end_time' => empty($data['end_time']) ? null : $data['end_time'],
                'location_id' => $data['location_id'],
            ]
        );
    }

    public function updateJmOrders()
    {
        $res = Http::get(env('JM_API_URL'), ['route' => 'list']);

        $items = $res->json()['data'];

        try {
            foreach ($items as $item) {
                $subOrder = SubOrder::where('tracking_id', $item['id_order'])->first();

                $subOrder?->update([
                    'total' => $item['total_paid'],
                    'base_price' => $item['total_paid'] - $item['total_shipping'],
                    'shipping_price' => $item['total_shipping'],
                    'total_discounts' => $item['total_discounts'],
                    // 'products' => $item['products'],
                    'sub_order_status_id' => $item['current_state'],
                    'date_add' => $item['date_add'],
                    'date_upd' => $item['date_upd'],
                ]);
            }
        } catch (\Exception $e) {
            throw new \Exception('Error while fetching JM orders by status');
        }
    }

    public function updateJmStatusOrder(string $jmOrderId, string $jmStateId)
    {
        $response = Http::post(env('JM_API_URL_STANDALONE') . "/delivery/change_state.php", [
            'order_id' => $jmOrderId,
            'state_id' => $jmStateId
        ]);

        if (!$response->json()['success'] ?? false) {
            throw new HttpException(400, 'Failed to update JM order');
        }
    }

    public function getStats()
    {
        $driverId = Auth::id();

        $stats = [
            'cancelled' => SubOrder::where('driver_id', $driverId)
                ->where('sub_order_status_id', 14)
                ->where('sub_order_status_id', 15)
                ->count(),

            'in_progress' => SubOrder::where('driver_id', $driverId)
                ->whereIn('sub_order_status_id', ['3', '4', '9', '11', '13', '18', '19'])
                ->count(),

            'delivered' => SubOrder::where('driver_id', $driverId)
                ->unsettled()
                ->where('sub_order_status_id', '5')
                ->count(),
        ];

        return $stats;
    }

    public function getMeargedItems($items)
    {
        $orderReferences = Order::whereHas('orderStatus', function ($query) {
            $query->where('name', '!=', 'pending');
        })->pluck('reference');

        $mergedItems = collect($items)
            ->groupBy('reference')
            ->filter(function ($group, $reference) use ($orderReferences) {
                return !$orderReferences->contains($reference);
            })
            ->map(function ($group) {
                return [
                    'delivery_date' => $group->first()['delivery_date'] ?? null,
                    'start_time' => $group->first()['start_time'] ?? null,
                    'end_time' => $group->first()['end_time'] ?? null,
                    'id_order' => $group->first()['id_order'],
                    'reference' => $group->first()['reference'],
                    'payment' => $group->first()['payment'],
                    'total_paid' => $group->sum('total_paid'),
                    'total_shipping' => $group->max()['total_shipping'],
                    'total_discounts' => $group->max()['total_discounts'],
                    'current_state_name' => $group->first()['current_state_name'],
                    'address' => $group->first()['customer']['address'],
                    'customer_name' => $group->first()['customer']['firstname'] . ' ' . $group->first()['customer']['lastname'],
                    'customer_phone' => $group->first()['customer']['phone'] ?? $group->first()['customer']['mobile'],
                    'latitude' => $group->first()['location']['latitude'] ?? null,
                    'longitude' => $group->first()['location']['longitude'] ?? null,
                    'products' => $group->map(function ($item) {
                        return array_map(function ($product) use ($item) {
                            $product['details']['description'] = sanitize_html_string($product['details']['description']);
                            $product['jm_order_id'] = $item['id_order'];
                            $product['current_state_name'] = $item['current_state_name'];
                            $product['details']['price'] = $product['price_now'];
                            return $product;
                        }, $item['products']);
                    })->flatten(1)->toArray(),
                    'items' => $group->sum(function ($item) {
                        return count($item['products']);
                    }),
                ];
            })
            ->values()
            ->toArray();

        return $mergedItems;
    }
}
