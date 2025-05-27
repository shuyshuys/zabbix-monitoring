<?php

namespace App\Filament\Resources\GKBLT3Resource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Services\ZabbixApiService;

class LinkStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Interface Link Up/Down Status';

    public ?string $filter = '1hour';

    protected static ?string $pollingInterval = '180s';

    protected function getData(): array
    {
        $zabbixService = new ZabbixApiService();
        $authToken = $zabbixService->getAuthToken();
        $client = new \GuzzleHttp\Client();

        // Daftar itemid dan label interface
        $interfaces = [
            '50567' => 'ether1',
            '50566' => 'ether2',
            '50628' => 'ether3',
            '50627' => 'ether4',
            '50625' => 'ether5',
            '50629' => 'ether6',
            '50630' => 'ether7',
            '50626' => 'ether8',
        ];

        [$timeFrom, $timeTill] = ZabbixApiService::getTimeRange($this->filter);

        $labels = [];
        $datasets = [];

        foreach ($interfaces as $itemid => $label) {
            $historyResponse = $client->request('POST', $zabbixService->getUrl(), [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'history.get',
                    'params' => [
                        'output' => 'extend',
                        'history' => 3,
                        'itemids' => [$itemid],
                        'sortfield' => 'clock',
                        'sortorder' => 'DESC',
                        'limit' => 25,
                        'time_from' => $timeFrom,
                        'time_till' => $timeTill,
                    ],
                    'id' => 1,
                    'auth' => $authToken,
                ],
            ]);
            $historyData = json_decode($historyResponse->getBody()->getContents(), true)['result'] ?? [];

            $data = [];
            $labelSet = [];
            foreach ($historyData as $entry) {
                $labelSet[] = date('H:i:s', $entry['clock']);
                $data[] = (int)$entry['value'];
            }
            // Gunakan label dari interface pertama
            if (empty($labels) && !empty($labelSet)) {
                $labels = array_reverse($labelSet);
            }
            $datasets[] = [
                'label' => $label,
                'data' => array_reverse($data),
                'borderColor' => '#' . substr(md5($label), 0, 6),
                'backgroundColor' => 'rgba(0,0,0,0)',
                'stepped' => true, // agar terlihat perubahan status
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
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
            '1hour' => 'Last hour',
            '3hours' => 'Last 3 hours',
            '6hours' => 'Last 6 hours',
            'today' => 'Today',
        ];
    }
}
