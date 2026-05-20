<?php

namespace App\Filament\Resources\MarketingMaterialResource\Pages;

use App\Filament\Resources\MarketingMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMarketingMaterial extends EditRecord
{
    protected static string $resource = MarketingMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
