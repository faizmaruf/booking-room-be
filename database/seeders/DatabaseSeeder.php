<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            WorkUnitSeeder::class,
            UserSeeder::class,
            RoomSeeder::class,
            RoomImageSeeder::class,
            BookingSeeder::class,
            PermissionSeeder::class,
            PermissionRoleSeeder::class,

        ]);
    }
}
