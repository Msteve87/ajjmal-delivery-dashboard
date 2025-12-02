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
            ->sum(DB::raw('CASE WHEN total > total_discounts THEN total - total_discounts ELSE 0 END'));
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
            ->sum(DB::raw('CASE WHEN total > total_discounts THEN total - total_discounts ELSE 0 END'));
    }

    public function getDeliveriesFeesDue(string $driverId): float
    {
        return SubOrder::where('driver_id', $driverId)
            ->delivered()
            ->unsettled()
            ->sum('shipping_price') * 0.5;
    }
}
