<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\OrderStatusController;

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::controller(OrderStatusController::class)
            ->group(function () {
                Route::get('/order-statuses/list', [OrderStatusController::class, 'index']);
            });
    });
