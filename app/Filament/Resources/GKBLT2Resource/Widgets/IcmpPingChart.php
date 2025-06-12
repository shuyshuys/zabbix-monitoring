<?php

namespace App\Filament\Resources\GKBLT2Resource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Services\ZabbixApiService;
use Illuminate\Support\Facades\Log;

class IcmpPingChart extends ChartWidget
{
    protected static ?string $heading = 'ICMP Ping';

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
            if ($host['host'] === 'mikrotik-gkb-lt1') {
                $hostId = $host['hostid'];
                break;
            }
        }

        // Item ID untuk ICMP status dan response time
        // $icmpStatusItemId = '50348';
        // $icmpResponseTimeItemId = '50350';

        // Ambil itemid untuk ICMP status
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
                    'search' => ['key_' => 'icmpping'],
                ],
                'id' => 1,
                'auth' => $authToken,
            ],
        ]);
        $data = json_decode($response->getBody()->getContents(), true);

        $icmpStatusItemId = $data['result'][0]['itemid'] ?? null;

        // Ambil itemid untuk ICMP response time
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
                    'search' => ['key_' => 'icmppingsec'],
                ],
                'id' => 1,
                'auth' => $authToken,
            ],
        ]);
        $data = json_decode($response->getBody()->getContents(), true);

        $icmpResponseTimeItemId = $data['result'][0]['itemid'] ?? null;

        // Panggil fungsi untuk mendapatkan rentang waktu berdasarkan filter
        [$timeFrom, $timeTill] = ZabbixApiService::getTimeRange($this->filter);

        // Ambil data ICMP status
        $statusResponse = $client->request('POST', $zabbixService->getUrl(), [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'history.get',
                'params' => [
                    'output' => 'extend',
                    'history' => 3,
                    'itemids' => $icmpStatusItemId,
                    'sortfield' => 'clock',
                    'sortorder' => 'DESC',
                    'limit' => 25,
                    'time_from' => $timeFrom,
                    'time_till' => $timeTill,
                ],
                'id' => 10,
                'auth' => $authToken,
            ],
        ]);
        $statusData = json_decode($statusResponse->getBody()->getContents(), true)['result'] ?? [];
        // Log::info('ICMP Status Data: ', ['data' => $statusData]);

        // Ambil data ICMP response time
        $responseTimeResponse = $client->request('POST', $zabbixService->getUrl(), [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'history.get',
                'params' => [
                    'output' => 'extend',
                    'history' => 0, // 0 untuk numeric float
                    'itemids' => $icmpResponseTimeItemId,
                    'sortfield' => 'clock',
                    'sortorder' => 'DESC',
                    'limit' => 50,
                    'time_from' => $timeFrom,
                    'time_till' => $timeTill,
                ],
                'id' => 11,
                'auth' => $authToken,
            ],
        ]);
        $responseTimeData = json_decode($responseTimeResponse->getBody()->getContents(), true)['result'] ?? [];
        // Log::info('ICMP Response Time Data: ', ['data' => $responseTimeData]);


        // Siapkan label dan dataset
        $labels = [];
        $statusValues = [];
        $responseTimeValues = [];

        // Sinkronisasi data berdasarkan waktu (clock)
        // Diasumsikan kedua data memiliki urutan clock yang sama
        $count = min(count($statusData), count($responseTimeData));
        for ($i = 0; $i < $count; $i++) {
            $labels[] = date('H:i:s', $statusData[$i]['clock']);
            $statusValues[] = (int)$statusData[$i]['value'];
            $responseTimeValues[] = (float)$responseTimeData[$i]['value'];
        }

        // Reverse agar urutan waktu dari lama ke baru
        $labels = array_reverse($labels);
        $statusValues = array_reverse($statusValues);
        $responseTimeValues = array_reverse($responseTimeValues);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'ICMP Status',
                    'data' => $statusValues,
                    'borderColor' => '#4CAF50',
                    'backgroundColor' => 'rgba(255, 87, 34, 0.2)',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'ICMP Response Time (ms)',
                    'data' => $responseTimeValues,
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