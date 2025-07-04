<?php

namespace App\Filament\Resources\FIK2LT1Resource\Widgets;

use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use App\Services\ZabbixApiService;

class CpuChart extends ChartWidget
{
    protected static ?string $heading = 'CPU Usage';
    protected static ?string $pollingInterval = '180s';
    public ?string $filter = '1hour';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $zabbix = new ZabbixApiService();

        // Ganti hostName sesuai kebutuhan resource
        $hostName = 'mikrotik-fik-2';

        $cpu = $zabbix->getCpuHistoryByHost($hostName, $this->filter);

        return [
            'labels' => $cpu['labels'],
            'datasets' => [
                [
                    'label' => $cpu['itemName'],
                    'data' => $cpu['data'],
                    'borderColor' => '#4CAF50',
                    'backgroundColor' => 'rgba(76, 175, 80, 0.2)',
                    'tension' => 0.5,
                    'fill' => true,
                ]
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
    {
        responsive: true,
        scales: {
            y: {
                min:  0,
                max:  100,
                beginAtZero: true,
                ticks: {
                    callback: (value) => value + '%',
                },
            },
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += Math.round(context.raw) + '%';
                        return label;
                    }
                }
            }
        }
    }
    JS);
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
