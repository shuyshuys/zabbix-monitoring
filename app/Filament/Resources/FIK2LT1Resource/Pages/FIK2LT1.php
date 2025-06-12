<?php

namespace App\Filament\Resources\FIK2LT1Resource\Pages;

use Filament\Resources\Pages\Page;
use App\Filament\Resources\FIK2LT1Resource;
use App\Filament\Resources\FIK2LT1Resource\Widgets\CpuChart;
use App\Filament\Resources\FIK2LT1Resource\Widgets\DhcpLeaseCountWidgets;
use App\Filament\Resources\FIK2LT1Resource\Widgets\TracerouteWidget;
use App\Filament\Resources\FIK2LT1Resource\Widgets\IcmpPingChart;
use App\Filament\Resources\FIK2LT1Resource\Widgets\InterfaceEther1Chart;
use App\Filament\Resources\FIK2LT1Resource\Widgets\InterfaceEther2Chart;
use App\Filament\Resources\FIK2LT1Resource\Widgets\LinkStatusChart;
use App\Filament\Resources\FIK2LT1Resource\Widgets\MemoryChart;

class FIK2LT1 extends Page
{
    protected static string $resource = FIK2LT1Resource::class;

    protected static string $view = 'filament.resources.f-i-k2-l-t1-resource.pages.f-i-k2-l-t1';

    protected function getHeaderWidgets(): array
    {
        return [
            DhcpLeaseCountWidgets::class,
            TracerouteWidget::class,

            CpuChart::class,
            MemoryChart::class,

            LinkStatusChart::class,
            IcmpPingChart::class,

            InterfaceEther1Chart::class,
            InterfaceEther2Chart::class,
        ];
    }

    public function getBreadcrumb(): string
    {
        return 'Graphs';
    }

    public function getTitle(): string
    {
        return 'Dashboard Mikrotik FIK2 LT1';
    }
}