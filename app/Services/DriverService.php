<?php

namespace App\Services;

use App\Models\SubOrder;
use Illuminate\Support\Facades\DB;
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
        $methods = [
            'الدفع عند الاستلام',
            'Cash on delivery (COD)',
        ];

        return (float) SubOrder::where('driver_id', $driverId)
            ->whereHas('order', function ($q) use ($methods) {
                $q->whereIn('payment_method', $methods);
            })
            ->delivered()
            ->unsettled()
            ->sum('total');
    }

    public function getDeliveriesTotalOnline(string $driverId): float
    {
        $onlineMethods = [
            'Module Wallet',
            'Module Moamalat',
            'Payment on delivery (POD)',
            'بطاقة مصرفية (اونلاين)',
            'الدفع بالبطاقة المصرفية ( ماكينة)'
        ];

        return (float) SubOrder::where('driver_id', $driverId)
            ->whereHas('order', function ($q) use ($onlineMethods) {
                $q->whereIn('payment_method', $onlineMethods);
            })
            ->delivered()
            ->unsettled()
            ->sum('total');
    }


}
