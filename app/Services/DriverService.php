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

    public function getTodaysDeliveriesTotalCashOnHand(string $driverId): float
    {
        return SubOrder::where('driver_id', $driverId)
            ->whereHas('order', function ($q) {
                $q->whereIn('payment_method', [
                    'الدفع عند الاستلام',
                    'Cash on delivery (COD)',
                ]);
            })
            ->unsettled()
            ->sum('total');
    }

    public function getTodaysDeliveriesTotalOnline(string $driverId): float
    {
        return SubOrder::where('driver_id', $driverId)
            ->whereHas('order', function ($q) {
                $q->whereIn('payment_method', [
                    'Module Wallet',
                    'Module Moamalat',
                    'Payment on delivery (POD)',
                ]);
            })
            ->deliveredToday()
            ->sum('total');
    }
}
