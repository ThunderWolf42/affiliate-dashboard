<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdmisiRegistrasiSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('admisi__registrations')->truncate();

        $data = [
            // [
            //     'no_registrasi' => 'REG-2026-001',
            //     'nama_calon' => 'kumaha damang',
            //     'email' => 'cecep@gmail.com',
            //     'no_hp' => '081234567890',
            //     'jurusan_id' => 1,
            //     'current_step' => 1,
            //     'total_tagihan' => 20000000.00,
            //     'total_dibayar' => 0.00,
            //     'step_start_at' => now()->subDays(1),
            //     'batch_number' => 1,
            //     'batch_year' => 2026,
            //     'last_update_at' => now(),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            [
                'no_registrasi' => 'REG-2026-002',
                'nama_calon' => 'Yudhi',
                'email' => 'yudhi.yk@gmail.com',
                'no_hp' => '0812233213',
                'jurusan_id' => 1,
                'current_step' => 1,
                'total_tagihan' => 20000000.00,
                'total_dibayar' => 0.00,
                'step_start_at' => now()->subDays(1),
                'batch_number' => 1,
                'batch_year' => 2026,
                'last_update_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'no_registrasi' => 'REG-2026-003',
            //     'nama_calon' => 'Chelsea Marsha Kurniawan', // <-- Sudah diperbaiki di sini
            //     'email' => 'chelseamarsha30@gmail.com',
            //     'no_hp' => '081310925505',
            //     'jurusan_id' => 1,
            //     'current_step' => 1,
            //     'total_tagihan' => 20000000.00,
            //     'total_dibayar' => 0.00,
            //     'step_start_at' => now()->subDays(1),
            //     'batch_number' => 1,
            //     'batch_year' => 2026,
            //     'last_update_at' => now(),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ];

        foreach ($data as $val) {
            DB::table('admisi__registrations')->updateOrInsert(
                ['no_registrasi' => $val['no_registrasi']],
                $val
            );
        }
    }

    // Note Webhook URL cadangan lu aman di sini:
    // https://api.telegram.org/bot8783859456:AAHUNfcL5Eyi24Xv-N5ZzCkIh87rystOh5M/setWebhook?url=https://dc89-2001-448a-20a0-574e-b90a-832-312d-dd20.ngrok-free.app/api/telegram/webhook
}
