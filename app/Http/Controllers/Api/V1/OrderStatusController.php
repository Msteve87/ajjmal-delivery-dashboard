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

    public function listJmOrderStatuses()
    {
        $items = SubOrderStatus::all();

        return response()->json([
            'status' => 'success',
            'items' => $items
        ]);
    }

}
