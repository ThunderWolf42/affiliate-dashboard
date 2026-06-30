<?php

namespace App\Observers;

use App\Jobs\SendPaymentReminderJob;
use App\Models\Admisi_Registration;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AdmisiRegistrationObserver
{
    /**
     * Dipanggil setelah data Admisi_Registration diperbarui.
     * Jika current_step berubah menjadi 4 (tahap pembayaran),
     * langsung dispatch job reminder dengan delay tepat sampai deadline H-0.
     */
    public function updated(Admisi_Registration $admisi): void
    {
        // Hanya proses jika current_step BARU saja berubah menjadi 4
        if ($admisi->wasChanged('current_step') && (int) $admisi->current_step === 4) {
            $this->dispatchReminderJob($admisi);
        }

        // Juga handle jika step_start_at diupdate sementara sudah di step 4
        // (misalnya admin reset tanggal mulai)
        if ($admisi->wasChanged('step_start_at') && (int) $admisi->current_step === 4) {
            $this->dispatchReminderJob($admisi);
        }
    }

    /**
     * Dipanggil saat data Admisi_Registration pertama kali dibuat.
     * Jika langsung di-insert dengan current_step = 4, tangani juga.
     */
    public function created(Admisi_Registration $admisi): void
    {
        if ((int) $admisi->current_step === 4) {
            $this->dispatchReminderJob($admisi);
        }
    }

    /**
     * Hitung delay dan dispatch SendPaymentReminderJob.
     */
    private function dispatchReminderJob(Admisi_Registration $admisi): void
    {
        if (!$admisi->step_start_at) {
            Log::warning("AdmisiRegistrationObserver: step_start_at kosong untuk admisi no_registrasi={$admisi->no_registrasi}. Job tidak di-dispatch.");
            return;
        }

        // Ambil SLA days dari relasi refTahapan (default 7 hari jika tidak ada)
        $batasHari = $admisi->refTahapan?->sla_days ?? 7;

        $stepStartAt = Carbon::parse($admisi->step_start_at);
        $deadline    = $stepStartAt->copy()->addDays($batasHari);

        // Hitung delay dari sekarang ke deadline
        $delayDetik = now()->diffInSeconds($deadline, false);

        // Jika deadline sudah lewat atau hari ini (H-0), langsung jalankan tanpa delay
        if ($delayDetik <= 0) {
            Log::info("AdmisiRegistrationObserver: Deadline sudah H-0 atau lewat untuk no_registrasi={$admisi->no_registrasi}. Job langsung di-dispatch tanpa delay.");
            SendPaymentReminderJob::dispatch($this->getLeadId($admisi), $admisi->step_start_at)
                ->onQueue('default');
        } else {
            $delayMenit = round($delayDetik / 60);
            Log::info("AdmisiRegistrationObserver: Job di-dispatch untuk no_registrasi={$admisi->no_registrasi} dengan delay {$delayMenit} menit (deadline: {$deadline->toDateTimeString()}).");
            SendPaymentReminderJob::dispatch($this->getLeadId($admisi), $admisi->step_start_at)
                ->delay(now()->addSeconds($delayDetik))
                ->onQueue('default');
        }
    }

    /**
     * Dapatkan Lead ID berdasarkan no_registrasi admisi.
     */
    private function getLeadId(Admisi_Registration $admisi): int
    {
        $lead = \App\Models\Lead::where('no_registrasi', $admisi->no_registrasi)->first();

        if (!$lead) {
            Log::warning("AdmisiRegistrationObserver: Tidak ada Lead dengan no_registrasi={$admisi->no_registrasi}");
            return 0;
        }

        return $lead->id;
    }
}
