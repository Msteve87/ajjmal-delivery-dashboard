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
        DB::table('drivers')->insert([
            'phone'             => '218945860171',
            'phone_verified_at' => now(),
            'password'          => bcrypt('password'),
            'first_name'        => 'John',
            'last_name'         => 'Doe',
            'gender'            => 'female',
            'driver_type'       => 'employee',
            'dob'               => '1990-01-01',
            'passport_no'       => 'A1234567',
            'criminal_case'     => 'No criminal cases',
            'national_no'       => '987654321',
            'delivery_status'   => 'available',
            'status'            => 'pending',
            'is_active'         => true,
            'remember_token'    => null,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }
}
