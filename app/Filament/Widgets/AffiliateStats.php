<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AffiliateStats extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

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
                // Memanggil accessor 'total_reward' yang dinamis
                $amount = $user->total_reward ?? 0;

                return 'Rp ' . number_format($amount, 0, ',', '.');
            })
                ->description('Komisi dari pendaftar yang lunas Pembayaran Uang Pangkal')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            // Stat::make(
            //     'Potential Reward',
            //     'Rp ' . number_format(
            //         \App\Models\Lead::where('user_id', $user->id)
            //             ->whereNull('no_registrasi')
            //             ->join('ref_jurusans', 'leads.jurusan_id', '=', 'ref_jurusans.id')
            //             ->sum('ref_jurusans.reward_amount'),
            //         0,
            //         ',',
            //         '.'
            //     )
            // )
            //     ->description('Reward otomatis bayar semesteran')
            //     ->color('warning'),
        ];
    }
}
