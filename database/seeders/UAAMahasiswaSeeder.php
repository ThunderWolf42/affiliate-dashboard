<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UAAMahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nim' => '422022032', // Ini untuk Darrel
                'full_name' => 'Darrel Alvino Christhoper',
                'email_civitas' => 'darrel.422022032@civitas.ukrida.ac.id',
                'is_active' => 1,
                'has_tuition_debt' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nim' => '422022036', // Andia
                'full_name' => 'Andia',
                'email_civitas' => 'andia.422022036@civitas.ukrida.ac.id',
                'is_active' => 1,
                'has_tuition_debt' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nim' => '422022038', // Putri
                'full_name' => 'Putri',
                'email_civitas' => 'putri.422022038@civitas.ukrida.ac.id',
                'is_active' => 1,
                'has_tuition_debt' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nim' => '422022040', // Ganti NIM Supriadi biar beda sama Darrel!
                'full_name' => 'Supriadi',
                'email_civitas' => 'supriadi.422022040@civitas.ukrida.ac.id',
                'is_active' => 1,
                'has_tuition_debt' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nim' => '422022033', // Sandiaga Uno
                'full_name' => 'Sandiaga Uno',
                'email_civitas' => 'sandiaga.422022033@civitas.ukrida.ac.id',
                'is_active' => 0,
                'has_tuition_debt' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('u_a_a__mahasiswas')->insert($data);
    }
}
