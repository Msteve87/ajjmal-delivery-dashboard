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
            ['slug' => 'pending', 'name' => 'Pending', 'name_ar' => 'جديدة'],
            ['slug' => 'awaiting', 'name' => 'awaiting', 'name_ar' => 'جاري التنفيد'],
            ['slug' => 'in_progress', 'name' => 'In progress', 'name_ar' => 'جاري التوصيل'],
            ['slug' => 'delivered', 'name' => 'Delivered', 'name_ar' => 'تم التوصيل'],
            ['slug' => 'cancelled_by_customer', 'name' => 'Cancelled by customer', 'name_ar' => 'إلغاء من العميل'],
            ['slug' => 'cancelled_by_seller', 'name' => 'Cancelled by seller', 'name_ar' => 'إلغاء من البائع'],
        ]);
    }
}
