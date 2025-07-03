<?php

namespace App\Filament\Resources\GKBLT1Resource\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;
use App\Services\ZabbixApiService;

class DhcpLeaseCountWidgets extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = '180s';

    protected function getStats(): array
    {
        $hostName = 'mikrotik-gkb-lt1';

        $zabbix = new ZabbixApiService();
        $stats = $zabbix->getDhcpLeaseAndUptimeStats($hostName);

        $formatDuration = function ($seconds) {
            $days = floor($seconds / 86400);
            $h = floor(($seconds % 86400) / 3600);
            $m = floor(($seconds % 3600) / 60);
            $s = $seconds % 60;
            return sprintf('%d days, %02d:%02d:%02d', $days, $h, $m, $s);
        };

        return [
            Stat::make('Active Leases', $stats['activeLeases'])
                ->description('Jumlah DHCP aktif')
                ->color('info'),
            Stat::make('Uptime (Device)', $formatDuration($stats['uptimeSeconds']))
                ->description('Uptime perangkat (system.hw.uptime)')
                ->color('success'),
            Stat::make('Uptime (Network)', $formatDuration($stats['netUptimeSeconds']))
                ->description('Uptime jaringan (system.net.uptime)')
                ->color('info'),
        ];
    }
}
