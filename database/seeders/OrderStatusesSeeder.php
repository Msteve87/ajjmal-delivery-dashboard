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
            ['slug' => 'awaiting', 'name' => 'In progress', 'name_ar' => 'جاري التوصيل'],
            ['slug' => 'in_progress', 'name' => 'In progress', 'name_ar' => 'جاري التوصيل'],
            ['slug' => 'delivered', 'name' => 'Delivered', 'name_ar' => 'تم التوصيل'],
            ['slug' => 'cancelled', 'name' => 'Cancelled', 'name_ar' => 'تم الإلغاء'],
        ]);
    }
}
