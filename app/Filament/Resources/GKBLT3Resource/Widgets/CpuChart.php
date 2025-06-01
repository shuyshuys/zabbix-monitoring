<?php

namespace App\Filament\Resources\GKBLT3Resource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Services\ZabbixApiService;
use Illuminate\Support\Facades\Log;

class CpuChart extends ChartWidget
{
    protected static ?string $heading = 'CPU Usage';

    protected static ?string $pollingInterval = '180s';

    public ?string $filter = 'today';

    protected function getData(): array
    {
        // Log::info('Start Fetching data for Mikrotik GKB LT1 CPU chart');

        $zabbixService = new ZabbixApiService();
        // Log::info('Starting Zabbix API service to fetch host data');

        $authToken = $zabbixService->getAuthToken();
        // Log::info('Fetching auth token from Zabbix API');

        $hosts = $zabbixService->getHosts();
        // Log::info('Retrieving hosts from Zabbix API');

        $client = new \GuzzleHttp\Client();
        // Log::info('Creating Guzzle HTTP client for Zabbix API requests');

        $hostId = null;

        // Find the host ID for "Mikrotik GKB LT1"
        foreach ($hosts as $host) {
            if ($host['host'] === 'mikrotik-gkb-lt3') {
                $hostId = $host['hostid'];
                break;
            }
        }

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
        // Log::info('Host Data: ', $hostData;

        // Log::info('Host Data: ', $hostData);

        // 2. Ambil itemid yang diinginkan (misal, item pertama)
        $items = $hostData['result'][0]['items'] ?? [];

        // Ambil itemid 50343 dari host.get (atau langsung gunakan jika sudah pasti ada)
        $itemId = '50570';
        $itemName = 'CPU utilization (%)';

        // Panggil fungsi untuk mendapatkan rentang waktu berdasarkan filter
        [$timeFrom, $timeTill] = ZabbixApiService::getTimeRange($this->filter);

        // Query history.get untuk itemid 50343
        $historyResponse = $client->request('POST', $zabbixService->getUrl(), [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'history.get',
                'params' => [
                    'output' => 'extend',
                    'history' => 0, // 0 untuk float (CPU utilization biasanya float)
                    'itemids' => $itemId,
                    'sortfield' => 'clock',
                    'sortorder' => 'DESC',
                    'limit' => 100,
                    'time_from' => $timeFrom,
                    'time_till' => $timeTill,
                ],
                'id' => 2,
                'auth' => $authToken,
            ],
        ]);
        $historyData = json_decode($historyResponse->getBody()->getContents(), true)['result'] ?? [];
        // Log::info('History Data: ', $historyData);

        // Siapkan data untuk chart
        $labels = [];
        $data = [];
        foreach ($historyData as $history) {
            $labels[] = date('H:i:s', $history['clock']);
            $data[] = $history['value'];
        }

        return [
            'labels' => array_reverse($labels),
            'datasets' => [
                [
                    'label' => $itemName,
                    'data' => array_reverse($data),
                    'borderColor' => '#4CAF50',
                    'backgroundColor' => 'rgba(76, 175, 80, 0.2)',
                ]
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
