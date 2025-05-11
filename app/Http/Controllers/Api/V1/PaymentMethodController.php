<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::query()
            ->select('id', 'name', 'name_ar', 'code', 'icon', 'status')
            ->get();

        return response()->json(
            [
                'status' => 200,
                'data' => $paymentMethods,
            ],
        );
    }
}
