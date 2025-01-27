<?php
namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function getJmOrders()
    {
        $http = Http::get('http://10.200.130.55/databasetest.php?limit=1000&sort_by=total_paid&order=desc');

        return response()->json(
            [
                'data' => $http->json(),
            ]
        );
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
                'driver_id'      => 1,
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
