<?php

namespace Database\Seeders;

use App\Models\MarketingMaterial;
use Illuminate\Database\Seeder;

class MarketingMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data kumpulan materi marketing simulasi untuk UKRIDA
        $materials = [
            [
                'title' => 'Brosur Resmi Pendaftaran Kuliah UKRIDA 2026',
                'description' => 'Brosur utama berisi daftar fakultas, program studi, rincian biaya kuliah, dan alur pendaftaran resmi. Silakan bagikan file ini ke grup WhatsApp alumni SMK/SMA kamu.',
                'download_link' => 'https://drive.google.com/file/d/sample-brosur-ukrida/view',
            ],
            [
                'title' => 'Flyer Digital Program Beasiswa Jalur Prestasi & Nilai Rapor',
                'description' => 'E-flyer berukuran kotak (1:1) yang sangat cocok untuk dipasang di Story Instagram, Status WhatsApp, atau Feed media sosial pribadi kamu untuk memikat calon mahasiswa.',
                'download_link' => 'https://drive.google.com/file/d/sample-flyer-beasiswa/view',
            ],
            [
                'title' => 'Panduan & Tips Sukses Lolos Tes Potensi Akademik (TPA)',
                'description' => 'E-book ringkas berisi kisi-kisi soal dan contoh ujian TPA UKRIDA. Berikan file ini kepada calon mahasiswa yang ragu agar mereka semakin tertarik mendaftar lewat kode kamu.',
                'download_link' => 'https://drive.google.com/file/d/sample-panduan-tpa/view',
            ],
        ];

        // Looping untuk memasukkan data ke database jika data tersebut belum ada
        foreach ($materials as $material) {
            MarketingMaterial::firstOrCreate(
                ['title' => $material['title']], // Jagaan biar data gak double kalau seeder dijalankan ulang
                [
                    'description' => $material['description'],
                    'download_link' => $material['download_link'],
                ]
            );
        }
    }
}
