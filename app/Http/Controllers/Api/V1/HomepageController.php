<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class HomepageController extends Controller
{
    public function lastOrders()
    {
        $orders = Order::where('driver_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

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
