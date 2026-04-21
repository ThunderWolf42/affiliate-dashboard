<?php

namespace App\Filament\Resources\RefTahapanResource\Pages;

use App\Filament\Resources\RefTahapanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRefTahapans extends ListRecords
{
    protected static string $resource = RefTahapanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
