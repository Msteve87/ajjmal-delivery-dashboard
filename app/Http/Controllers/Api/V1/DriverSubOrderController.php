<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\SubOrder;
use App\Http\Resources\Api;
use App\Enums\JmOrderStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DriverSubOrderController extends Controller
{
    public function getSubOrders()
    {
        $subOrders = SubOrder::where('driver_id', Auth::id())
            ->where('sub_order_status_id', JmOrderStatus::delivered->value)
            ->with('order')
            ->get();

        return response()->json(
            [
                'status' => 'success',
                'data' => [
                    'items' => Api\V1\SubOrderResource::collection($subOrders),
                ],
            ]
        );
    }

    public function getAwaitingSubOrders()
    {
        $subOrders = SubOrder::where('driver_id', Auth::id())
            ->with('order')
            ->whereIn('sub_order_status_id', [
                JmOrderStatus::processingInProgress->value,
                JmOrderStatus::deliveryIsInProgress->value,
                JmOrderStatus::readyForDelivery->value,
                JmOrderStatus::paymentAccepted->value,
                JmOrderStatus::beingCollected->value,
                JmOrderStatus::awaitingCashOnDelivery->value,
                JmOrderStatus::awaitingPaymentOnDeliveryValidation->value,
            ])
            ->get();

        return response()->json(
            [
                'status' => 'success',
                'data' => [
                    'items' => Api\V1\SubOrderResource::collection($subOrders),
                ],
            ]
        );
    }

    public function getCanceledSubOrders()
    {
        $subOrders = SubOrder::where('driver_id', Auth::id())
            ->with('order')
            ->whereIn('sub_order_status_id', [
                JmOrderStatus::cancellationByCustomer->value,
                JmOrderStatus::cancellationByMerchant->value,
            ])
            ->get();

        return response()->json(
            [
                'status' => 'success',
                'data' => [
                    'items' => Api\V1\SubOrderResource::collection($subOrders),
                ],
            ]
        );
    }
}
