<?php

namespace App\Filament\Resources\FIK2LT1Resource\Widgets;

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

        $hosts = $zabbixService->getHosts();
        // Log::info('Retrieving hosts from Zabbix API');

        $hostId = null;

        // Find the host ID for "Mikrotik GKB LT1"
        foreach ($hosts as $host) {
            if ($host['host'] === 'mikrotik-fik-2') {
                $hostId = $host['hostid'];
                break;
            }
        }

        // Ambil semua item dengan key_ mengandung 'net.if.status[ifOperStatus.'
        $response = $client->request('POST', $zabbixService->getUrl(), [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'item.get',
                'params' => [
                    'output' => ['itemid', 'name', 'key_'],
                    'hostids' => $hostId,
                    'search' => ['key_' => 'net.if.status[ifOperStatus'],
                ],
                'id' => 1,
                'auth' => $authToken,
            ],
        ]);
        $data = json_decode($response->getBody()->getContents(), true);

        // Susun array itemid dan label interface
        $interfaces = [];
        if (!empty($data['result'])) {
            foreach ($data['result'] as $item) {
                // Contoh label: ether1, ether2, dst, bisa diambil dari name
                if (preg_match('/Interface ([\w\d]+)\(\): Operational status/', $item['name'], $matches)) {
                    $label = $matches[1];
                } else {
                    $label = $item['name'];
                }
                $interfaces[$item['itemid']] = $label;
            }
        }

        // // Daftar itemid dan label interface
        // $interfaces = [
        //     '50320' => 'combo1',
        //     '50324' => 'ether1',
        //     '50323' => 'ether2',
        //     '50321' => 'ether3',
        //     '50325' => 'ether4',
        //     '50326' => 'ether5',
        //     '50322' => 'ether6',
        //     '50319' => 'ether7',
        // ];

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
                $labelSet[] = date('H:i', $entry['clock']);
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
