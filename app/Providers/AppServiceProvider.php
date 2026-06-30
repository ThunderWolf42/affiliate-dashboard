<?php

namespace App\Providers;

use App\Models\Admisi_Registration;
use App\Observers\AdmisiRegistrationObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        if (config('app.env') === 'local') {
            URL::forceScheme('https');
        }

        Gate::policy(\App\Models\User::class, \App\Policies\UserPolicy::class);
        // Register Lead policy only if the class exists to avoid "unknown class" errors
        if (class_exists(\App\Policies\LeadPolicy::class)) {
            Gate::policy(\App\Models\Lead::class, \App\Policies\LeadPolicy::class);
        }

        // Daftarkan Observer: Admisi_Registration
        // Saat current_step berubah ke 4, otomatis dispatch job reminder pembayaran ke H-0
        Admisi_Registration::observe(AdmisiRegistrationObserver::class);
    }




}
