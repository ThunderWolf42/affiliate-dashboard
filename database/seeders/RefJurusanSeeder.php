<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefJurusanSeeder extends Seeder
{
    public function run(): void
    {
        $jurusans = [
            [
                'nama_jurusan' => 'Informatika',
                'reward_amount' => 500000, // Rp 500.000
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jurusan' => 'Sistem Informasi',
                'reward_amount' => 450000, // Rp 450.000
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jurusan' => 'Teknik Elektro',
                'reward_amount' => 400000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jurusan' => 'Kedokteran',
                'reward_amount' => 1000000, // Rp 1.000.000 (Reward lebih besar)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jurusan' => 'Psikologi',
                'reward_amount' => 350000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($jurusans as $jurusan) {
            DB::table('ref_jurusans')->updateOrInsert(
                ['nama_jurusan' => $jurusan['nama_jurusan']],
                $jurusan
            );
        }
    }
}
