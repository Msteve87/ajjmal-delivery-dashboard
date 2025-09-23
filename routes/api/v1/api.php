<?php

use App\Http\Controllers\Api\V1\DeviceTokenController;
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
                Route::get('/sub-order-statuses/list', 'listSubOrderStatuses');
            });

        Route::controller(OrderController::class)
            ->group(function () {
                Route::get('/show-jm-order-products/{reference}', 'showJmOrderProducts');
            });

        Route::controller(SubOrderController::class)
            ->group(function () {
                Route::get('/sub-orders/{trackingId}/accept-sub-order', 'acceptSubOrder');
                Route::post('/sub-orders/update/{trackingId}', 'updateSubOrderStatus');
                Route::post('/sub-orders/{trackingId}/pickup', 'pickup');
                Route::get('/sub-orders/list', 'listNewSubOrders');
                Route::post('/sub-orders/{trackingId}/add-discount', 'addSubOrderDiscount');
            });

        Route::controller(DeviceTokenController::class)
            ->group(function () {
                Route::post('/device-token', 'store');
            });

        Route::controller(PaymentMethodController::class)
            ->group(function () {
                Route::get('/payment-methods/list', [PaymentMethodController::class, 'index']);
            });
    });
