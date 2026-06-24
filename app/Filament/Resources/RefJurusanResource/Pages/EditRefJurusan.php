<?php

namespace App\Filament\Resources\RefJurusanResource\Pages;

use App\Filament\Resources\RefJurusanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRefJurusan extends EditRecord
{
    protected static string $resource = RefJurusanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
