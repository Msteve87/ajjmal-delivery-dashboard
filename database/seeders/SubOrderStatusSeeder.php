<?php

namespace Database\Seeders;

use App\Models\SubOrderStatus;
use App\Services\AjjmalMarketApiService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubOrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = (new AjjmalMarketApiService())->getJmOrderStates();

        $subOrderStatuesAr = collect($items)
            ->where('id_lang', 2)
            ->keyBy('id_order_state');

        foreach ($items as $item) {
            if ($item['id_lang'] != 1) {
                continue;
            }

            SubOrderStatus::firstOrCreate(
                ['name' => $item['name']],
                [
                    'name' => $item['name'],
                    'name_ar' => $subOrderStatuesAr[$item['id_order_state']]['name'] ?? null,
                    'color' => $item['color'],
                ]
            );

        }
    }
}
