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

    public function getTodaysDeliveries(string $driverId)
    {
        return SubOrder::where('driver_id', $driverId)
            ->deliveredToday()
            ->get();
    }
}
