
<?php

    use App\Http\Controllers\Api\V1\Driver\AuthController;
    use App\Http\Controllers\Api\V1\Driver\OrderController;
    use Illuminate\Support\Facades\Route;

    Route::controller(AuthController::class)
        ->group(function () {
            Route::post('/login', 'login');
        });

    Route::controller(OrderController::class)
        ->group(function () {
            Route::get('/get-jm-orders', 'getJmOrders');
            Route::get('/{reference}/accept-order', 'acceptOrder');
    });
