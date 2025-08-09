<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Tim Data dan Informasi (Datin) - Bagian TU'],
            ['name' => 'Tim Sumber Daya Manusia (SDM) - Bagian TU'],
            ['name' => 'Tim Umum - Bagian TU'],
            ['name' => 'Tim Perencanaan - Bagian TU'],
            ['name' => 'Tim Organisasi dan Tata Laksana (Ortala) - Bagian TU'],
            ['name' => 'Tim Keuangan - Bagian TU'],
            ['name' => 'Tim Humas - Bagian TU'],
            ['name' => 'Tim Halal - Bagian TU'],
            ['name' => 'Tim Hukum dan Kerukunan Umat Beragama (KUB) - Bagian TU'],
            ['name' => 'Bidang Urusan Agama Islam'],
            ['name' => 'Bidang Bimbingan Masyarakat Hindu (Pembimas Hindu)'],
            ['name' => 'Bidang Penerangan Agama Islam, Zakat, dan Wakaf'],
            ['name' => 'Bidang Pendidikan Keagamaan dan Agama Islam'],
            ['name' => 'Bidang Pendidikan Madrasah'],
            ['name' => 'Bidang Penyelenggaraan Haji dan Umrah'],
            ['name' => 'Pembimbing Masyarakat Kristen (Pembimas Kristen)'],
            ['name' => 'Pembimbing Masyarakat Katolik (Pembimas Katolik)'],
            ['name' => 'Pembimbing Masyarakat Buddha (Pembimas Buddha)'],
        ];

        DB::table('work_units')->insert($units);
    }
}
