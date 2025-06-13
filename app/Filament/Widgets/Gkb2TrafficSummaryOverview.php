<?php

namespace App\Filament\Widgets;

use App\Services\ZabbixApiService;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class Gkb2TrafficSummaryOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';

    protected function getHeading(): ?string
    {
        return 'Traffic Summary GKB LT2';
    }

    protected function getStats(): array
    {
        $zabbixService = new ZabbixApiService();
        $authToken = $zabbixService->getAuthToken();
        $hosts = $zabbixService->getHosts();

        $hostId = null;
        foreach ($hosts as $host) {
            if ($host['host'] === 'mikrotik-gkb-lt2') {
                $hostId = $host['hostid'];
                break;
            }
        }
        if (!$hostId) {
            return [
                Stat::make('Total Inbound', 'N/A')
                    ->description('Total lalu lintas masuk')
                    ->icon('heroicon-o-arrow-down-circle')
                    ->color('info'),
                Stat::make('Total Outbound', 'N/A')
                    ->description('Total lalu lintas keluar')
                    ->icon('heroicon-o-arrow-up-circle')
                    ->color('info'),
                Stat::make('Total Users', 'N/A')
                    ->description('Jumlah pengguna aktif')
                    ->icon('heroicon-o-users')
                    ->color('primary'),
            ];
        }

        $client = new \GuzzleHttp\Client();

        // Ambil semua item interface dari host.get
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

        $totalInbound = 0;
        $totalOutbound = 0;

        // Ambil Active Leases (Total Users)
        $activeLeases = 'N/A';
        try {
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
                        'search' => ['key_' => 'mtxrDHCPLeaseCount'],
                    ],
                    'id' => 10,
                    'auth' => $authToken,
                ],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            $itemId = $data['result'][0]['itemid'] ?? null;

            if ($itemId) {
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
                            'itemids' => [$itemId],
                            'sortfield' => 'clock',
                            'sortorder' => 'DESC',
                            'limit' => 1,
                        ],
                        'id' => 11,
                        'auth' => $authToken,
                    ],
                ]);
                $historyData = json_decode($historyResponse->getBody()->getContents(), true)['result'] ?? [];
                $activeLeases = $historyData[0]['value'] ?? 'N/A';
            }
        } catch (\Exception $e) {
            // Optional: handle error
        }

        foreach ($items as $item) {
            // Inbound: net.if.in[ifHCInOctets.X]
            if (isset($item['key_']) && str_starts_with($item['key_'], 'net.if.in[ifHCInOctets.')) {
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
                            'itemids' => [$item['itemid']],
                            'sortfield' => 'clock',
                            'sortorder' => 'DESC',
                            'limit' => 1,
                        ],
                        'id' => 2,
                        'auth' => $authToken,
                    ],
                ]);
                $historyData = json_decode($historyResponse->getBody()->getContents(), true)['result'] ?? [];
                if (!empty($historyData)) {
                    // value dalam bit per detik, konversi ke Mbps
                    $totalInbound += $historyData[0]['value'] / 1000000;
                }
            }
            // Outbound: net.if.out[ifHCOutOctets.X]
            if (isset($item['key_']) && str_starts_with($item['key_'], 'net.if.out[ifHCOutOctets.')) {
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
                            'itemids' => [$item['itemid']],
                            'sortfield' => 'clock',
                            'sortorder' => 'DESC',
                            'limit' => 1,
                        ],
                        'id' => 3,
                        'auth' => $authToken,
                    ],
                ]);
                $historyData = json_decode($historyResponse->getBody()->getContents(), true)['result'] ?? [];
                if (!empty($historyData)) {
                    $totalOutbound += $historyData[0]['value'] / 1000000;
                }
            }
        }

        // Format ke 2 desimal
        $totalInbound = number_format($totalInbound, 2) . ' Mbps';
        $totalOutbound = number_format($totalOutbound, 2) . ' Mbps';

        return [
            Stat::make('Total Inbound', $totalInbound)
                ->description('Total lalu lintas masuk')
                ->icon('heroicon-o-arrow-down-circle')
                ->color('info'),
            Stat::make('Total Outbound', $totalOutbound)
                ->description('Total lalu lintas keluar')
                ->icon('heroicon-o-arrow-up-circle')
                ->color('info'),
            Stat::make('Total Users', $activeLeases)
                ->description('Jumlah pengguna aktif')
                ->icon('heroicon-o-users')
                ->color('primary'),
        ];
    }
}
