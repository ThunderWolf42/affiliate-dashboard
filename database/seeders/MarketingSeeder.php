<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Marketing;

class MarketingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data dummy staff marketing UKRIDA yang diizinkan untuk registrasi akun
        $marketingStaffs = [
            [
                'name' => 'Stevanus UKRIDA Pusat',
                'email' => 'stevanus@admisiukrida.ac.id',
                'is_active' => true,

            ],
            [
                'name' => 'Budi Santoso (Marketing)',
                'email' => 'BudiSantoso@admisiukrida.ac.id',
                'is_active' => true,
            ],
            [
                'name' => 'Siti Aminah (Admisi)',
                'email' => 'SitiAminah@admisiukrida.ac.id',
                'is_active' => true,
            ],
        ];

        foreach ($marketingStaffs as $staff) {
            // updateOrCreate dipakai agar kalau seedernya di-run berkali-kali tidak akan bikin data ganda (double)
            Marketing::updateOrCreate(
                ['email' => $staff['email']], // Kunci pengecekan berdasarkan email
                $staff
            );
        }
    }
}
