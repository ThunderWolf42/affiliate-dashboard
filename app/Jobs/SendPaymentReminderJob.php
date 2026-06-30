<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Mail\PaymentReminder;
use App\Models\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendPaymentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan ulang jika job gagal.
     */
    public int $tries = 3;

    /**
     * Timeout maksimal eksekusi job (detik).
     */
    public int $timeout = 60;

    public function __construct(
        public readonly int $leadId,
        public readonly string $stepStartAt,
    ) {}

    /**
     * Execute the job.
     * Job ini dijalankan tepat pada H-0 deadline pembayaran.
     */
    public function handle(): void
    {
        $lead = Lead::find($this->leadId);

        if (!$lead) {
            Log::warning("SendPaymentReminderJob: Lead ID {$this->leadId} tidak ditemukan.");
            return;
        }

        $admisi = $lead->admisiRegistration;

        if (!$admisi) {
            Log::warning("SendPaymentReminderJob: Admisi untuk Lead ID {$this->leadId} tidak ditemukan.");
            return;
        }

        // Pastikan lead masih di step 4 dan step_start_at belum berubah
        // (guard: jika step_start_at berubah, berarti ada job baru yang sudah di-dispatch)
        if ((int) $admisi->current_step !== 4) {
            Log::info("SendPaymentReminderJob: Lead {$lead->lead_name} sudah tidak di step 4. Job dibatalkan.");
            return;
        }

        if ($admisi->step_start_at && Carbon::parse($admisi->step_start_at)->toDateTimeString() !== Carbon::parse($this->stepStartAt)->toDateTimeString()) {
            Log::info("SendPaymentReminderJob: step_start_at Lead {$lead->lead_name} sudah berubah. Job lama ini dibatalkan.");
            return;
        }

        // Hitung persentase pembayaran
        $tagihan = $admisi->total_tagihan ?? $lead->total_tagihan ?? 0;
        $dibayar = $admisi->total_dibayar ?? $lead->total_dibayar ?? 0;
        $persentase = $tagihan > 0 ? ($dibayar / $tagihan) * 100 : 0;

        if ($persentase >= 20) {
            Log::info("SendPaymentReminderJob: Lead {$lead->lead_name} sudah bayar {$persentase}% (>= 20%). Reminder tidak dikirim.");
            return;
        }

        // Cek apakah reminder sudah pernah dikirim untuk step_start_at ini
        if ($lead->payment_reminder_sent_at &&
            Carbon::parse($lead->payment_reminder_sent_at)->greaterThanOrEqualTo(Carbon::parse($admisi->step_start_at))) {
            Log::info("SendPaymentReminderJob: Reminder untuk Lead {$lead->lead_name} sudah dikirim sebelumnya. Lewati.");
            return;
        }

        Log::info("SendPaymentReminderJob: Mengirim reminder ke Lead {$lead->lead_name} (ID: {$lead->id}), bayar: {$persentase}%");

        // 1. Kirim Email
        try {
            Mail::to($lead->email)->send(new PaymentReminder($lead));
            Log::info("SendPaymentReminderJob: Email berhasil dikirim ke {$lead->email}");
        } catch (\Throwable $e) {
            Log::error("SendPaymentReminderJob: Gagal kirim email ke {$lead->email}: " . $e->getMessage());
        }

        // 2. Kirim Telegram (jika telegram_chat_id tersedia)
        if ($lead->telegram_chat_id) {
            try {
                $token = config('services.telegram.bot_token') ?? env('TELEGRAM_BOT_TOKEN');
                $messageText = "Halo *{$lead->lead_name}*! 👋\n\n" .
                    "Hari ini adalah batas waktu (deadline) pembayaran biaya pendaftaran & kuliah kamu di UKRIDA dengan nomor registrasi *{$lead->no_registrasi}*.\n\n" .
                    "Mohon segera melakukan pembayaran dan konfirmasi melalui sistem pendaftaran agar status pendaftaranmu tetap aktif dan tidak hangus.\n\n" .
                    "Untuk melakukan pembayaran, silakan akses:\n" .
                    "👉 [Sistem Pendaftaran UKRIDA](https://register.ukrida.ac.id/admisi/public/register/register/registerEmail)\n\n" .
                    "Jika kamu sudah melakukan pembayaran, mohon abaikan pesan ini. Terima kasih! ✨";

                $response = Http::withoutVerifying()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id'    => $lead->telegram_chat_id,
                    'text'       => $messageText,
                    'parse_mode' => 'Markdown',
                ]);

                if ($response->successful()) {
                    Log::info("SendPaymentReminderJob: Telegram terkirim ke chat ID {$lead->telegram_chat_id}");

                    ChatMessage::create([
                        'lead_id'      => $lead->id,
                        'message_text' => $messageText,
                        'direction'    => 'outbound',
                    ]);
                } else {
                    Log::error("SendPaymentReminderJob: Gagal kirim Telegram ke {$lead->telegram_chat_id}: " . $response->body());
                }
            } catch (\Throwable $e) {
                Log::error("SendPaymentReminderJob: Telegram API Error untuk {$lead->telegram_chat_id}: " . $e->getMessage());
            }
        } else {
            Log::info("SendPaymentReminderJob: Lead {$lead->lead_name} tidak memiliki telegram_chat_id.");
        }

        // 3. Update timestamp pengiriman
        $lead->updateQuietly([
            'payment_reminder_sent_at' => now(),
        ]);
    }
}
