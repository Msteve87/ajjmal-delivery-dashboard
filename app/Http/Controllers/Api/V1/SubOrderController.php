<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Driver;
use App\Models\SubOrder;
use Illuminate\Http\Request;
use App\Events\JmOrderStatusUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Api\V1\SubOrderResource;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SubOrderController extends Controller
{
    public function __construct(
        protected \App\Services\AjjmalMarketApiService $ajjmalMarketApiService,
        protected \App\Services\OrderService $orderService,
        protected \App\Services\SubOrderService $subOrderService,
        protected \App\Services\DeviceTokenService $deviceTokenService
    ) {
    }

    public function pickup(string $trackingId)
    {
        $subOrder = SubOrder::where('tracking_id', $trackingId)->firstOrFail();

        if ($subOrder->driver_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not assigned to this sub-order',
            ], 403);
        }

        $subOrder->update([
            'picked_up_by' => Auth::id()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Sub-order marked as picked up',
            'data' => $subOrder,
        ]);
    }

    /**
     * List new orders from Ajamal
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listNewSubOrders()
    {
        try {
            $items = $this->subOrderService->getUnassignedSubOrders();

            return response()->json(
                [
                    'status' => 'success',
                    'data' => [
                        'items' => SubOrderResource::collection($items),
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

    public function acceptSubOrder(string $trackingId)
    {
        try {
            $subOrder = SubOrder::where('tracking_id', $trackingId)->first();

            $subOrder->update([
                'driver_id' => Auth::id(),
            ]);

            event(new \App\Events\DriverAcceptedOrder($subOrder, Driver::find(auth()->id())));

            return response()->json(
                [
                    'status' => 'success',
                    'data' => new SubOrderResource($subOrder),
                ],
                200
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

    public function updateSubOrderStatus(string $trackingId, Request $request)
    {
        try {

            $request->validate([
                'statusId' => 'required|string|exists:sub_order_statuses,id',
            ]);

            $this->orderService->updateJmStatusOrder(
                $trackingId,
                $request->statusId
            );

            SubOrder::where('tracking_id', $trackingId)
                ->update([
                    'sub_order_status_id' => $request->statusId,
                ]);

            // event(new JmOrderStatusUpdated($trackingId));

            return response()->json([
                'status' => 'success',
                'message' => 'Ajjmal Order status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addSubOrderDiscount(string $trackingId, Request $request)
    {
        $request->validate([
            'discount_type' => ['required', 'string', 'in:amount,percent'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'discount_name' => ['required', 'string', 'max:255'],
        ]);

        $orderdDetails = $this->subOrderService->addDiscount(
            [
                'id_order' => $trackingId,
                ...$request->all()
            ]
        );

        return response()->json(
            [
                'status' => 'success',
                'data' => $orderdDetails
            ]
        );
    }

    public function showByTrackingId(string $trackingId)
    {
        $subOrder = SubOrder::where('tracking_id', $trackingId)->firstOrFail();

        return response()->json(
            [
                'status' => 'success',
                'data' => new SubOrderResource($subOrder),
            ]
        );
    }
}
