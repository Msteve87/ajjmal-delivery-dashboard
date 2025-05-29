<?php
namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Resources\Api\V1\OrdersResource;
use App\Models\Order;
use App\Models\Location;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderController extends Controller
{
    public function __construct(
        protected \App\Services\OrderService $orderService
    ) {
    }

    public function awaitingOrders()
    {
        $awaitingOrders = Order::whereHas('orderStatus', function ($query) {
            $query->where('slug', 'awaiting')
                ->orWhere('slug', 'in_progress');
        })->with(['orderStatus:id,name,name_ar', 'location'])->get();

        return response()->json(
            [
                'status' => 'success',
                'data' => [
                    'items' => OrdersResource::collection($awaitingOrders),
                ],
            ]
        );
    }

    /**
     * List new orders from Ajamal
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listNewOrders()
    {
        try {
            $items = $this->orderService->getJmOrders();

            return response()->json(
                [
                    'status' => 'success',
                    'data' => [
                        'items' => $items,
                    ],
                ]
            );

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function listDriverOrders()
    {
        $orders = Order::
            where('driver_id', Auth::id())
            ->with('location:id,latitude,longitude')
            ->where('order_status_id', 4)
            ->get();

        return response()->json(
            [
                'status' => 'success',
                'data' => [
                    'items' => OrdersResource::collection($orders),
                ],
            ]
        );
    }

    public function acceptOrder(string $reference)
    {
        try {
            $exist = Order::whereHas('orderStatus', function ($query) {
                $query->where('name', '===', 'pending');
            })->exists();

            if ($exist) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'operation is not allowed',
                ], 400);
            }

            $item = $this->orderService->getJmOrderByReference($reference);

            $location = Location::create([
                'latitude' => $item['latitude'],
                'longitude' => $item['longitude'],
            ]);

            $existingOrder = Order::where('reference', $reference)->first();

            if ($existingOrder) {
                $existingOrder->update([
                    'jm_order_id' => $item['id_order'],
                    'price' => $item['total_paid'] - $item['total_shipping'],
                    'total_paid' => $item['total_paid'],
                    'total_shipping' => $item['total_shipping'],
                    'payment_method' => $item['payment'],
                    'order_status_id' => 2,
                    'items' => $item['items'],
                    'address' => $item['address'],
                    'customer_name' => $item['customer_name'],
                    'customer_phone' => $item['customer_phone'],
                    'products' => $item['products'],
                    'driver_id' => Auth::id(),
                    'location_id' => $location->id,
                ]);

                $order = $existingOrder;
            } else {
                $order = Order::create(
                    [
                        'jm_order_id' => $item['id_order'],
                        'reference' => $item['reference'],
                        'price' => $item['total_paid'] - $item['total_shipping'],
                        'total_paid' => $item['total_paid'],
                        'total_shipping' => $item['total_shipping'],
                        'payment_method' => $item['payment'],
                        'order_status_id' => 2,
                        'items' => $item['items'],
                        'address' => $item['address'],
                        'customer_name' => $item['customer_name'],
                        'customer_phone' => $item['customer_phone'],
                        'products' => $item['products'],
                        'driver_id' => Auth::id(),
                        // 'delivery_date' => $item['delivery_date'],
                        'location_id' => $location->id,
                    ]
                );
            }

            return response()->json(
                [
                    'status' => 'success',
                    'data' => new OrdersResource($order),
                ],
                201
            );

        } catch (HttpException $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $order = Order::findOrFail($id);
            $status = OrderStatus::findOrFail($request->statusId);

            if (app()->environment('production')) {
                switch ($status->slug) {
                    case "pending":
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Operation is not allowed'
                        ], 400);
                    case "awaiting":
                        if ($order->orderStatus->slug !== "pending") {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Operation is not allowed'
                            ], 400);
                        }
                        break;
                    case "in_progress":
                        if ($order->orderStatus->slug !== "awaiting") {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Operation is not allowed'
                            ], 400);
                        }
                        break;
                    case "delivered":
                        if ($order->orderStatus->slug !== "in_progress") {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Operation is not allowed'
                            ], 400);
                        }
                        break;
                    case "cancelled":
                        if ($order->orderStatus->slug === "delivered" || $order->orderStatus->slug === "in_progress") {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Operation is not allowed'
                            ], 400);
                        }
                        break;
                }
            }

            $order->order_status_id = $request->statusId;

            $order->save();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Order status updated successfully'
                ]
            );

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
