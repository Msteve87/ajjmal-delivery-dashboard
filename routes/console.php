<?php

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Schedule::call(function (OrderService $orderService) {
    $orderService->storeNewJmOrders();
})->everyMinute();


Schedule::call(function (OrderService $orderService) {
    $orderService->updateJmOrders();
})->everyMinute();