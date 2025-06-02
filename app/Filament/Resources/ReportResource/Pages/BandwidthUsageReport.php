<?php

namespace App\Filament\Resources\ReportResource\Pages;

use Filament\Pages\Page;
use App\Services\ZabbixApiService;

class BandwidthUsageReport extends Page
{
    // protected static string $resource = ReportResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.resources.report-resource.pages.bandwidth-usage-report';
    // protected static string $view = 'filament.pages.bandwidth-usage-report';
    protected static ?string $navigationLabel = 'Laporan Bandwidth';
    protected static ?string $title = 'Laporan Penggunaan Bandwidth';
    protected static ?string $slug = 'reports/bandwidth-usage';

    public $bandwidthData = [];
    public array $devices = [];

    public function mount()
    {
        $zabbix = new ZabbixApiService();
        $hosts = $zabbix->getHosts();

        $client = new \GuzzleHttp\Client();
        $authToken = $zabbix->getAuthToken();

        $this->bandwidthData = [];

        foreach ($hosts as $host) {
            $hostId = $host['hostid'];
            // Ambil semua item in/out interface
            $response = $client->request('POST', $zabbix->getUrl(), [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'item.get',
                    'params' => [
                        'output' => ['itemid', 'name', 'key_'],
                        'hostids' => $hostId,
                        'search' => ['key_' => 'net.if.in'],
                    ],
                    'id' => 1,
                    'auth' => $authToken,
                ],
            ]);
            $inItems = json_decode($response->getBody()->getContents(), true)['result'] ?? [];

            $response = $client->request('POST', $zabbix->getUrl(), [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'item.get',
                    'params' => [
                        'output' => ['itemid', 'name', 'key_'],
                        'hostids' => $hostId,
                        'search' => ['key_' => 'net.if.out'],
                    ],
                    'id' => 2,
                    'auth' => $authToken,
                ],
            ]);
            $outItems = json_decode($response->getBody()->getContents(), true)['result'] ?? [];

            // Ambil history untuk masing-masing item (limit 20 data terakhir)
            $interfaceData = [];
            foreach (array_merge($inItems, $outItems) as $item) {
                $historyResponse = $client->request('POST', $zabbix->getUrl(), [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'jsonrpc' => '2.0',
                        'method' => 'history.get',
                        'params' => [
                            'output' => 'extend',
                            'history' => 3,
                            'itemids' => [$item['itemid']],
                            'sortfield' => 'clock',
                            'sortorder' => 'DESC',
                            'limit' => 20,
                        ],
                        'id' => 3,
                        'auth' => $authToken,
                    ],
                ]);
                $history = json_decode($historyResponse->getBody()->getContents(), true)['result'] ?? [];
                $interfaceData[] = [
                    'name' => $item['name'],
                    'key' => $item['key_'],
                    'history' => array_reverse($history),
                ];
            }

            $this->bandwidthData[] = [
                'device' => $host['name'] ?? $host['host'],
                'interfaces' => $interfaceData,
            ];
        }
    }
}
