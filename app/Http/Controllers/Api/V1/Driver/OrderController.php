<?php
namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderController extends Controller
{
    public function __construct(
        protected \App\Services\OrderService $orderService
    ) {
    }

    public function listNewOrders()
    {
        try {
            $items = $this->orderService->getJmOrders();

            return response()->json(
                [
                    'status' => 'success',
                    'data'   => [
                        'items' => $items,
                    ],
                ]
            );

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function listDriverOrders()
    {
        $orders = Order::where('driver_id', Auth::id())->get();

        return response()->json(
            [
                'status' => 'success',
                'data'   => [
                    'items' => $orders,
                ],
            ]
        );
    }

    public function acceptOrder(string $reference)
    {
        try {
            $exist = Order::where('reference', $reference)->exists();

            if ($exist) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'operation is not allowed',
                ], 400);
            }

            $item = $this->orderService->getJmOrderByReference($reference);

            $location = Location::create([
                'latitude'  => $item['latitude'],
                'longitude' => $item['longitude'],
            ]);

            $order = Order::create(
                [
                    'reference'      => $item['reference'],
                    'total_paid'     => $item['total_paid'],
                    'total_shipping' => $item['total_shipping'],
                    'payment_method' => $item['payment'],
                    'status'         => 'accepted',
                    'items'          => $item['items'],
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

        } catch (HttpException $e) {
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage()], 500);
        }
    }
}
