<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             //
    //         ]);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Affiliate')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nim')
                    ->label('NIM')
                    ->sortable(),

                // Mengambil status aktif dari tabel u_a_a__mahasiswas
                Tables\Columns\IconColumn::make('u_a_a_mahasiswa.is_active')
                    ->label('Status Mahasiswa (UAA)')
                    ->boolean() // Menampilkan icon Checklist (Aktif) atau Silang (Tidak Aktif)
                    ->alignCenter()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->color(fn($state) => $state ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('affiliate_code')
                    ->label('Kode Unik')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Daftar')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_reward')
                    ->label('Total Reward')
                    ->money('IDR') // Otomatis memformat jadi Rp 750.000
                    ->sortable(),

            ])
            ->modifyQueryUsing(function (Builder $query) {
                // Hanya tampilkan user dengan role 'affiliate'
                $query->where('role', 'affiliate');
            })

            ;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'),
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    // app/Filament/Resources/UserResource.php

    public static function shouldRegisterNavigation(): bool
    {
        // Hanya tampilkan di sidebar jika usernya adalah Admin
        return auth()->user()->role === 'admin';
    }

    public static function canViewAny(): bool
    {
        // Hanya admin yang bisa melihat menu Users
        return auth()->user()?->role === 'admin';
    }
}
