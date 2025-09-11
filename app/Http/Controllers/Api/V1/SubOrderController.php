<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\SubOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Api\v1\ProductResource;
use App\Http\Resources\Api\V1\SubOrderResource;
use App\Http\Resources\Api\V1\OrderItemsResource;
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
        $subOrder->update([
            'is_picked_up' => true,
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
}
