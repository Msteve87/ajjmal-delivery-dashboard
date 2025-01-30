
<?php

    use App\Http\Controllers\Api\V1\Driver\AuthController;
    use App\Http\Controllers\Api\V1\Driver\DriverController;
    use App\Http\Controllers\Api\V1\Driver\OrderController;
    use Illuminate\Support\Facades\Route;

    Route::controller(AuthController::class)
        ->group(function () {
            Route::post('/login', 'login');
        });

    Route::middleware('auth:sanctum')
        ->group(function () {

            Route::controller(DriverController::class)
                ->group(function () {
                    Route::get('/update-delivery-status', 'update');
                });
            Route::controller(OrderController::class)
                ->group(function () {
                    Route::get('/list-orders', 'listDriverOrders');
                    Route::get('/get-jm-orders', 'listNewOrders');
                    Route::get('/{reference}/accept-order', 'acceptOrder');
                });
    });
