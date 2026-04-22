<?php

namespace App\Http\Controllers;
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

    public function store (Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'affiliate_code' => 'required|exists:users,affiliate_code',
        ]);

        
        // Cari affiliate berdasarkan kode

        $affiliate = User::where('affiliate_code', $request->affiliate_code)->firstOrFail();

        // Simpan data join (misalnya di tabel terpisah atau log)
        // Contoh: AffiliateJoin::create([...]);

        return redirect()->back()->with('success', 'Anda berhasil bergabung melalui kode affiliate!');
    }
}
