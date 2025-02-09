<?php
namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        $driver = auth()->user();

        if ($driver->delivery_status === 'available') {
            $driver->delivery_status = 'not_available';
        } else {
            $driver->delivery_status = 'available';
        }

        $driver->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Driver delivery status updated successfully']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public static function me()
    {
        return Auth::user();
    }
}
