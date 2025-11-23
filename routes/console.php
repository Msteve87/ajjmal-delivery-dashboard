<?php

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function (OrderService $orderService) {
    $orders = $orderService->getJmOrders();

    $refs = collect($orders)->pluck('reference')->filter()->values();

    foreach ($refs as $ref) {
        $orderService->storeNewJmOrderByRef($ref);
    }
})->everyThirtySeconds();

// Schedule::call(function (OrderService $orderService) {
//     $orderService->storeNewJmOrders();
// })->everyThirtySeconds();


Schedule::call(function (OrderService $orderService) {
    $orderService->updateJmOrders();
})->everyMinute();

