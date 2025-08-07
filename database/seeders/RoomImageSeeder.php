<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomImageSeeder extends Seeder
{
    public function run()
    {
        DB::table('room_images')->insert([
            // Pepadun room_id = 1
            ['room_id' => 1, 'image_path' => 'room_images/pepadun1.jpeg', 'is_main' => true, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 1, 'image_path' => 'room_images/pepadun2.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 1, 'image_path' => 'room_images/pepadun3.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 1, 'image_path' => 'room_images/pepadun4.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 1, 'image_path' => 'room_images/pepadun5.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 1, 'image_path' => 'room_images/pepadun6.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 1, 'image_path' => 'room_images/pepadun7.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 1, 'image_path' => 'room_images/pepadun8.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 1, 'image_path' => 'room_images/pepadun9.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],

            // PTSP room_id = 3
            ['room_id' => 3, 'image_path' => 'room_images/ptsp1.jpeg', 'is_main' => true, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 3, 'image_path' => 'room_images/ptsp2.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 3, 'image_path' => 'room_images/ptsp3.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 3, 'image_path' => 'room_images/ptsp4.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Saibatin room_id = 2
            ['room_id' => 2, 'image_path' => 'room_images/saibatin1.jpeg', 'is_main' => true, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 2, 'image_path' => 'room_images/saibatin2.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 2, 'image_path' => 'room_images/saibatin3.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 2, 'image_path' => 'room_images/saibatin4.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 2, 'image_path' => 'room_images/saibatin5.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 2, 'image_path' => 'room_images/saibatin6.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['room_id' => 2, 'image_path' => 'room_images/saibatin7.jpeg', 'is_main' => false, 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
