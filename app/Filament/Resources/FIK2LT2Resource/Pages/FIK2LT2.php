<?php

namespace App\Filament\Resources\FIK2LT2Resource\Pages;

use Filament\Resources\Pages\Page;
use App\Filament\Resources\FIK2LT2Resource;
use App\Filament\Resources\FIK2LT2Resource\Widgets\CpuChart;
use App\Filament\Resources\FIK2LT2Resource\Widgets\MemoryChart;
use App\Filament\Resources\FIK2LT2Resource\Widgets\IcmpPingChart;
use App\Filament\Resources\FIK2LT2Resource\Widgets\LinkStatusChart;
use App\Filament\Resources\FIK2LT2Resource\Widgets\TracerouteWidget;
use App\Filament\Resources\FIK2LT2Resource\Widgets\InterfaceEther1Chart;
use App\Filament\Resources\FIK2LT2Resource\Widgets\InterfaceEther2Chart;
use App\Filament\Resources\FIK2LT2Resource\Widgets\InterfaceEther3Chart;
use App\Filament\Resources\FIK2LT2Resource\Widgets\InterfaceEther4Chart;
use App\Filament\Resources\FIK2LT2Resource\Widgets\InterfaceEther5Chart;
use App\Filament\Resources\FIK2LT2Resource\Widgets\InterfaceEther6Chart;
use App\Filament\Resources\FIK2LT2Resource\Widgets\InterfaceEther7Chart;
use App\Filament\Resources\FIK2LT2Resource\Widgets\DhcpLeaseCountWidgets;
use App\Filament\Resources\FIK2LT2Resource\Widgets\InterfaceCombo1Chart;

class FIK2LT2 extends Page
{
    protected static string $resource = FIK2LT2Resource::class;

    protected static string $view = 'filament.resources.f-i-k2-l-t2-resource.pages.f-i-k2-l-t2';

    protected function getHeaderWidgets(): array
    {
        return [
            DhcpLeaseCountWidgets::class,
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
        return 'Dashboard';
    }

    public function getTitle(): string
    {
        return 'Mikrotik FIK2 Lantai 2 - CCR1009-7G-1C-1S+';
    }
}
