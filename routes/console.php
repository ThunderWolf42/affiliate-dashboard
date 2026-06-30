<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


// Scheduler harian dihapus.
// Reminder sekarang dikirim secara event-driven via AdmisiRegistrationObserver
// yang men-dispatch SendPaymentReminderJob tepat saat H-0 deadline pembayaran.
// Untuk test manual, gunakan: php artisan reminders:send

