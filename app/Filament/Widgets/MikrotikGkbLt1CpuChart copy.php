<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Services\ZabbixApiService;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class MikrotikGkbLt1CpuChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        $zabbixService = new ZabbixApiService();
        $authToken = $zabbixService->getAuthToken();

        // Ganti hostId sesuai kebutuhan, misal 10107
        $hostId = 10667;

        // Ambil data graph dari Zabbix API
        $client = new Client();
        $response = $client->request('POST', 'http://192.168.192.114:8080/api_jsonrpc.php', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'graph.get',
                'params' => [
                    'output' => 'extend',
                    'hostids' => $hostId,
                    'sortfield' => 'name',
                ],
                'id' => 1,
                'auth' => $authToken,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        // Log::info('Graph Data', ['data' => $data]);

        // Contoh: tampilkan nama graph sebagai label, dan graphid sebagai data (dummy)
        $labels = [];
        $datasets = [
            [
                'label' => 'Graph ID',
                'data' => [],
                'borderColor' => '#4CAF50',
                'backgroundColor' => 'rgba(76, 175, 80, 0.2)',
            ]
        ];

        if (!empty($data['result'])) {
            foreach ($data['result'] as $graph) {
                $labels[] = $graph['name'] ?? $graph['graphid'];
                $datasets[0]['data'][] = (int)($graph['graphid'] ?? 0);
            }
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
