<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DriverBalanceController extends Controller
{
    public function __construct(
        protected \App\Services\DriverService $driverService
    ) {
    }

    public function unsettledTotals()
    {
        return response()->json(
            [
                'status' => 'success',
                'data' => [
                    'total_cash' => $this->driverService->getDeliveriesTotalCashOnHand(auth()->user()->id),
                    'total_online' => $this->driverService->getDeliveriesTotalOnline(auth()->user()->id),
                ],
            ]
        );
    }
}
