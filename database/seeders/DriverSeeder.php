<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('drivers')
            ->insert([
                'phone' => '218930598145',
                'phone_verified_at' => now(),
                'password' => bcrypt('P@ss@145'),
                'first_name' => 'Hiba',
                'last_name' => 'Tannish',
                'gender' => 'female',
                'driver_type' => 'employee',
                'dob' => '1993-01-01',
                'passport_no' => '',
                'criminal_case' => 'No criminal cases',
                'national_no' => '',
                'delivery_status' => 'available',
                'status' => 'pending',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }
}
