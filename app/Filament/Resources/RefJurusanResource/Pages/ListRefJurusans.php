<?php

namespace App\Filament\Resources\RefJurusanResource\Pages;

use App\Filament\Resources\RefJurusanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRefJurusans extends ListRecords
{
    protected static string $resource = RefJurusanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
