<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AffiliateJoinController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/join/{affiliate_code}', [AffiliateJoinController::class, 'index'])->name('affiliate');// ini buat halaman join berdasarkan link affiliate

Route::post('/join/store', [App\Http\Controllers\AffiliateJoinController::class, 'store'])->name('store');// ini buat proses penyimpanan data join dari form yang ada di halaman join




