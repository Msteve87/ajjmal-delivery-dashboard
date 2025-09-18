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
    public function listDriverSubOrders()
    {
        $subOrders = SubOrder::where('driver_id', Auth::id())
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
}
