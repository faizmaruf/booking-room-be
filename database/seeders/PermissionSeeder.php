<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->insert([
            [
                'name' => 'view users',
                'type' => 'view',
                'slug' => 'view-users',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'create users',
                'type' => 'create',
                'slug' => 'create-users',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'edit users',
                'type' => 'edit',
                'slug' => 'edit-users',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'delete users',
                'type' => 'delete',
                'slug' => 'delete-users',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'view roles',
                'type' => 'view',
                'slug' => 'view-roles',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'create roles',
                'type' => 'create',
                'slug' => 'create-roles',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'edit roles',
                'type' => 'edit',
                'slug' => 'edit-roles',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'delete roles',
                'type' => 'delete',
                'slug' => 'delete-roles',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'view permissions',
                'type' => 'view',
                'slug' => 'view-permissions',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'create permissions',
                'type' => 'create',
                'slug' => 'create-permissions',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'edit permissions',
                'type' => 'edit',
                'slug' => 'edit-permissions',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'delete permissions',
                'type' => 'delete',
                'slug' => 'delete-permissions',
                'created_by' => 1,
                'created_at' => now(),
            ],

            [
                'name' => 'view rooms',
                'type' => 'view',
                'slug' => 'view-rooms',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'create rooms',
                'type' => 'create',
                'slug' => 'create-rooms',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'edit rooms',
                'type' => 'edit',
                'slug' => 'edit-rooms',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'delete rooms',
                'type' => 'delete',
                'slug' => 'delete-rooms',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'view bookings',
                'type' => 'view',
                'slug' => 'view-bookings',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'create bookings',
                'type' => 'create',
                'slug' => 'create-bookings',
                'created_by' => 1,
                'created_at' => now(),
            ],

            [
                'name' => 'edit bookings',
                'type' => 'edit',
                'slug' => 'edit-bookings',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'delete bookings',
                'type' => 'delete',
                'slug' => 'delete-bookings',
                'created_by' => 1,
                'created_at' => now(),
            ],

            [
                'name' => 'edit status bookings',
                'type' => 'edit',
                'slug' => 'edit-status-bookings',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'view reports',
                'type' => 'view',
                'slug' => 'view-reports',
                'created_by' => 1,
                'created_at' => now(),
            ],


        ]);
    }
}
