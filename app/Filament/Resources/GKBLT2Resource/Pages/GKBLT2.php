<?php

namespace App\Filament\Resources\GKBLT2Resource\Pages;

use App\Filament\Resources\GKBLT2Resource\Widgets\LinkStatusChart;
use App\Filament\Resources\GKBLT2Resource\Widgets\TracerouteWidget;
use App\Filament\Resources\GKBLT2Resource;
use App\Filament\Resources\GKBLT2Resource\Widgets\CpuChart;
use App\Filament\Resources\GKBLT2Resource\Widgets\IcmpPingChart;
use App\Filament\Resources\GKBLT2Resource\Widgets\MemoryChart;
use App\Filament\Widgets\MikrotikGkbLt2\DhcpLeaseCountWidgets;
use Filament\Resources\Pages\Page;

class GKBLT2 extends Page
{
    protected static string $resource = GKBLT2Resource::class;

    protected static string $view = 'filament.resources.g-k-b-l-t2-resource.pages.g-k-b-l-t2';

    protected function getHeaderWidgets(): array
    {
        return [
            // DhcpLeaseCountWidgets::class,
            TracerouteWidget::class,

            CpuChart::class,
            MemoryChart::class,

            IcmpPingChart::class,
            LinkStatusChart::class,
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