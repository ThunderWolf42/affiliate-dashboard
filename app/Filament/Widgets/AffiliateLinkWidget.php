<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AffiliateLinkWidget extends Widget
{
    protected static string $view = 'filament.widgets.affiliate-link-widget';

    protected int | string | array $columnSpan = 'full';
}
