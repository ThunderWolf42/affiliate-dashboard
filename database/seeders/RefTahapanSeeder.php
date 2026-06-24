<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefTahapanSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data 8 tahap yang lama biar gak nyampah
        DB::table('ref_tahapans')->truncate();

        $tahapan = [
            ['step_number' => 1, 'step_name' => 'Pembayaran Biaya Formulir', 'sla_days' => 0],
            ['step_number' => 2, 'step_name' => 'Upload Berkas Rapor & Kesehatan', 'sla_days' => 0],
            ['step_number' => 3, 'step_name' => 'Seleksi Pendaftaran (Proses Admisi)', 'sla_days' => 0],
            ['step_number' => 4, 'step_name' => 'Pengumuman Kelulusan & Pembayaran Biaya Kuliah', 'sla_days' => 7], // 🔥 Pemicu H-7
        ];

        foreach ($tahapan as $t) {
            DB::table('ref_tahapans')->insert([
                'step_number' => $t['step_number'],
                'step_name' => $t['step_name'],
                'sla_days' => $t['sla_days'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
