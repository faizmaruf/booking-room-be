<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    public function run()
    {
        DB::table('rooms')->insert([
            [
                'name' => 'Aula Pepadun',
                'capacity' => 40,
                'description' => 'Aula Pepadun adalah ruang rapat modern yang dilengkapi dengan meja konferensi berbentuk U, kursi ergonomis, mikrofon di setiap tempat duduk, sistem pendingin ruangan (AC), dan proyektor. Ruangan ini cocok untuk rapat pimpinan, presentasi, serta pertemuan formal dengan kapasitas hingga 40 orang duduk di meja utama, serta tambahan kursi di bagian belakang untuk peserta lainnya.',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aula Saibatin',
                'capacity' => 100,
                'description' => 'Aula Saibatin adalah ruang serbaguna yang luas dan elegan, cocok untuk seminar, rapat besar, maupun kegiatan seremonial. Dilengkapi dengan panggung utama, meja pimpinan, kursi tamu berdesain klasik, serta kursi peserta yang dapat menampung hingga 100 orang. Ruangan ini juga dilengkapi dengan sistem pendingin ruangan, mikrofon, serta latar belakang yang cocok untuk presentasi atau sambutan resmi.',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aula PSTP',
                'capacity' => 10,
                'description' => 'Aula Saibatin adalah ruang serbaguna yang cocok untuk rapat sederhana. Dilengkapi dengan kursi pimpinan serta kursi peserta yang dapat menampung hingga 10 orang. Ruangan ini juga dilengkapi dengan sistem pendingin .',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
