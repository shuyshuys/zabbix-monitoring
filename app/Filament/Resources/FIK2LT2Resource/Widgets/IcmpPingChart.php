<?php

namespace App\Filament\Resources\FIK2LT2Resource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Services\ZabbixApiService;

class IcmpPingChart extends ChartWidget
{
    protected static ?string $heading = 'ICMP Ping';
    public ?string $filter = 'today';
    protected static ?string $pollingInterval = '180s';

    protected function getData(): array
    {
        $zabbix = new ZabbixApiService();
        $hostName = 'mikrotik-fik-msi';

        $result = $zabbix->getIcmpPingHistoryByHost($hostName, $this->filter, 50, 15);

        return [
            'labels' => $result['labels'],
            'datasets' => [
                [
                    'label' => 'ICMP Status',
                    'data' => $result['statusValues'],
                    'borderColor' => '#4CAF50',
                    'backgroundColor' => 'rgba(255, 87, 34, 0.2)',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'ICMP Response Time (ms)',
                    'data' => $result['responseTimeValues'],
                    'borderColor' => '#2196F3',
                    'backgroundColor' => 'rgba(33, 150, 243, 0.2)',
                    'yAxisID' => 'y1',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
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
            'week' => 'Last week',
            'month' => 'Last month',
        ];
    }
}
