<?php

namespace App\Filament\Resources\GKBLT2Resource\Pages;

use App\Filament\Resources\GKBLT2Resource;
use App\Filament\Widgets\MikrotikGkbLt2\DhcpLeaseCountWidgets;
use Filament\Resources\Pages\Page;

class GKBLT2 extends Page
{
    protected static string $resource = GKBLT2Resource::class;

    protected static string $view = 'filament.resources.g-k-b-l-t2-resource.pages.g-k-b-l-t2';

    protected function getHeaderWidgets(): array
    {
        return [
            DhcpLeaseCountWidgets::class,
        ];
    }

    public function getBreadcrumb(): string
    {
        return 'Graphs';
    }

    public function getTitle(): string
    {
        return 'Dashboard Mikrotik GKB LT2';
    }
}
