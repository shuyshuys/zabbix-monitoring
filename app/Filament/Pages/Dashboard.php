<?php

use Filament\Pages\Dashboard as BaseDashboard;
// use App\Filament\Widgets\WebPushSubscribeButton;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            // WebPushSubscribeButton::class,
        ];
    }
}
