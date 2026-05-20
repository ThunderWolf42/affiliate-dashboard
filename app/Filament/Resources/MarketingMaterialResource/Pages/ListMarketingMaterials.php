<?php

namespace App\Filament\Resources\MarketingMaterialResource\Pages;

use App\Filament\Resources\MarketingMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarketingMaterials extends ListRecords
{
    protected static string $resource = MarketingMaterialResource::class;


    protected function getHeaderActions(): array
    {
        return [
            // Tombol tambah baru di atas tabel HANYA MUNCUL jika yang login adalah admin
            Actions\CreateAction::make()->visible(fn() => auth()->user()?->role === 'admin'),
        ];
    }
}
