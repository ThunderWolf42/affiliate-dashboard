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
        'jurusan_id',
        'status',
        'telegram_chat_id',
        'total_tagihan',
        'total_dibayar',
        'is_refund_case',
        'batch_number',
        'batch_year',
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
     * 🎯 LOGIKA REVISI ADMISI: Hitung Persentase & Pemicu H-7 di Tahap 4
     */
    public function getStatusSgsAttribute(): array
    {
        // 🔑 KUNCI SINKRONISASI REALTIME: Ambil data langsung dari objek relasi admisi!
        $admisi = $this->admisiRegistration;

        // Jika data admisi ada, pakai duit dari admisi. Kalau gak ada, baru pakai bawaan leads.
        $tagihan = $admisi ? $admisi->total_tagihan : $this->total_tagihan;
        $dibayar = $admisi ? $admisi->total_dibayar : $this->total_dibayar;

        // Rumus hitung persentase pembayaran uang masuk kuliah
        $persentase = $tagihan > 0 ? ($dibayar / $tagihan) * 100 : 0;

        $urgent = false;
        $rewardStatus = 'Belum Layak';

        // Membaca current_step milik Admisi
        $currentStep = $admisi?->current_step;

        // Pemicu aktif jika status lemparan admisi sudah masuk tahap 4
        if ((int) $currentStep === 4) {

            // Jagaan URGENT H-7: Jika di tahap 4 total bayar masih di bawah 20%
            if ($persentase < 20) {
                $urgent = true;
            }

            // Penentuan nasib status reward affiliator
            if ($this->is_refund_case) {
                $rewardStatus = $persentase >= 20 ? 'Sah (Cair - Refund Case)' : 'Hangus (Refund < 20%)';
            } else {
                $rewardStatus = $persentase >= 20 ? 'Sah (Bisa Cair)' : 'Menunggu Pelunasan (Min 20%)';
            }
        }

        return [
            'is_urgent' => $urgent,
            'reward_status' => $rewardStatus,
            'persen' => number_format($persentase, 1) . '%',
        ];
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
                    $lead->batch_number = $match->batch_number;
                    $lead->batch_year = $match->batch_year;
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
