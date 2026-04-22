<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RefTahapanResource\Pages;
use App\Filament\Resources\RefTahapanResource\RelationManagers;
use App\Models\RefTahapan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RefTahapanResource extends Resource
{
    protected static ?string $model = RefTahapan::class;

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
                Tables\Columns\TextColumn::make('step_number')->sortable(),
                Tables\Columns\TextColumn::make('step_name'),
                Tables\Columns\TextColumn::make('sla_days')->label('SLA (Hari)'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListRefTahapans::route('/'),
            'create' => Pages\CreateRefTahapan::route('/create'),
            'edit' => Pages\EditRefTahapan::route('/{record}/edit'),
        ];
    }
}
