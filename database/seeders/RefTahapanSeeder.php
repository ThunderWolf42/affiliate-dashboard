<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefTahapanSeeder extends Seeder
{
    public function run(): void
    {
        $tahapan = [
            ['step_number' => 1, 'step_name' => 'Pengisian Formulir', 'sla_days' => 2],
            ['step_number' => 2, 'step_name' => 'Upload Berkas (Ijazah/SKL)', 'sla_days' => 3],
            ['step_number' => 3, 'step_name' => 'Verifikasi Dokumen', 'sla_days' => 2],
            ['step_number' => 4, 'step_name' => 'Ujian Saringan Masuk (USM)', 'sla_days' => 1],
            ['step_number' => 5, 'step_name' => 'Wawancara (Khusus Prodi Tertentu)', 'sla_days' => 3],
            ['step_number' => 6, 'step_name' => 'Pembayaran Uang Pangkal', 'sla_days' => 7], // Titik cair reward
            ['step_number' => 7, 'step_name' => 'Penyerahan Jas Almamater', 'sla_days' => 14],
            ['step_number' => 8, 'step_name' => 'Mahasiswa Baru (Lunas)', 'sla_days' => 0],
        ];

        foreach ($tahapan as $t) {
            DB::table('ref_tahapans')->updateOrInsert(
                ['step_number' => $t['step_number']], // Kunci pengecekan
                [
                    'step_name' => $t['step_name'],
                    'sla_days' => $t['sla_days'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
