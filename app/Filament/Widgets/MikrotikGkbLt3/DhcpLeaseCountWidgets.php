<?php

namespace App\Filament\Widgets\MikrotikGkbLt3;

use App\Services\ZabbixApiService;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\Log;

class DhcpLeaseCountWidgets extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $zabbixService = new ZabbixApiService();
        $authToken = $zabbixService->getAuthToken();
        $hosts = $zabbixService->getHosts();

        // Log::info('Auth Token: ', ['authToken' => $authToken]);

        $hostId = null;
        foreach ($hosts as $host) {
            if ($host['host'] === 'mikrotik-gkb-lt3') {
                $hostId = $host['hostid'];
                break;
            }
        }

        if (!$hostId) {
            return [
                Stat::make('Active Leases', 'active_leases')
                    ->label('Active Leases')
                    ->value('0')
                    ->color('success'),
            ];
        }
        $client = new \GuzzleHttp\Client();

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
                'id' => 1,
                'auth' => $authToken,
            ],
        ]);
        $data = json_decode($response->getBody()->getContents(), true);

        // Log::info('DHCP Lease Count Data: ', $data);

        // use history.get to get the last value of the item

        if (empty($data['result'])) {
            return [
                Stat::make('Active Leases', 'active_leases')
                    ->label('Active Leases')
                    ->value('0')
                    ->color('success'),
            ];
        }

        $itemId = $data['result'][0]['itemid'] ?? null;
        // Log::info('DHCP Lease Count Item ID: ', ['itemId' => $itemId]);
        if (!$itemId) {
            return [
                Stat::make('Active Leases', 'active_leases')
                    ->label('Active Leases')
                    ->value('0')
                    ->color('success'),
            ];
        }
        $response = $client->request('POST', $zabbixService->getUrl(), [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'history.get',
                'params' => [
                    'output' => 'extend',
                    'history' => 3, // 0 for float
                    'itemids' => [$itemId],
                    'sortfield' => 'clock',
                    'sortorder' => 'DESC',
                    'limit' => 1,
                ],
                'id' => 2,
                'auth' => $authToken,
            ],
        ]);
        $historyData = json_decode($response->getBody()->getContents(), true)['result'] ?? [];
        // Log::info('DHCP Lease Count History Data: ', $historyData);
        if (empty($historyData)) {
            return [
                Stat::make('Active Leases', 'active_leases')
                    ->label('Active Leases')
                    ->value('0')
                    ->color('success'),
            ];
        }
        // Ambil value dari history.get
        $activeLeases = $historyData[0]['value'] ?? '0';

        return [
            Stat::make('Active Leases', 'active_leases')
                ->label('Active Leases')
                ->value($activeLeases)
                ->color('success'),
        ];
    }
}
