<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderStatusController extends Controller
{
    public function index()
    {
        $statuses = OrderStatus::all();

        return response()->json(['items' => $statuses]);
    }
}
