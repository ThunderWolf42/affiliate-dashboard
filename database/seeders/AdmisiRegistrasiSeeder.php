<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdmisiRegistrasiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'no_registrasi' => 'REG-2026-001',
                'nama_calon' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'no_hp' => '081234567890',
                'jurusan_id' => 1, // Informatika
                'current_step' => 2, // Misal: Upload Berkas
                'step_start_at' => now()->subDays(5), // Sudah 5 hari di tahap ini
                'last_update_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_registrasi' => 'REG-2026-002',
                'nama_calon' => 'Siti Aminah',
                'email' => 'siti.amina@gmail.com',
                'no_hp' => '085711223344',
                'jurusan_id' => 5,
                'current_step' => 6, // SUDAH STEP 6 (Potensi Reward Cair!)
                'step_start_at' => now()->subDays(1),
                'last_update_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_registrasi' => 'REG-2026-003',
                'nama_calon' => 'Andi Wijaya',
                'email' => 'andi.w@outlook.com',
                'no_hp' => '089988776655',
                'jurusan_id' => 3,
                'current_step' => 1, // Baru Isi Formulir
                'step_start_at' => now(),
                'last_update_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($data as $val) {
            DB::table('admisi__registrations')->updateOrInsert(
                ['no_registrasi' => $val['no_registrasi']],
                $val
            );
        }
    }
    //https://api.telegram.org/bot8783859456:AAHUNfcL5Eyi24Xv-N5ZzCkIh87rystOh5M/setWebhook?url=https://dc89-2001-448a-20a0-574e-b90a-832-312d-dd20.ngrok-free.app/api/telegram/webhook
}
