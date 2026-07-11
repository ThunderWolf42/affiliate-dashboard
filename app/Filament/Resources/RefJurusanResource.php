<?php
namespace App\Filament\Resources;
use App\Filament\Resources\RefJurusanResource\Pages;
use App\Filament\Resources\RefJurusanResource\RelationManagers;
use App\Models\Ref_Jurusan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
class RefJurusanResource extends Resource
{
    protected static ?string $model = Ref_Jurusan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('nama_jurusan')
                    ->label('Nama Jurusan')
                    ->required(),

                Forms\Components\TextInput::make('reward_amount')
                    ->label('Reward')
                    ->numeric()
                    ->required(),

            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_jurusan')
                    ->label('Nama Jurusan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reward_amount')
                    ->label('Reward')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->role === 'admin'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()?->role === 'admin'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canEdit( $record): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRefJurusans::route('/'),
            'create' => Pages\CreateRefJurusan::route('/create'),
            'edit' => Pages\EditRefJurusan::route('/{record}/edit'),
        ];
    }
}
