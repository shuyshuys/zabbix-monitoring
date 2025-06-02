<?php

namespace App\Filament\Resources\GKBLT1Resource\Pages;

use Filament\Resources\Pages\Page;
use App\Filament\Resources\GKBLT1Resource;
// use App\Filament\Widgets\MikrotikGkbLt1\DhcpLeaseCountWidgets;

use App\Filament\Resources\GKBLT1Resource\Widgets\CpuChart;
use App\Filament\Resources\GKBLT1Resource\Widgets\MemoryChart;
use App\Filament\Resources\GKBLT1Resource\Widgets\IcmpPingChart;
use App\Filament\Resources\GKBLT1Resource\Widgets\LinkStatusChart;
use App\Filament\Resources\GKBLT1Resource\Widgets\TracerouteWidget;
use App\Filament\Resources\GKBLT1Resource\Widgets\InterfaceCombo1Chart;
use App\Filament\Resources\GKBLT1Resource\Widgets\InterfaceEther1Chart;
use App\Filament\Resources\GKBLT1Resource\Widgets\InterfaceEther2Chart;
use App\Filament\Resources\GKBLT1Resource\Widgets\InterfaceEther3Chart;
use App\Filament\Resources\GKBLT1Resource\Widgets\InterfaceEther4Chart;
use App\Filament\Resources\GKBLT1Resource\Widgets\InterfaceEther5Chart;
use App\Filament\Resources\GKBLT1Resource\Widgets\InterfaceEther6Chart;
use App\Filament\Resources\GKBLT1Resource\Widgets\InterfaceEther7Chart;
use App\Filament\Resources\GKBLT1Resource\Widgets\DhcpLeaseCountWidgets;
use App\Filament\Resources\GKBLT1Resource\Widgets\IcmpUpDownPeriodWidget;

class GKBLT1 extends Page
{
    protected static string $resource = GKBLT1Resource::class;

    protected static string $view = 'filament.resources.g-k-b-l-t1-resource.pages.g-k-b-l-t1';

    protected function getHeaderWidgets(): array
    {
        return [
            IcmpUpDownPeriodWidget::class,
            TracerouteWidget::class,

            CpuChart::class,
            MemoryChart::class,

            LinkStatusChart::class,
            IcmpPingChart::class,
            InterfaceCombo1Chart::class,

            InterfaceEther1Chart::class,
            InterfaceEther2Chart::class,
            InterfaceEther3Chart::class,
            InterfaceEther4Chart::class,
            InterfaceEther5Chart::class,
            InterfaceEther6Chart::class,
            InterfaceEther7Chart::class,
        ];
    }

    public function getBreadcrumb(): string
    {
        return 'Graphs';
    }

    public function getTitle(): string
    {
        return 'Dashboard Mikrotik GKB LT1';
    }
}
