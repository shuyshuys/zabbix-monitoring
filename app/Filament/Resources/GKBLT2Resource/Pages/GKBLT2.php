<?php

namespace App\Filament\Resources\GKBLT2Resource\Pages;

use App\Filament\Resources\GKBLT2Resource\Widgets\LinkStatusChart;
use App\Filament\Resources\GKBLT2Resource\Widgets\TracerouteWidget;
use App\Filament\Resources\GKBLT2Resource;
use App\Filament\Resources\GKBLT2Resource\Widgets\CpuChart;
use App\Filament\Resources\GKBLT2Resource\Widgets\IcmpPingChart;
use App\Filament\Resources\GKBLT2Resource\Widgets\MemoryChart;
use App\Filament\Resources\GKBLT2Resource\Widgets\DhcpLeaseCountWidgets;
use App\Filament\Resources\GKBLT2Resource\Widgets\InterfaceEther1Chart;
use App\Filament\Resources\GKBLT2Resource\Widgets\InterfaceEther2Chart;
use App\Filament\Resources\GKBLT2Resource\Widgets\InterfaceEther3Chart;
use App\Filament\Resources\GKBLT2Resource\Widgets\InterfaceEther4Chart;
use App\Filament\Resources\GKBLT2Resource\Widgets\InterfaceEther5Chart;
use App\Filament\Resources\GKBLT2Resource\Widgets\InterfaceEther6Chart;
use App\Filament\Resources\GKBLT2Resource\Widgets\InterfaceEther7Chart;
use App\Filament\Resources\GKBLT2Resource\Widgets\InterfaceEther8Chart;
use Filament\Resources\Pages\Page;

class GKBLT2 extends Page
{
    protected static string $resource = GKBLT2Resource::class;

    protected static string $view = 'filament.resources.g-k-b-l-t2-resource.pages.g-k-b-l-t2';

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
            InterfaceEther3Chart::class,
            InterfaceEther4Chart::class,
            InterfaceEther5Chart::class,
            InterfaceEther6Chart::class,
            InterfaceEther7Chart::class,
            InterfaceEther8Chart::class,
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
