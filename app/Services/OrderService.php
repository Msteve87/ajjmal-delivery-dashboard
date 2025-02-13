<?php
namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getJmOrders($limit = 1000)
    {
        $params = [
            'limit' => $limit,
            'sort_by' => 'total_paid',
            'order' => 'desc',
        ];

        $response = Http::get(env('JM_API_URL'), $params);

        $orderReferences = Order::whereNot('status', 'pending')->pluck('reference');

        if ($response->successful()) {
            $items = $response->json()['data'];

            $mergedItems = collect($items)
                ->groupBy('reference')
                ->filter(function ($group, $reference) use ($orderReferences) {
                    return !$orderReferences->contains($reference);
                })
                ->map(function ($group) {
                    return [
                        'jm_order_id' => $group->first()['id_order'],
                        'delivery_date' => $group->first()['delivery_date'],
                        'start_time' => $group->first()['start_time'],
                        'end_time' => $group->first()['end_time'],
                        'id_order' => $group->first()['id_order'],
                        'reference' => $group->first()['reference'],
                        'payment_method' => $group->first()['payment'],
                        'total_paid' => $group->sum('total_paid'),
                        'total_shipping' => (float) $group->max()['total_shipping'],
                        'status' => 'pending',
                        'status_ar' => 'جديدة',
                        'current_state_name' => $group->first()['current_state_name'],
                        'customer_name' => $group->first()['customer']['firstname'] . ' ' . $group->first()['customer']['lastname'],
                        'address' => $group->first()['customer']['address'],
                        'customer_phone' => $group->first()['customer']['phone'] ?? $group->first()['customer']['mobile'],
                        'latitude' => $group->first()['location']['latitude'],
                        'longitude' => $group->first()['location']['longitude'],
                        'products' => $group->first()['products'],
                        'items' => $group->sum(function ($item) {
                            return count($item['products']);
                        }),
                    ];
                })
                ->values()
                ->toArray();

            return $mergedItems;

        } else {
            throw new \Exception('Error while fetching JM orders');
        }

    }

    public function getJmOrderByReference($reference)
    {
        $response = Http::get(env('JM_API_URL') . "?reference={$reference}&order=desc");

        if (empty(json_decode($response, associative: true)['data'])) {
            throw new HttpException(404, 'Order not found');
        }

        $items = $response->json()['data'];

        $mergedItem = collect($items)
            ->groupBy('reference')
            ->map(function ($group) {
                return [
                    'delivery_date' => $group->first()['delivery_date'],
                    'start_time' => $group->first()['start_time'],
                    'end_time' => $group->first()['end_time'],
                    'id_order' => $group->first()['id_order'],
                    'reference' => $group->first()['reference'],
                    'payment' => $group->first()['payment'],
                    'total_paid' => $group->sum('total_paid'),
                    'total_shipping' => $group->max()['total_shipping'],
                    'current_state_name' => $group->first()['current_state_name'],
                    'address' => $group->first()['customer']['address'],
                    'customer_name' => $group->first()['customer']['firstname'] . ' ' . $group->first()['customer']['lastname'],
                    'customer_phone' => $group->first()['customer']['phone'] ?? $group->first()['customer']['mobile'],
                    'latitude' => $group->first()['location']['latitude'],
                    'longitude' => $group->first()['location']['longitude'],
                    'products' => $group->first()['products'],
                    'items' => $group->sum(function ($item) {
                        return count($item['products']);
                    }),
                ];
            })
            ->values()
            ->toArray()[0];

        return $mergedItem;
    }
}
