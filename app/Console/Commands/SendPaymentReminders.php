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
            
            $sisaHari = (int) ceil(now()->diffInDays($deadline, false));

            if ($sisaHari === 0) {
                // Check if reminder was already sent for the current stage/step_start_at
                if ($lead->payment_reminder_sent_at && Carbon::parse($lead->payment_reminder_sent_at)->greaterThanOrEqualTo(Carbon::parse($admisi->step_start_at))) {
                    $this->info("Reminder already sent today / for this step for Lead: {$lead->lead_name} (ID: {$lead->id})");
                    continue;
                }

                $this->info("Dispatching reminder job for Lead: {$lead->lead_name} (ID: {$lead->id})");
                \App\Jobs\SendPaymentReminderJob::dispatch($lead->id, $admisi->step_start_at)->onQueue('default');


                $count++;
            }
        }

        $this->info("Process completed. {$count} reminders sent.");
    }
}
