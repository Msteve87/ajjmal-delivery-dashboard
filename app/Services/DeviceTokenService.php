<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DeviceTokenService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getDeviceToken($driverId)
    {
        return DB::table('device_tokens')
            ->where('driver_id', $driverId)->value('token');
    }

    public function storeDeviceToken($driverId, $token, $deviceType = null)
    {
        return (DB::table('device_tokens')->updateOrInsert(
            ['driver_id' => $driverId],
            ['token' => $token, 'device_type' => $deviceType]
        ));
    }
}
