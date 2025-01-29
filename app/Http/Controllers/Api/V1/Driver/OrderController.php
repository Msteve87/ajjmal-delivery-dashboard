<?php
namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
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
            return response()->json(['message' => 'operation is not allowed'], 400);
        }

        $http = Http::get("http://10.200.130.55/databasetest.php?reference={$reference}&order=desc");

        $data = json_decode($http, associative: true);

        if (empty($data['data'])) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $totalPaid = 0;

        foreach ($data['data'] as $jmOrder) {
            $totalPaid += $jmOrder['total_paid'];
        }

        $location = Location::create(
            [
                'latitude'  => $data['data'][0]['latitude'],
                'longitude' => $data['data'][0]['longitude'],
            ]
        );

        $order = Order::create(
            [
                'reference'      => $data['data'][0]['reference'],
                'total_paid'     => $totalPaid,
                'payment_method' => $data['data'][0]['payment'],
                'status'         => 'accepted',
                'driver_id'      => Auth::id(),
                'location_id'    => $location->id,
            ]
        );

        return response()->json(
            [
                'data' => $order,
            ]
        );
    }
}
