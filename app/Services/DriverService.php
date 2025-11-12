<?php

namespace App\Services;

use App\Models\SubOrder;
use Illuminate\Support\Facades\Auth;

class DriverService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getDeliveriesTotalCashOnHand(string $driverId): float
    {
        return SubOrder::where('driver_id', $driverId)
            ->whereHas('order', function ($q) {
                $q->whereIn('payment_method', [
                    'الدفع عند الاستلام',
                    'Cash on delivery (COD)',
                ]);
            })
            ->delivered()
            ->unsettled()
            ->sum('total');
    }

    public function getDeliveriesTotalOnline(string $driverId): float
    {
        return SubOrder::where('driver_id', $driverId)
            ->whereHas('order', function ($q) {
                $q->whereIn('payment_method', [
                    'Module Wallet',
                    'Module Moamalat',
                    'Payment on delivery (POD)',
                ]);
            })
            ->delivered()
            ->unsettled()
            ->sum('total');
    }

    public function getDeliveriesFeesDue(string $driverId): float
    {
        return SubOrder::where('driver_id', $driverId)
            ->delivered()
            ->unsettled()
            ->sum('shipping_price') * 0.5;
    }
}
