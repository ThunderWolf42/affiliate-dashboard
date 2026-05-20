<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarketingMaterialResource\Pages;
use App\Filament\Resources\MarketingMaterialResource\RelationManagers;
use App\Models\MarketingMaterial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MarketingMaterialResource extends Resource
{
    protected static ?string $model = MarketingMaterial::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\TextInput::make('title')
                    ->label('Judul Materi Pemasaran')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi Singkat')
                    ->rows(3)
                    ->maxLength(65535)
                    ->columnSpanFull(), // Membuat textarea ini mengambil seluruh lebar form

                Forms\Components\TextInput::make('download_link')
                    ->label('Link Unduhan (Google Drive/Dropbox)')
                    ->required()
                    ->url()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Nama Materi Marketing')->searchable(),
                Tables\Columns\TextColumn::make('description')->label('Keterangan')->limit(50),

                // Aksi klik cepat untuk mendownload/membuka link materi
                Tables\Columns\TextColumn::make('download_link')
                    ->label('Aksi')
                    ->formatStateUsing(fn () => 'Buka / Download Materi 📥')
                    ->color('primary')
                    ->url(fn ($record) => $record->download_link, true), // true = open in new tab
            ])
            ->actions([
                // Tombol edit dan delete HANYA MUNCUL jika yang login adalah admin
                Tables\Actions\EditAction::make()->visible(fn () => auth()->user()?->role === 'admin'),
                Tables\Actions\DeleteAction::make()->visible(fn () => auth()->user()?->role === 'admin'),
            ]);
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
            'index' => Pages\ListMarketingMaterials::route('/'),
            'create' => Pages\CreateMarketingMaterial::route('/create'),
            'edit' => Pages\EditMarketingMaterial::route('/{record}/edit'),
        ];
    }
}
