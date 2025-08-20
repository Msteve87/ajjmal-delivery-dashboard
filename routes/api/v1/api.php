<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\SubOrderController;
use App\Http\Controllers\Api\V1\OrderStatusController;
use App\Http\Controllers\Api\V1\Driver\OrderController;
use App\Http\Controllers\Api\V1\PaymentMethodController;

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::controller(OrderStatusController::class)
            ->group(function () {
                Route::get('/order-statuses/list', 'index');
                Route::get('/jm-order-statuses', 'listJmOrderStatuses');
            });

        Route::controller(OrderController::class)
            ->group(function () {
                Route::get('/show-jm-order/{reference}', 'showJmOrder');
            });

        Route::controller(SubOrderController::class)
            ->group(function () {
                Route::post('/sub-orders/{subOrder}/pickup', 'pickup');
            });

        Route::controller(PaymentMethodController::class)
            ->group(function () {
                Route::get('/payment-methods/list', [PaymentMethodController::class, 'index']);
            });
    });
