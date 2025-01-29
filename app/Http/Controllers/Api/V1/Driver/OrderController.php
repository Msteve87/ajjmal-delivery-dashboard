<?php
namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function listOrders()
    {
        $orders = Order::where('driver_id', Auth::id())->get();

        return response()->json(
            [
                'data' => [
                    'items' => $orders,
                ],
            ]
        );
    }

    public function getJmOrders()
    {
        $response = Http::get('http://10.200.130.55/databasetest.php?limit=1000&sort_by=total_paid&order=desc');

        if ($response->successful()) {
            $items = $response->json()['data'];

            $mergedItems = collect($items)
                ->groupBy('reference')
                ->map(function ($group) {
                    return [
                        'delivery_date'      => $group->first()['delivery_date'],
                        'start_time'         => $group->first()['start_time'],
                        'end_time'           => $group->first()['end_time'],
                        'id_order'           => $group->first()['id_order'],
                        'reference'          => $group->first()['reference'],
                        'payment'            => $group->first()['payment'],
                        'total_paid'         => $group->sum('total_paid'),
                        'total_shipping'     => $group->max()['total_shipping'],
                        'current_state_name' => $group->first()['current_state_name'],
                        'address1'           => $group->first()['address1'],
                        'customer_phone'     => $group->first()['customer_phone'],
                        'customer_mobile'    => $group->first()['customer_mobile'],
                        'latitude'           => $group->first()['latitude'],
                        'longitude'          => $group->first()['longitude'],
                    ];
                })
                ->values()
                ->toArray();

            return response()->json(
                [
                    'data' => [
                        'items' => $mergedItems,
                    ],
                ]
            );
        } else {
            return response()->json(['error' => 'Failed to fetch data from API'], 500);
        }

    }

    public function acceptOrder(string $reference)
    {
        $exist = Order::where('reference', $reference)->exists();

        if ($exist) {
            return response()->json([
                'status'  => 'error',
                'message' => 'operation is not allowed',
            ], 400);
        }

        $response = Http::get("http://10.200.130.55/databasetest.php?reference={$reference}&order=desc");

        if (empty(json_decode($response, associative: true))) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Order not found',
            ], 404);
        }

        $items = $response->json()['data'];

        $mergedItem = collect($items)
            ->groupBy('reference')
            ->map(function ($group) {
                return [
                    'delivery_date'      => $group->first()['delivery_date'],
                    'start_time'         => $group->first()['start_time'],
                    'end_time'           => $group->first()['end_time'],
                    'id_order'           => $group->first()['id_order'],
                    'reference'          => $group->first()['reference'],
                    'payment'            => $group->first()['payment'],
                    'total_paid'         => $group->sum('total_paid'),
                    'total_shipping'     => $group->max()['total_shipping'],
                    'current_state_name' => $group->first()['current_state_name'],
                    'address1'           => $group->first()['address1'],
                    'customer_phone'     => $group->first()['customer_phone'],
                    'customer_mobile'    => $group->first()['customer_mobile'],
                    'latitude'           => $group->first()['latitude'],
                    'longitude'          => $group->first()['longitude'],
                ];
            })
            ->values()
            ->toArray()[0];

        $location = Location::create(
            [
                'latitude'  => $mergedItem['latitude'],
                'longitude' => $mergedItem['longitude'],
            ]
        );

        $order = Order::create(
            [
                'reference'      => $mergedItem['reference'],
                'total_paid'     => $mergedItem['total_paid'],
                'total_shipping' => $mergedItem['total_shipping'],
                'payment_method' => $mergedItem['payment'],
                'status'         => 'accepted',
                'driver_id'      => Auth::id(),
                'location_id'    => $location->id,
            ]
        );

        return response()->json(
            [
                'status' => 'success',
                'data'   => $order,
            ], 201
        );
    }
}
