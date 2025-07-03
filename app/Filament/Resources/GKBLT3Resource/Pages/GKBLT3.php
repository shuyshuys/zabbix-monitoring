<?php

namespace App\Filament\Resources\GKBLT3Resource\Pages;

use App\Filament\Resources\GKBLT3Resource;
use App\Filament\Resources\GKBLT3Resource\Widgets\CpuChart;
use App\Filament\Resources\GKBLT3Resource\Widgets\IcmpPingChart;
use App\Filament\Resources\GKBLT3Resource\Widgets\LinkStatusChart;
use App\Filament\Resources\GKBLT3Resource\Widgets\MemoryChart;
use App\Filament\Resources\GKBLT3Resource\Widgets\TracerouteWidget;
use App\Filament\Resources\GKBLT3Resource\Widgets\DhcpLeaseCountWidgets;
use App\Filament\Resources\GKBLT3Resource\Widgets\InterfaceEther1Chart;
use App\Filament\Resources\GKBLT3Resource\Widgets\InterfaceEther2Chart;
use Filament\Resources\Pages\Page;

class GKBLT3 extends Page
{
    protected static string $resource = GKBLT3Resource::class;

    protected static string $view = 'filament.resources.g-k-b-l-t3-resource.pages.g-k-b-l-t3';

    protected function getHeaderWidgets(): array
    {
        return [
            DhcpLeaseCountWidgets::class,
            TracerouteWidget::class,

            CpuChart::class,
            MemoryChart::class,

            IcmpPingChart::class,
            LinkStatusChart::class,

            InterfaceEther1Chart::class,
            InterfaceEther2Chart::class,
        ];
    }

    public function getBreadcrumb(): string
    {
        return 'Dashboard';
    }

    public function getTitle(): string
    {
        return 'Mikrotik GKB Lantai 3 - CRS112-8G-4S-IN';
    }
}
