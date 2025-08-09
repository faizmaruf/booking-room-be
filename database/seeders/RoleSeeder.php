<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'description' => 'Administrator dengan akses penuh',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user',
                'description' => 'Reguler user dengan akses terbatas atau reguler user untuk booking',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'pic ruangan',
                'description' => 'Person in Charge (PIC) ruangan untuk mengelola ruangan dan fasilitas',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
