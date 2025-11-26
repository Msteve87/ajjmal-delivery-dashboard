<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\OrderStatus;
use App\Enums\JmOrderStatus;
use App\Models\SubOrderStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderStatusController extends Controller
{
    public function index()
    {
        $statuses = OrderStatus::all();

        return response()->json(['items' => $statuses]);
    }

    public function listSubOrderStatuses()
    {
        $items = SubOrderStatus::whereIn(
            'id',
            [
                JmOrderStatus::processingInProgress->value,
                JmOrderStatus::readyForDelivery->value,
                JmOrderStatus::delivered->value,
                JmOrderStatus::cancellationByCustomer->value,
                JmOrderStatus::cancellationByMerchant->value,
                JmOrderStatus::refunded->value,
            ]
        )->get();

        return response()->json([
            'status' => 'success',
            'items' => $items
        ]);
    }

}
