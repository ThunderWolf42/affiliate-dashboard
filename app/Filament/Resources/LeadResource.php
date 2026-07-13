<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers;
use App\Filament\Resources\MarketingMaterialResource\Pages as MarketingMaterialPages;
use Carbon\CarbonInterface;
use Carbon\Constants\DiffOptions;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;



class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('3s')
            ->columns([

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Aff.')
                    ->searchable()
                    ->size('xs')
                    ->limit(15)
                    ->visible(fn() => auth()->user()?->role === 'admin')
                    ->formatStateUsing(
                        fn($record) =>
                        $record->user?->role === 'affiliate'
                        ? $record->user?->name
                        : '-'
                    ),

                Tables\Columns\TextColumn::make('lead_name')
                    ->label('Nama')
                    ->searchable()
                    ->size('xs')
                    ->limit(20),

                Tables\Columns\TextColumn::make('no_registrasi')
                    ->label('Reg.')
                    ->placeholder('-')
                    ->badge()
                    ->size('xs')
                    ->color(fn($state) => $state ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('admisiRegistration.refTahapan.step_name')
                    ->label('Tahap')
                    ->size('xs')
                    ->placeholder('Pending')
                    ->wrap()
                    ->color(
                        fn($record) =>
                        (int) $record->admisiRegistration?->current_step === 4
                        ? 'success'
                        : 'gray'
                    ),

                Tables\Columns\TextColumn::make('batch_number')
                    ->label('Batch')
                    ->badge()
                    ->size('xs')
                    ->sortable()
                    ->getStateUsing(
                        fn($record) =>
                        "B{$record->batch_number}-{$record->batch_year}"
                    )
                    ->color('info'),

                Tables\Columns\TextColumn::make('admisiRegistration.total_dibayar')
                    ->label('Bayar')
                    ->money('IDR')
                    ->size('xs'),

                Tables\Columns\TextColumn::make('status_sgs.persen')
                    ->label('%')
                    ->alignCenter()
                    ->size('xs')
                    ->getStateUsing(
                        fn($record) =>
                        $record->status_sgs['persen']
                    ),



                Tables\Columns\TextColumn::make('countdown_h7')
                    ->label('Deadline')
                    ->badge()
                    ->size('xs')
                    ->getStateUsing(function ($record) {

                        $admisi = $record->admisiRegistration;

                        if (!$admisi || (int) $admisi->current_step !== 4) {
                            return 'Tunggu';
                        }

                        if (!$admisi->step_start_at) {
                            return '-';
                        }

                        $tglMulaiTahap = \Carbon\Carbon::parse(
                            $admisi->step_start_at
                        );

                        $batasHari =
                            $admisi->refTahapan?->sla_days ?? 7;

                        $deadline = $tglMulaiTahap->copy()->addDays($batasHari);

                        // $sisaHari = now()->diffInDays($deadline, false);
                        $sisaHari = (int) ceil(now()->diffInDays($deadline, false));

                        if ($sisaHari < 0) {
                            return "Lewat " . abs($sisaHari) . "H";
                        }

                        if ($sisaHari == 0) {
                            return "Hari Ini";
                        }

                        return "H-{$sisaHari}";
                    })

                    ->color(fn($state) => match (true) {
                        str_contains($state, 'Lewat') => 'danger',
                        str_contains($state, 'Hari Ini') => 'warning',
                        str_contains($state, 'H-') => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('kondisi_urgent')
                    ->label('Urgent')
                    ->badge()
                    ->size('xs')
                    ->getStateUsing(
                        fn($record) =>
                        $record->status_sgs['is_urgent']
                        ? 'Ya'
                        : 'Tidak'
                    )
                    ->color(
                        fn($state) =>
                        $state === 'Ya'
                        ? 'danger'
                        : 'success'
                    ),

                Tables\Columns\TextColumn::make('reward_status_sgs')
                    ->label('Reward')
                    ->badge()
                    ->size('xs')
                    ->wrap()
                    ->getStateUsing(function ($record) {

                        $status =
                            $record->status_sgs['reward_status'];

                        return match ($status) {

                            'Sah (Bisa Cair)' => 'Cair',

                            'Sah (Cair - Refund Case)' => 'Refund',

                            'Menunggu Pelunasan (Min 20%)'
                            => 'Tunggu 20%',

                            'Belum Layak'
                            => 'Belum',

                            default => $status,
                        };
                    })
                    ->color(fn($state) => match ($state) {

                        'Cair',
                        'Refund'
                        => 'success',

                        'Tunggu 20%'
                        => 'warning',

                        'Belum'
                        => 'gray',

                        default => 'gray',
                    }),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'pending' => 'Pending',
                        'drop-out' => 'Batal',
                    ]),

                Tables\Filters\SelectFilter::make('batch_number')
                    ->label('Gel.')
                    ->options([
                        1 => 'Batch 1 Tahun 2026',
                        2 => 'Batch 2 Tahun 2026',
                    ]),
            ])
            ->actions([
                // Tables\Actions\EditAction::make()
                //     ->visible(fn() => auth()->check() && auth()->user()->role === 'admin'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }



    // Tambahkan ini supaya Mahasiswa cuma bisa liat Lead-nya sendiri
    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        // Jika yang login adalah Admin, buka akses gembok (Bisa lihat semua data leads global)
        if ($user?->role === 'admin') {
            return parent::getEloquentQuery();
        }

        // Jika yang login adalah Mahasiswa Affiliate, batasi data miliknya sendiri saja
        return parent::getEloquentQuery()->where('user_id', $user?->id);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        // Admin dan Affiliate sama-sama berhak melihat menu data Leads ini
        return in_array(auth()->user()?->role, ['admin', 'affiliate']);
    }
}
