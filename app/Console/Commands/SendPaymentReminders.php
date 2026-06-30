<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\ChatMessage;
use App\Mail\PaymentReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminders to leads who have reached their payment deadline (H-0)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to check for payment deadlines...');

        // Fetch leads that are on step 4 (current_step = 4)
        $leads = Lead::whereNotNull('no_registrasi')
            ->whereHas('admisiRegistration', function ($query) {
                $query->where('current_step', 4);
            })
            ->get();

        $count = 0;

        foreach ($leads as $lead) {
            $admisi = $lead->admisiRegistration;
            if (!$admisi || !$admisi->step_start_at) {
                continue;
            }

            $tglMulaiTahap = Carbon::parse($admisi->step_start_at);
            $batasHari = $admisi->refTahapan?->sla_days ?? 7;
            $deadline = $tglMulaiTahap->copy()->addDays($batasHari);

            // Sisa hari calculated using the same logic as the lead list countdown column,
            // or calendar day difference. To align perfectly with countdown_h7:
            $sisaHari = (int) ceil(now()->diffInDays($deadline, false));

            if ($sisaHari === 0) {
                // Check if reminder was already sent for the current stage/step_start_at
                if ($lead->payment_reminder_sent_at && Carbon::parse($lead->payment_reminder_sent_at)->greaterThanOrEqualTo(Carbon::parse($admisi->step_start_at))) {
                    $this->info("Reminder already sent today / for this step for Lead: {$lead->lead_name} (ID: {$lead->id})");
                    continue;
                }

                $this->info("Sending reminder to Lead: {$lead->lead_name} (ID: {$lead->id})");

                // 1. Send Email
                try {
                    Mail::to($lead->email)->send(new PaymentReminder($lead));
                    $this->info("Email sent to {$lead->email}");
                } catch (\Throwable $e) {
                    Log::error("Failed to send payment reminder email to {$lead->email}: " . $e->getMessage());
                    $this->error("Failed to send email to {$lead->email}");
                }

                // 2. Send Telegram (if telegram_chat_id is set)
                if ($lead->telegram_chat_id) {
                    try {
                        $token = env('TELEGRAM_BOT_TOKEN');
                        $messageText = "Halo *{$lead->lead_name}*! 👋\n\n" .
                            "Hari ini adalah batas waktu (deadline) pembayaran biaya pendaftaran & kuliah kamu di UKRIDA dengan nomor registrasi *{$lead->no_registrasi}*.\n\n" .
                            "Mohon segera melakukan pembayaran dan konfirmasi melalui sistem pendaftaran agar status pendaftaranmu tetap aktif dan tidak hangus.\n\n" .
                            "Untuk melakukan pembayaran, silakan akses:\n" .
                            "👉 [Sistem Pendaftaran UKRIDA](https://register.ukrida.ac.id/admisi/public/register/register/registerEmail)\n\n" .
                            "Jika kamu sudah melakukan pembayaran, mohon abaikan pesan ini. Terima kasih! ✨";

                        $response = Http::withoutVerifying()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                            'chat_id' => $lead->telegram_chat_id,
                            'text' => $messageText,
                            'parse_mode' => 'Markdown',
                        ]);

                        if ($response->successful()) {
                            $this->info("Telegram message sent to chat ID {$lead->telegram_chat_id}");
                            
                            // Save to chat_messages as outbound
                            ChatMessage::create([
                                'lead_id' => $lead->id,
                                'message_text' => $messageText,
                                'direction' => 'outbound',
                            ]);
                        } else {
                            Log::error("Failed to send Telegram message to chat ID {$lead->telegram_chat_id}: " . $response->body());
                            $this->error("Failed to send Telegram message to {$lead->telegram_chat_id}");
                        }
                    } catch (\Throwable $e) {
                        Log::error("Telegram API Error for chat ID {$lead->telegram_chat_id}: " . $e->getMessage());
                        $this->error("Telegram API Error: " . $e->getMessage());
                    }
                } else {
                    $this->info("No Telegram chat ID found for Lead: {$lead->lead_name}");
                }

                // Update payment_reminder_sent_at
                $lead->update([
                    'payment_reminder_sent_at' => now(),
                ]);

                $count++;
            }
        }

        $this->info("Process completed. {$count} reminders sent.");
    }
}
