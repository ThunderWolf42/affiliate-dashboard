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
                'name' => 'Jonathan',
                'email' => 'jonathan@admisiukrida.ac.id',
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
