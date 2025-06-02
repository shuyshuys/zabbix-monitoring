<?php

namespace App\Filament\Resources\GKBLT1Resource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Services\ZabbixApiService;

class InterfaceEther7Chart extends ChartWidget
{
    protected static ?string $heading = 'Interface Ether7 Traffic';

    protected function getData(): array
    {
        $zabbixService = new ZabbixApiService();
        $authToken = $zabbixService->getAuthToken();
        $hosts = $zabbixService->getHosts();

        $hostId = null;
        foreach ($hosts as $host) {
            if ($host['host'] === 'mikrotik-gkb-lt1') {
                $hostId = $host['hostid'];
                break;
            }
        }
        if (!$hostId) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }
        $client = new \GuzzleHttp\Client();

        // Ambil semua item interface combo1 dari host.get
        $hostResponse = $client->request('POST', $zabbixService->getUrl(), [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'host.get',
                'params' => [
                    'output' => ['host'],
                    'sortfield' => 'name',
                    'hostids' => $hostId,
                    'selectItems' => ['itemid', 'name', 'key_'],
                ],
                'id' => 1,
                'auth' => $authToken,
            ],
        ]);
        $hostData = json_decode($hostResponse->getBody()->getContents(), true);
        $items = $hostData['result'][0]['items'] ?? [];

        // Filter item untuk Bits sent dan Bits received saja
        $targetItems = [
            'Bits sent' => null,
            'Bits received' => null,
        ];

        foreach ($items as $item) {
            if (isset($item['name'])) {
                if (str_contains($item['name'], 'Interface ether7(): Bits sent')) {
                    $targetItems['Bits sent'] = $item;
                } elseif (str_contains($item['name'], 'Interface ether7(): Bits received')) {
                    $targetItems['Bits received'] = $item;
                }
            }
        }

        $labels = [];
        $datasets = [];

        // Ambil data Bits received
        $receivedLabels = [];
        $receivedData = [];
        if ($targetItems['Bits received']) {
            $item = $targetItems['Bits received'];
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
                        'itemids' => $item['itemid'],
                        'sortfield' => 'clock',
                        'sortorder' => 'DESC',
                        'limit' => 50,
                    ],
                    'id' => 2,
                    'auth' => $authToken,
                ],
            ]);
            $historyData = json_decode($historyResponse->getBody()->getContents(), true)['result'] ?? [];

            foreach ($historyData as $history) {
                $receivedLabels[] = date('H:i:s', $history['clock']);
                $receivedData[] = $history['value'] / 1000000;
            }
            $datasets[] = [
                'label' => 'Bits received (Mbps)',
                'data' => array_reverse($receivedData),
                'borderColor' => '#4CAF50',
                'backgroundColor' => 'rgba(76, 175, 80, 0.2)',
            ];
        }

        // Ambil data Bits sent
        $sentData = [];
        // Log::info('Target Items', ['targetItems' => $targetItems]);
        // Log::info('timefrom', ['timeFrom' => $timeFrom]);
        if ($targetItems['Bits sent']) {
            $item = $targetItems['Bits sent'];
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
                        'itemids' => $item['itemid'],
                        'sortfield' => 'clock',
                        'sortorder' => 'DESC',
                        'limit' => 50,
                    ],
                    'id' => 3,
                    'auth' => $authToken,
                ],
            ]);
            $historyData = json_decode($historyResponse->getBody()->getContents(), true)['result'] ?? [];
            // Log::info('History Data for Bits sent', ['historyData' => $historyData]);

            foreach ($historyData as $history) {
                $sentData[] = $history['value'] / 1000000;
            }
            $datasets[] = [
                'label' => 'Bits sent (Mbps)',
                'data' => array_reverse($sentData),
                'borderColor' => '#2196F3',
                'backgroundColor' => 'rgba(33, 150, 243, 0.2)',
            ];
        }

        // Gunakan label dari Bits received (atau sent jika received kosong)
        $labels = array_reverse($receivedLabels);
        if (empty($labels) && !empty($sentData)) {
            // Jika tidak ada received, gunakan sent (tapi label harus diisi manual dari sent)
            // Anda bisa menambahkan pengisian label dari sent di sini jika perlu
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
