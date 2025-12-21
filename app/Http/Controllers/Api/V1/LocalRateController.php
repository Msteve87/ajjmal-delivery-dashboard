<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LocalRate;

class LocalRateController extends Controller
{
    public function index()
    {
        $localRates = LocalRate::all();
        
        $data = $localRates->map(function ($localRate) {
            return [
                'areas' => $localRate->areas,
                'home_rate' => $localRate->home_rate,
                'locker_rate' => $localRate->locker_rate,
            ];
        });

        return response()->json([
            'data' => $data,
        ]);
    }
}
