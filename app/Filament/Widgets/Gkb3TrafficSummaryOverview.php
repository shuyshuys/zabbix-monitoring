<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Gkb3TrafficSummaryOverview extends BaseWidget
{
    protected function getHeading(): ?string
    {
        return 'Traffic Summary GKB LT1';
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Inbound', '120 Mbps')
                ->description('Total lalu lintas masuk')
                ->icon('heroicon-o-arrow-down-circle')
                ->color('info'),
            Stat::make('Total Outbound', '85 Mbps')
                ->description('Total lalu lintas keluar')
                ->icon('heroicon-o-arrow-up-circle')
                ->color('info'),
            Stat::make('Total Users', '150')
                ->description('Jumlah pengguna aktif')
                ->icon('heroicon-o-users')
                ->color('primary'),
        ];
    }
}