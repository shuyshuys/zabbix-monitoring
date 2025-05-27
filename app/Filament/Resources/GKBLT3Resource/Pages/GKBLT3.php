<?php

namespace App\Filament\Resources\GKBLT3Resource\Pages;

use App\Filament\Resources\GKBLT3Resource;
use App\Filament\Widgets\MikrotikGkbLt3\DhcpLeaseCountWidgets;
use Filament\Resources\Pages\Page;

class GKBLT3 extends Page
{
    protected static string $resource = GKBLT3Resource::class;

    protected static string $view = 'filament.resources.g-k-b-l-t3-resource.pages.g-k-b-l-t3';

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
        return 'Dashboard Mikrotik GKB LT3';
    }
}
