<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->insert([
            ['name' => 'assign.permission', 'guard_name' => 'sanctum'],
            ['name' => 'add.settlement', 'guard_name' => 'sanctum'],
            ['name' => 'browse.activity_log', 'guard_name' => 'sanctum'],
            ['name' => 'browse.order', 'guard_name' => 'sanctum'],
            ['name' => 'add.user', 'guard_name' => 'sanctum'],
            ['name' => 'edit.user', 'guard_name' => 'sanctum'],
            ['name' => 'delete.user', 'guard_name' => 'sanctum'],
            ['name' => 'browse.user', 'guard_name' => 'sanctum'],

            ['name' => 'add.driver', 'guard_name' => 'sanctum'],
            ['name' => 'edit.driver', 'guard_name' => 'sanctum'],
            ['name' => 'view.driver', 'guard_name' => 'sanctum'],
            ['name' => 'browse.driver', 'guard_name' => 'sanctum'],

            ['name' => 'update.order.status', 'guard_name' => 'sanctum'],
            ['name' => 'browse.user', 'guard_name' => 'sanctum'],
            ['name' => 'browse.own_activity_log', 'guard_name' => 'sanctum'],
            ['name' => 'assign.delivery.tasks', 'guard_name' => 'sanctum']
        ]);
    }
}
