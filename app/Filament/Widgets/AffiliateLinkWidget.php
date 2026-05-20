<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\Widget;

class AffiliateLinkWidget extends Widget
{

    protected static string $view = 'filament.widgets.affiliate-link-widget';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        // Gembok otomatis: Widget ini HANYA boleh dirender kalau rolenya 'affiliate'
        return auth()->user()?->role === 'affiliate';
    }

    public function getViewData(): array
    {
        return [
            'user' => auth()->user(),
        ];
    }
}
