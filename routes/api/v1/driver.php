
<?php
    use App\Http\Controllers\Api\V1;
    use Illuminate\Support\Facades\Route;

    Route::controller(V1\Driver\AuthController::class)
        ->group(function () {
            Route::post('/login', 'login');
        });

    Route::middleware('auth:sanctum')
        ->group(function () {

            Route::controller(V1\Driver\DriverController::class)
                ->group(function () {
                    Route::get('/update-delivery-status', 'update');
                });

            Route::controller(V1\Driver\OrderController::class)
                ->group(function () {
                    Route::get('/list-orders', 'listDriverOrders');
                    Route::get('/get-jm-orders', 'listNewOrders');
                    Route::get('/{reference}/accept-order', 'acceptOrder');
                });

            Route::controller(V1\HomepageController::class)
                ->group(function () {
                    Route::get('/last-orders', 'lastOrders');
                });
    });
