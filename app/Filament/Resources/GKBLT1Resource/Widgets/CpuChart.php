<?php

namespace App\Filament\Resources\GKBLT1Resource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Services\ZabbixApiService;
// use Illuminate\Support\Facades\Log;

class CpuChart extends ChartWidget
{
    protected static ?string $heading = 'CPU Usage';

    protected static ?string $pollingInterval = '180s';

    public ?string $filter = '1hour';

    protected function getData(): array
    {
        $zabbixService = new ZabbixApiService();
        $authToken = $zabbixService->getAuthToken();
        $hosts = $zabbixService->getHosts();

        $client = new \GuzzleHttp\Client();

        $hostId = null;

        // Find the host ID for "Mikrotik GKB LT1"
        foreach ($hosts as $host) {
            if ($host['host'] === 'mikrotik-gkb-lt1') {
                $hostId = $host['hostid'];
                break;
            }
        }

        // Get host details
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

        // Log::info('Host Data: ', $hostData);

        // 2. Ambil itemid yang diinginkan (misal, item pertama)
        $items = $hostData['result'][0]['items'] ?? [];

        // Ambil itemid 50343 dari host.get (atau langsung gunakan jika sudah pasti ada)
        // $itemId = '50335';
        $itemName = 'CPU utilization (%)';

        // Ambil itemid untuk CPU utilization
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
                    'search' => ['key_' => 'system.cpu.util[hrProcessorLoad.1]'],
                ],
                'id' => 1,
                'auth' => $authToken,
            ],
        ]);
        $data = json_decode($response->getBody()->getContents(), true);

        $itemId = $data['result'][0]['itemid'] ?? null;

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

        // Siapkan data untuk chart
        $labels = [];
        $data = [];
        foreach ($historyData as $history) {
            $labels[] = date('H:i', $history['clock']);
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
