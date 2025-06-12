<?php

namespace App\Filament\Resources\GKBLT1Resource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Services\ZabbixApiService;
use Illuminate\Support\Facades\Log;

class InterfaceCombo1Chart extends ChartWidget
{
    protected static ?string $heading = 'Interface Combo1 Traffic';

    protected static ?string $pollingInterval = '180s';

    public ?string $filter = '1hour';

    protected function getData(): array
    {
        $activeFilter = $this->filter;

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
                if (str_contains($item['name'], 'Interface combo1(): Bits sent')) {
                    $targetItems['Bits sent'] = $item;
                } elseif (str_contains($item['name'], 'Interface combo1(): Bits received')) {
                    $targetItems['Bits received'] = $item;
                }
            }
        }

        // Panggil fungsi untuk mendapatkan rentang waktu berdasarkan filter
        [$timeFrom, $timeTill] = ZabbixApiService::getTimeRange($this->filter);
        Log::info('Filter', ['filter' => $this->filter]);
        Log::info('Time range', ['from' => $timeFrom, 'till' => $timeTill]);

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
                        'time_from' => $timeFrom,
                        'time_till' => $timeTill,
                    ],
                    'id' => 2,
                    'auth' => $authToken,
                ],
            ]);
            $historyData = json_decode($historyResponse->getBody()->getContents(), true)['result'] ?? [];

            foreach ($historyData as $history) {
                $receivedLabels[] = date('H:i', $history['clock']);
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
                        'time_from' => $timeFrom,
                        'time_till' => $timeTill,
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
