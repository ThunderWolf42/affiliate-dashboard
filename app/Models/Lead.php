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
        'jurusan_id', // WAJIB ADA AGAR BISA DISIMPAN
        'status',
        'telegram_chat_id', // Tambahkan field ini untuk menyimpan chat_id Telegram
    ];

    /**
     * RELASI: Ke Affiliate (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELASI: Ke Master Jurusan
     */
    public function jurusan(): BelongsTo
    {
        // Sesuaikan nama Class Model Jurusan kamu (Ref_Jurusan atau Jurusan)
        return $this->belongsTo(Ref_Jurusan::class, 'jurusan_id');
    }

    /**
     * RELASI: Ke Data Pendaftaran Admisi
     */
    public function admisiRegistration(): BelongsTo
    {
        return $this->belongsTo(Admisi_Registration::class, 'no_registrasi', 'no_registrasi');
    }

    /**
     * BOOTED: Otomatis mencari kecocokan saat data Lead diakses
     */
    protected static function booted()
    {
        static::retrieved(function ($lead) {
            // Hanya jalankan logic jika no_registrasi masih kosong
            if (is_null($lead->no_registrasi)) {
                $match = \App\Models\Admisi_Registration::where('email', $lead->email)
                    ->orWhere('no_hp', $lead->wa_number)
                    ->orWhere('nama_calon', $lead->lead_name)
                    ->first();

                if ($match) {
                    // Update field
                    $lead->no_registrasi = $match->no_registrasi;
                    $lead->jurusan_id = $match->jurusan_id;
                    $lead->status = 'active';

                    // saveQuietly agar tidak memicu event 'updated' atau 'retrieved' berulang kali
                    $lead->saveQuietly();
                }
            }
        });
    }
}
