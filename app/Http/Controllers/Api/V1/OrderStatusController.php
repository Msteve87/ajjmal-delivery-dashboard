<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\OrderStatus;
use App\Enums\JmOrderStatus;
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
        $statuses = array_map(fn($status) => [
            'id' => $status->value,
            'slug' => $status->slug(),
            'name' => $status->label(),
        ], JmOrderStatus::cases());

        return response()->json(['items' => $statuses]);
    }

}
