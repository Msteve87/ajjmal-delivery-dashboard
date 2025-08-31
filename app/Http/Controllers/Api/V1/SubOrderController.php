<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\SubOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\ProductResource;
use App\Http\Resources\Api\V1\SubOrderResource;
use App\Http\Resources\Api\V1\OrderItemsResource;

class SubOrderController extends Controller
{
    public function __construct(
        protected \App\Services\AjjmalMarketApiService $ajjmalMarketApiService,
        protected \App\Services\OrderService $orderService,
        protected \App\Services\SubOrderService $subOrderService,
        protected \App\Services\DeviceTokenService $deviceTokenService
    ) {
    }

    public function pickup(SubOrder $subOrder)
    {
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
    public function listNewOrders()
    {
        try {
            $items = $this->ajjmalMarketApiService->getNewOrders();

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
}
