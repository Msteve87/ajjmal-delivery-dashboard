<?php

use App\Http\Controllers\Api\V1\Driver\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(OrderController::class)
    ->group(function () {
        Route::get('/get-jm-orders', 'getJmOrders');
    });
