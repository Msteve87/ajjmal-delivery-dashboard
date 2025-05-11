<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\OrderStatusController;
use App\Http\Controllers\Api\V1\PaymentMethodController;

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::controller(OrderStatusController::class)
            ->group(function () {
                Route::get('/order-statuses/list', [OrderStatusController::class, 'index']);
            });

        Route::controller(PaymentMethodController::class)
            ->group(function () {
                Route::get('/payment-methods/list', [PaymentMethodController::class, 'index']);
            });
    });
