<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\SubOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubOrderController extends Controller
{
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
}
