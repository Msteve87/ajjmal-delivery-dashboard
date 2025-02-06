<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

class HomepageController extends Controller
{
    public function __construct(
        protected \App\Services\OrderService $orderService
    ) {
    }

    public function lastOrders()
    {
        $orders = $this->orderService->getJmOrders();

        return response()->json(
            [
                'status' => 'success',
                'data'   => [
                    'items' => $orders,
                ],
            ]
        );
    }
}
