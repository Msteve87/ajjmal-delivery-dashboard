<?php
namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        PaymentMethod::create([
            'name' => 'YusorPay',
            'name_ar' => 'يوسر باي',
            'code' => 'YUSORPAY',
            'icon' => 'payment-methods/yusorpay.png',
        ]);

        PaymentMethod::create([
            'name' => 'moamalat',
            'name_ar' => 'بطاقات مصرفية (online)',
            'code' => 'MOAMALAT',
            'icon' => 'payment-methods/moamalat.png',
        ]);

        PaymentMethod::create([
            'name' => 'sadad',
            'name_ar' => 'خدمة سداد',
            'code' => 'SADAD',
            'icon' => 'payment-methods/sadad.png',
        ]);

        $this->call([
            // DriverSeeder::class,
            // OrderStatusesSeeder::class
        ]);


    }
}
