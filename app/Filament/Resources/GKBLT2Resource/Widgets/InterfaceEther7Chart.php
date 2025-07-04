<?php

namespace App\Filament\Resources\GKBLT2Resource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Services\ZabbixApiService;

class InterfaceEther7Chart extends ChartWidget
{
    protected static ?string $heading = 'Interface Ether7 Traffic';
    protected static ?string $pollingInterval = '600s';
    public ?string $filter = '1hour';

    protected function getData(): array
    {
        $hostName = 'mikrotik-gkb-lt2';
        $zabbix = new ZabbixApiService();
        return $zabbix->getInterfaceTraffic($hostName, $this->filter, 'ether7');
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getTitle(): string
    {
        return 'Ether7 Traffic Chart';
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            '1hour' => 'Last hour',
            '2hours' => 'Last 2 hours',
            '3hours' => 'Last 3 hours',
            '4hours' => 'Last 4 hours',
            '5hours' => 'Last 5 hours',
            '6hours' => 'Last 6 hours',
            '12hours' => 'Last 12 hours',
            'yesterday' => 'Yesterday',
        ];
    }
}
