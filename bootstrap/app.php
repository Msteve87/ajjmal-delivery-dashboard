<?php
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/api/v1/api.php'));

            Route::middleware('api')
                ->prefix('api/v1/drivers')
                ->group(base_path('routes/api/v1/driver.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies([
            '192.168.10.26',
        ]);

        $middleware->api(prepend: [
            'App\Http\Middleware\ForceJsonResponse::class',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
