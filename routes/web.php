<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AffiliateJoinController;
use App\Http\Controllers\WebPushSubscriptionController;



Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/join/{affiliate_code}', [AffiliateJoinController::class, 'index'])->name('affiliate');// ini buat halaman join berdasarkan link affiliate

Route::post('/join/store', [App\Http\Controllers\AffiliateJoinController::class, 'store'])->name('store');// ini buat proses penyimpanan data join dari form yang ada di halaman join

Route::middleware('auth')->group(function () {
    Route::get('/webpush/public-key', [WebPushSubscriptionController::class, 'publicKey'])
        ->name('webpush.public-key');
    Route::post('/webpush/subscribe', [WebPushSubscriptionController::class, 'store'])
        ->name('webpush.subscribe');
    Route::delete('/webpush/subscribe', [WebPushSubscriptionController::class, 'destroy'])
        ->name('webpush.unsubscribe');
});

Route::get(
    '/test-webpush',
    function () {
        $user = \App\Models\User::find(1);

        logger()->info([
            'subs' => $user->pushSubscriptions()->count(),
        ]);

        $user->notify(
            new \App\Notifications\TelegramChatWebPushNotification(
                \App\Models\Lead::first(),
                \App\Models\ChatMessage::latest()->first()
            )
        );

        return 'sent';
    }
);






