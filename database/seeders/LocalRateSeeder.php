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
        \App\Models\LocalRate::create([
            'areas' => ["طرابلس", "السراج", "جنزور", "الكريمية", "تاجوراء"],
            'rate'  => 5,
        ]);
    }
}
