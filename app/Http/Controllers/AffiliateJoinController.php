<?php

namespace App\Http\Controllers;
use App\Models\Lead;
use App\Models\User;

use Illuminate\Http\Request;

class AffiliateJoinController extends Controller
{
    public function index($affiliate_code)
    {
        // Cari siapa pemilik kodenya
        $affiliate = User::where('affiliate_code', $affiliate_code)->firstOrFail();

        return view('affiliate-join', compact('affiliate'));
    }



    public function store(Request $request)
    {
        // Validasi harus diisi agar tidak error SQL saat create
        $request->validate([
            'affiliate_id' => 'required|exists:users,id',
            'lead_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'wa_number' => 'required|string|max:15', // Gunakan string agar tidak kena limit angka matematika
        ]);

        $lead = Lead::create([
            'user_id' => $request->input('affiliate_id'),
            'lead_name' => $request->input('lead_name'), // Sesuaikan dengan key di validate
            'email' => $request->input('email'),
            'wa_number' => $request->input('wa_number'),
            'status' => 'pending',
        ]);

        // 2. Ambil username bot Telegram kamu dari file .env
        $botUsername = env('TELEGRAM_BOT_USERNAME', 'DutaUkrida_bot');

        // 3.REDIRECT KE TELEGRAM BAWA ID LEAD!
        // Hasilnya dinamis: https://t.me/{$botUsername}?start={$lead->id}
        return redirect()->away("https://t.me/{$botUsername}?start={$lead->id}");
    }
}
