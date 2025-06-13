<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DeviceOnlineOverview extends BaseWidget
{
    protected function getHeading(): ?string
    {
        return 'Device Online Overview';
    }

    protected function getDescription(): ?string
    {
        return 'Overview of online mikrotik devices in the network';
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Fakultas Ilmu Komputer', 'Mikrotik LT1')
                ->description('Online')
                ->color('success')
                ->descriptionIcon('heroicon-o-check-circle')
                ->icon('heroicon-o-building-office-2'),
            Stat::make('Gedung Kuliah Bersama', 'Mikrotik LT1')
                ->description('Online')
                ->color('success')
                ->descriptionIcon('heroicon-o-check-circle')
                ->icon('heroicon-o-building-office'),
            Stat::make('Gedung Kuliah Bersama', 'Mikrotik LT2')
                ->description('Online')
                ->color('success')
                ->descriptionIcon('heroicon-o-check-circle')
                ->icon('heroicon-o-building-office'),
            Stat::make('Gedung Kuliah Bersama', 'Mikrotik LT3')
                ->description('Online')
                ->color('success')
                ->descriptionIcon('heroicon-o-check-circle')
                ->icon('heroicon-o-building-office'),
        ];
    }
}