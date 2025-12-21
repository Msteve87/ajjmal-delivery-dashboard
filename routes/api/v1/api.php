<?php

use App\Http\Controllers\Api\V1\DeviceTokenController;
use App\Http\Controllers\Api\V1\Driver\OrderController;
use App\Http\Controllers\Api\V1\LocalRateController;
use App\Http\Controllers\Api\V1\OrderStatusController;
use App\Http\Controllers\Api\V1\PaymentMethodController;
use App\Http\Controllers\Api\V1\SubOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::controller(OrderStatusController::class)
            ->group(function () {
                Route::get('/order-statuses/list', 'index');
                Route::get('/sub-order-statuses/list', 'listSubOrderStatuses');
            });

        Route::controller(OrderController::class)
            ->group(function () {
                Route::get('/show-jm-order-products/{reference}', 'showJmOrderProducts');
            });

        Route::controller(SubOrderController::class)
            ->group(function () {
                Route::get('/sub-orders/{trackingId}/show', 'showByTrackingId');
                Route::get('/sub-orders/{trackingId}/accept-sub-order', 'acceptSubOrder');
                Route::post('/sub-orders/update/{trackingId}', 'updateSubOrderStatus');
                Route::post('/sub-orders/{trackingId}/pickup', 'pickup');
                Route::get('/sub-orders/list', 'listNewSubOrders');
                Route::post('/sub-orders/{trackingId}/add-discount', 'addSubOrderDiscount');
            });

        Route::controller(DeviceTokenController::class)
            ->group(function () {
                Route::post('/device-tokens', 'store');
            });

        Route::controller(PaymentMethodController::class)
            ->group(function () {
                Route::get('/payment-methods/list', [PaymentMethodController::class, 'index']);
            });

        Route::controller(LocalRateController::class)
            ->group(function () {
                Route::get('/local-rates/list', 'index')->withoutMiddleware('auth:sanctum');
            });
    });
