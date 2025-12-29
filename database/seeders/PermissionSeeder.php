<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'assign.permission',
            'add.settlement',
            'browse.activity_log',
            'browse.order',
            'add.user',
            'edit.user',
            'delete.user',
            'browse.user',
            'add.driver',
            'edit.driver',
            'view.driver',
            'browse.driver',
            'update.order.status',
            'browse.own_activity_log',
            'assign.delivery.tasks',
            'edit.rates',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'sanctum']
            );
        }
    }
}
