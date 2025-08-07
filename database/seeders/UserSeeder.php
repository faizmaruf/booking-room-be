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
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Yoga Dwi Septana',
                'email' => '199811142025051005',
                'password' => Hash::make('ASN004141198'),
                'phone' => '08123456788',
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
