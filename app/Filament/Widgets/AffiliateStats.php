<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AffiliateStats extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        // --------------------------------------------------------------------------
        // JALUR SKENARIO 1: JIKA YANG LOGIN ADALAH ADMIN MARKETING (GLOBAL STATS)
        // --------------------------------------------------------------------------
        if ($user->role === 'admin') {

            // Hitung total dari seluruh leads yang ada di database tanpa filter user_id
            $totalLeadsGlobal = \App\Models\Lead::count();
            $totalMatchGlobal = \App\Models\Lead::whereNotNull('no_registrasi')->count();

            // Hitung total seluruh reward terkumpul dari semua user affiliate
            $totalRewardGlobal = \App\Models\User::where('role', 'affiliate')->get()->sum('total_reward') ?? 0;

            return [
                Stat::make(
                    'Total Seluruh Camaba (Global)',
                    $totalLeadsGlobal
                )
                    ->description('Total calon mahasiswa dari semua affiliate')
                    ->chart([5, 8, 12, 18, 25])
                    ->color('info'),

                Stat::make(
                    'Total Terdaftar Match (Global)',
                    $totalMatchGlobal
                )
                    ->description('Semua camaba yang sudah masuk admisi')
                    ->color('success'),

                Stat::make('Total Reward Keluar', function () use ($totalRewardGlobal) {
                    return 'Rp ' . number_format($totalRewardGlobal, 0, ',', '.');
                })
                    ->description('Akumulasi dana komisi untuk seluruh affiliate')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('danger'), // Diberi warna merah/danger karena statusnya kas keluar bagi admin
            ];
        }

        // --------------------------------------------------------------------------
        // JALUR SKENARIO 2: JIKA YANG LOGIN ADALAH MAHASISWA AFFILIATE (PRIVATE STATS)
        // --------------------------------------------------------------------------
        return [
            Stat::make(
                'Total Teman yang Diajak',
                \App\Models\Lead::where('user_id', $user->id)->count()
            )
                ->description('Berdasarkan klik link affiliate')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('info'),

            Stat::make(
                'Status Terdaftar (Match)',
                \App\Models\Lead::where('user_id', $user->id)->whereNotNull('no_registrasi')->count()
            )
                ->description('Sudah masuk sistem admisi')
                ->color('success'),

            Stat::make('Reward Terkumpul', function () use ($user) {
                $amount = $user->total_reward ?? 0;
                return 'Rp ' . number_format($amount, 0, ',', '.');
            })
                ->description('Komisi dari pendaftar yang lunas Pembayaran Uang Pangkal')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
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
