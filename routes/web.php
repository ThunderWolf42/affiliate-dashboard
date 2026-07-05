<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AffiliateJoinController;
use App\Http\Controllers\WebPushSubscriptionController;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;


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

Route::get('/test-webpush', function () {
    $user = \App\Models\User::find(1);

    $user->notify(
        new \App\Notifications\TelegramChatWebPushNotification(
            \App\Models\Lead::first(),
            \App\Models\ChatMessage::latest()->first()
        )
    );

    return 'sent';
});

Route::get('/test-fcm', function () {

    $scopes = [
        'https://www.googleapis.com/auth/firebase.messaging'
    ];

    $credentials = new ServiceAccountCredentials(
        $scopes,
        storage_path('app/firebase-service-account.json')
    );

    $accessToken = $credentials->fetchAuthToken()['access_token'];

    $response = Http::withToken($accessToken)
        ->post(
            'https://fcm.googleapis.com/v1/projects/ukrida-affiliate-dashboard/messages:send',
            [
                'message' => [
                    'token' => 'e_vlnGrmnC1Me6P8PxNsYT:APA91bFKmRI017nt1ZKS08P6ao5EenCxPiDpnorYkN7zPFDs8aMwz74gsmedLyZN_typZ6kW0U6lL3mGE9jqtke9vaYc9TFLsqabhY1y7oJH8q1My9jErd4',
                    'notification' => [
                        'title' => 'Tes FCM',
                        'body' => 'Halo Darrel 🚀',
                    ],
                ],
            ]
        );

    dd($response->json());
});

Route::post('/save-fcm-token', function (Request $request) {

    Log::info('SAVE FCM REQUEST', [
        'user_id' => auth()->id(),
        'all' => $request->all(),
    ]);

    $user = auth()->user();

    $user->fcm_token = $request->input('token');
    $user->save();

    return response()->json([
        'success' => true
    ]);
})->middleware('auth');






