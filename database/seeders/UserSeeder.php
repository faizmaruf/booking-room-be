<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Faiz Alauddin M',
                'email' => '199811142025051004',
                'password' => Hash::make('ASN004141198'),
                'phone' => '08984153117',
                'role_id' => 1, // Assuming role_id 1 is for 'admin'
                'work_unit_id' => 1, // Assuming work_unit_id 1 is for 'Tim Data dan Informasi (Datin) - Bagian TU'
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name' => 'Yoga Dwi Septana',
                'email' => '199811142025051005',
                'password' => Hash::make('ASN004141198'),
                'phone' => '08123456788',
                'role_id' => 2, // Assuming role_id 2 is for 'user'
                'work_unit_id' => 2, // Assuming work_unit_id 2 is for 'Tim Sumber Daya Manusia (SDM) - Bagian TU'
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
