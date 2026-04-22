<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers;
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
            ->columns([
                Tables\Columns\TextColumn::make('lead_name')
                    ->label('Calon Mahasiswa')
                    ->searchable(),

                Tables\Columns\TextColumn::make('no_registrasi')
                    ->label('No. Registrasi')
                    ->placeholder('Belum Terdeteksi')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'gray'),

                // Memanggil Nama Tahap (Step Name) dari tabel RefTahapan via AdmisiRegistration
                Tables\Columns\TextColumn::make('admisiRegistration.refTahapan.step_name')
                    ->label('Tahapan Saat Ini')
                    ->placeholder('Pending (Belum Daftar)')
                    ->description(fn($record) => $record->no_registrasi ? 'Progres Resmi UKRIDA' : null),

                // Kolom Urgensi (SLA)
                Tables\Columns\TextColumn::make('urgency')
                    ->label('Urgensi')
                    ->getStateUsing(function ($record) {
                        if (!$record->admisiRegistration || !$record->admisiRegistration->refTahapan) {
                            return 'Normal';
                        }

                        $start = \Carbon\Carbon::parse($record->admisiRegistration->step_start_at);
                        $days = $start->diffInDays(now());
                        $sla = $record->admisiRegistration->refTahapan->sla_days;

                        return $days > $sla ? 'High Priority (Stalled)' : 'Normal';
                    })
                    ->badge()
                    ->color(fn($state) => $state === 'High Priority (Stalled)' ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'drop-out' => 'danger',
                        default => 'gray',
                    }),

                // Status Reward
                Tables\Columns\IconColumn::make('reward_status')
                    ->label('Reward Cair')
                    ->boolean()
                    ->getStateUsing(function ($record) {
                        // Reward cair jika sudah mencapai step 6 atau lebih
                        return ($record->admisiRegistration?->current_step >= 6);
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'pending' => 'Pending',
                        'drop-out' => 'Batal',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    // Tambahkan ini supaya Mahasiswa cuma bisa liat Lead-nya sendiri
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
