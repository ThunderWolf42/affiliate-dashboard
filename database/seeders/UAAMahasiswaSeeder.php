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
                'nim' => '222025004',
                'full_name' => 'Gatasya Valentina',
                'email_civitas' => 'gatasya.222025004@civitas.ukrida.ac.id',
                'is_active' => 1,
                'has_tuition_debt' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
              [
                'nim' => '4220240020',
                'full_name' => 'Steven Credentia Ivanemaga Zega',
                'email_civitas' => 'steven.4220240020@civitas.ukrida.ac.id',
                'is_active' => 1,
                'has_tuition_debt' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
              [
                'nim' => '312025018',
                'full_name' => 'Chelsea Marsha Kurniawan',
                'email_civitas' => 'chelsea,312025018@civitas.ukrida.ac.id',
                'is_active' => 1,
                'has_tuition_debt' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
              [
                'nim' => '312025024',
                'full_name' => 'Elizabeth',
                'email_civitas' => 'elizabeth.312025024@civitas.ukrida.ac.id',
                'is_active' => 1,
                'has_tuition_debt' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // [
            //     'nim' => '422022036', // Andia
            //     'full_name' => 'Andia',
            //     'email_civitas' => 'andia.422022036@civitas.ukrida.ac.id',
            //     'is_active' => 1,
            //     'has_tuition_debt' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'nim' => '422022038', // Putri
            //     'full_name' => 'Putri',
            //     'email_civitas' => 'putri.422022038@civitas.ukrida.ac.id',
            //     'is_active' => 1,
            //     'has_tuition_debt' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'nim' => '422022040', // Ganti NIM Supriadi biar beda sama Darrel!
            //     'full_name' => 'Supriadi',
            //     'email_civitas' => 'supriadi.422022040@civitas.ukrida.ac.id',
            //     'is_active' => 1,
            //     'has_tuition_debt' => 0,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'nim' => '422022033', // Sandiaga Uno
            //     'full_name' => 'Sandiaga Uno',
            //     'email_civitas' => 'sandiaga.422022033@civitas.ukrida.ac.id',
            //     'is_active' => 0,
            //     'has_tuition_debt' => 0,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ];

        DB::table('u_a_a__mahasiswas')->insert($data);
    }
}
