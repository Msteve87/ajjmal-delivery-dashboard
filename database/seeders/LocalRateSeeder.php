<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LocalRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\LocalRate::updateOrCreate(
            ['id' => 1],
            [
                'areas'       => ["طرابلس", "السراج", "جنزور", "الكريمية", "تاجوراء"],
                'home_rate'   => 5,
                'locker_rate' => 2,
            ]
        );
    }
}
