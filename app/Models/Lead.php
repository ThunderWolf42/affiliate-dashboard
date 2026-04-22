<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'no_registrasi',
        'lead_name',
        'email',
        'wa_number',
        'status',
    ];

    /**
     * RELASI: Lead ini milik siapa (Affiliate/Mahasiswa mana?)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELASI: Menghubungkan Lead ke data pendaftaran asli di Admisi
     * Kita hubungkan lewat no_registrasi
     */
    public function admisiRegistration(): BelongsTo
    {
        return $this->belongsTo(Admisi_Registration::class, 'no_registrasi', 'no_registrasi');
    }

    /**
     * LOGIC MATCHING: Fungsi untuk mencari kecocokan data di tabel Admisi
     * Ini yang kamu pakai untuk Triple Matching (Nama, Email, atau HP)
     */


    protected static function booted()
    {
        // Setiap kali data lead diambil dari database, sistem langsung ngecek matching
        static::retrieved(function ($lead) {
            if (!$lead->no_registrasi) {
                $match = \App\Models\Admisi_Registration::where('email', $lead->email)
                    ->orWhere('no_hp', $lead->wa_number)
                    ->orWhere('nama_calon', $lead->lead_name)
                    ->first();

                if ($match) {
                    $lead->no_registrasi = $match->no_registrasi;
                    $lead->status = 'active';
                    $lead->saveQuietly(); // Simpan diam-diam
                }
            }
        });
    }
    // public static function findMatchInAdmisi booted()
    // {
    //     // 1. Ambil data calon mahasiswa dari tabel Admisi yang cocok
    //     // Berdasarkan Email ATAU No HP ATAU Nama yang sama dengan data di model Lead ini
    //     $match = Admisi_Registration::where('email', $this->email)
    //         ->orWhere('no_hp', $this->wa_number)
    //         ->orWhere('nama_calon', $this->lead_name)
    //         ->first();

    //     // 2. Jika ditemukan kecocokan di database Admisi
    //     if ($match) {
    //         // 3. Update data di tabel Leads milik si Affiliate ini
    //         $this->update([
    //             'no_registrasi' => Lead::where('id', $this->id)->value('no_registrasi') ?? $match->no_registrasi, // Update no_registrasi jika belum ada
    //             'status'        => 'active' // Ubah jadi aktif karena sudah terdeteksi di admisi
    //         ]);
    //     }
    // }

}
