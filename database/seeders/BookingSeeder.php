<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('bookings')->insert([
            [
                'user_id' => 2,
                'room_id' => 1,
                'booking_date' => now()->toDateString(),
                'start_time' => '09:00:00',
                'end_time' => '11:00:00',
                'purpose' => 'Rapat Koordinasi Tahunan',
                'status' => 'approved',
                'created_by' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
