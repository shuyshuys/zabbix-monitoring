<?php

namespace App\Filament\Resources\GKBLT1Resource\Pages;

use Filament\Resources\Pages\Page;
use App\Filament\Resources\GKBLT1Resource;
use App\Filament\Widgets\MikrotikGkbLt1\CpuChart;
use App\Filament\Widgets\MikrotikGkbLt1\MemoryChart;
use App\Filament\Widgets\MikrotikGkbLt1\IcmpPingChart;
use App\Filament\Widgets\MikrotikGkbLt1\InterfaceCombo1Chart;
use App\Filament\Widgets\MikrotikGkbLt1\InterfaceEther1Chart;
use App\Filament\Widgets\MikrotikGkbLt1\InterfaceEther3Chart;
use App\Filament\Widgets\MikrotikGkbLt1\InterfaceEther4Chart;
use App\Filament\Widgets\MikrotikGkbLt1\InterfaceEther5Chart;
use App\Filament\Widgets\MikrotikGkbLt1\DhcpLeaseCountWidgets;
use App\Filament\Resources\GKBLT1Resource\Widgets\LinkStatusChart;
use App\Filament\Resources\GKBLT1Resource\Widgets\IcmpUpDownPeriodWidget;
use App\Filament\Resources\GKBLT1Resource\Widgets\TracerouteWidget;

class GKBLT1 extends Page
{
    protected static string $resource = GKBLT1Resource::class;

    protected static string $view = 'filament.resources.g-k-b-l-t1-resource.pages.g-k-b-l-t1';

    protected function getHeaderWidgets(): array
    {
        return [
            // DhcpLeaseCountWidgets::class,
            IcmpUpDownPeriodWidget::class,
            TracerouteWidget::class,

            CpuChart::class,
            MemoryChart::class,

            IcmpPingChart::class,
            LinkStatusChart::class,
            InterfaceCombo1Chart::class,
            
            InterfaceEther1Chart::class,
            InterfaceEther3Chart::class,
            InterfaceEther4Chart::class,
            InterfaceEther5Chart::class,
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