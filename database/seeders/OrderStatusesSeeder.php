<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('order_statuses')->insert([
            ['slug' => 'cancelled_by_customer', 'name' => 'Cancelled by customer', 'name_ar' => 'إلغاء من العميل'],
            ['slug' => 'cancelled_by_seller', 'name' => 'Cancelled by seller', 'name_ar' => 'إلغاء من البائع'],
        ]);
    }
}
