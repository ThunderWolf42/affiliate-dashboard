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
                'Total Teman Diajak',
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

            Stat::make(
                'Saldo Deposit UAA',
                'Rp ' . number_format($user->u_a_a_mahasiswa?->deposit_balance ?? 0, 0, ',', '.')
            )
                ->description('Reward otomatis bayar semesteran')
                ->color('warning'),
        ];
    }
}
