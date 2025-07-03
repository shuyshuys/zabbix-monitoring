<?php

namespace App\Filament\Resources\FIK2LT2Resource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Services\ZabbixApiService;

class LinkStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Interface Link Up/Down Status';
    public ?string $filter = 'today';
    protected static ?string $pollingInterval = '180s';

    protected function getData(): array
    {
        $zabbix = new ZabbixApiService();
        $hostName = 'mikrotik-fik-msi';

        // Data per 30 menit
        $result = $zabbix->getLinkStatusHistoryByHost($hostName, $this->filter, 30);

        return [
            'labels' => $result['labels'],
            'datasets' => $result['datasets'],
            'options' => [
                'scales' => [
                    'y' => [
                        'min' => 0,
                        'max' => 5,
                        'ticks' => [
                            'stepSize' => 1,
                            'callback' => 'function(value) { return value == 1 ? "Up" : (value == 2 ? "Down" : value); }',
                        ],
                    ],
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
            '3hours' => 'Last 3 hours',
            '6hours' => 'Last 6 hours',
            '12hours' => 'Last 12 hours',
            'yesterday' => 'Yesterday',
        ];
    }
}
