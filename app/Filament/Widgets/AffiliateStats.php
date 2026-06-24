<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Lead;

class AffiliateStats extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        // --------------------------------------------------------------------------
        // JALUR SKENARIO 1: JIKA YANG LOGIN ADALAH ADMIN MARKETING (GLOBAL STATS)
        // --------------------------------------------------------------------------
        if ($user->role === 'admin') {

            $totalLeadsGlobal = Lead::count();
            $totalMatchGlobal = Lead::whereNotNull('no_registrasi')->count();

            // 🎯 HITUNG REWARD GLOBAL DINAMIS BERDASARKAN REWARD JURUSAN
            // Menggunakan eager loading 'jurusan' agar query tetap ringan (mencegah N+1 Issue)
            $allLeads = Lead::with('jurusan')->get();
            $totalRewardGlobal = 0;

            foreach ($allLeads as $lead) {
                // Pastikan data status sgs dan relasi jurusannya ada sebelum dihitung
                if (isset($lead->status_sgs['reward_status']) && str_contains($lead->status_sgs['reward_status'], 'Sah')) {
                    // Ambil nominal langsung dari relasi jurusan, jika kosong default ke 0
                    $totalRewardGlobal += $lead->jurusan?->reward_amount ?? 0;
                }
            }

            return [
                Stat::make('Total Seluruh Camaba (Global)', $totalLeadsGlobal)
                    ->description('Total calon mahasiswa dari semua affiliate')
                    ->chart([5, 8, 12, 18, 25])
                    ->color('info'),

                Stat::make('Total Terdaftar Match (Global)', $totalMatchGlobal)
                    ->description('Semua camaba yang sudah masuk admisi')
                    ->color('success'),

                Stat::make('Total Reward Keluar', 'Rp ' . number_format($totalRewardGlobal, 0, ',', '.'))
                    ->description('Akumulasi dana komisi sah untuk seluruh affiliate')
                    ->descriptionIcon('heroicon-m-banknotes')
                    // ->color('danger'), // Warna merah karena kas keluar bagi kampus
            ];
        }


        // Tarik data lead milik user yang login beserta data jurusannya
        $myLeads = Lead::where('user_id', $user->id)->with('jurusan')->get();

        $totalTeman = $myLeads->count();
        $totalMatchPrivate = $myLeads->whereNotNull('no_registrasi')->count();


        $myRewardTerkumpul = 0;
        foreach ($myLeads as $lead) {
            if (isset($lead->status_sgs['reward_status']) && str_contains($lead->status_sgs['reward_status'], 'Sah')) {
                // Tambahkan nominal reward sesuai dengan jurusan target si maba
                $myRewardTerkumpul += $lead->jurusan?->reward_amount ?? 0;
            }
        }

        return [
            Stat::make('Total Teman yang Diajak', $totalTeman)
                ->description('Berdasarkan klik link affiliate')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('info'),

            Stat::make('Status Terdaftar (Match)', $totalMatchPrivate)
                ->description('Sudah masuk sistem admisi')
                ->color('success'),

            Stat::make('Reward Terkumpul', 'Rp ' . number_format($myRewardTerkumpul, 0, ',', '.'))
                ->description('Cair jika total cicilan pembayaran maba >= 20%')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'), // Warna hijau karena rezeki masuk bagi mahasiswa
        ];
    }
}

// namespace App\Filament\Widgets;

// use Filament\Widgets\StatsOverviewWidget as BaseWidget;
// use Filament\Widgets\StatsOverviewWidget\Stat;

// class AffiliateStats extends BaseWidget
// {
//     protected function getStats(): array
//     {
//         $user = auth()->user();

//         if ($user->role === 'admin') {
//             return [
//                 Stat::make(
//                     'Total Teman yang Diajak',
//                     \App\Models\Lead::where('user_id', $user->id)->count()
//                 )
//                     ->description('Berdasarkan klik link affiliate')
//                     ->chart([7, 2, 10, 3, 15, 4, 17])
//                     ->color('info'),

//                 Stat::make(
//                     'Status Terdaftar (Match)',
//                     \App\Models\Lead::where('user_id', $user->id)->whereNotNull('no_registrasi')->count()
//                 )
//                     ->description('Sudah masuk sistem admisi')
//                     ->color('success'),

//                 Stat::make('Reward Terkumpul', function () use ($user) {
//                     // Memanggil accessor 'total_reward' yang dinamis
//                     $amount = $user->total_reward ?? 0;

//                     return 'Rp ' . number_format($amount, 0, ',', '.');
//                 })
//                     ->description('Komisi dari pendaftar yang lunas Pembayaran Uang Pangkal')
//                     ->descriptionIcon('heroicon-m-banknotes')
//                     ->color('success'),


//             ];
//         }
//     }
// }
