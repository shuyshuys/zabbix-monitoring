<?php

namespace App\Filament\Widgets\MikrotikFik2Lt1;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;
use App\Services\ZabbixApiService;
use Illuminate\Support\Facades\Log;

class DhcpLeaseCountWidgets extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = '180s';

    protected function getStats(): array
    {
        $zabbixService = new ZabbixApiService();
        $authToken = $zabbixService->getAuthToken();
        $hosts = $zabbixService->getHosts();
        // Log::info('Hosts from Zabbix API', ['hosts' => $hosts]);
        $client = new \GuzzleHttp\Client();

        $hostId = null;
        foreach ($hosts as $host) {
            if ($host['host'] === 'mikrotik-fik-2') {
                $hostId = $host['hostid'];
                Log::info('Found host ID for mikrotik-fik-2', ['hostId' => $hostId]);
                break;
            }
        }
        // if (!$hostId) {
        //     return [
        //         Stat::make('Active Leases', 'active_leases')
        //             ->label('Active Leases')
        //             ->value('10')
        //             ->color('success'),
        //     ];
        // }

        // // ...existing code...
        // if ($hostId) {
        //     // Ambil semua item dari hostid dan print ke log
        //     $allItemsResponse = $client->request('POST', $zabbixService->getUrl(), [
        //         'headers' => [
        //             'Content-Type' => 'application/json',
        //         ],
        //         'json' => [
        //             'jsonrpc' => '2.0',
        //             'method' => 'item.get',
        //             'params' => [
        //                 'output' => ['itemid', 'name', 'key_'],
        //                 'hostids' => $hostId,
        //             ],
        //             'id' => 100,
        //             'auth' => $authToken,
        //         ],
        //     ]);
        //     $allItems = json_decode($allItemsResponse->getBody()->getContents(), true);
        //     \Log::info('All items for hostid ' . $hostId, $allItems['result'] ?? []);
        // }
        // // ...existing code...


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

        // if (empty($data['result'])) {
        //     return [
        //         Stat::make('Active Leases', 'active_leases')
        //             ->label('Active Leases')
        //             ->value('0')
        //             ->color('success'),
        //     ];
        // }

        $itemId = $data['result'][0]['itemid'] ?? null;
        // if (!$itemId) {
        //     return [
        //         Stat::make('Active Leases', 'active_leases')
        //             ->label('Active Leases')
        //             ->value('0')
        //             ->color('success'),
        //     ];
        // }
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
        $activeLeases = $historyData[0]['value'] ?? '0';


        // Ganti dengan itemid ICMP status Anda
        $icmpStatusItemId = '49916';

        // Ambil 1000 data terakhir (atau lebih jika ingin periode lebih panjang)
        $statusResponse = $client->request('POST', $zabbixService->getUrl(), [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'history.get',
                'params' => [
                    'output' => 'extend',
                    'history' => 3, // 3 = numeric unsigned (ICMP status biasanya unsigned)
                    'itemids' => [$icmpStatusItemId],
                    'sortfield' => 'clock',
                    'sortorder' => 'ASC', // Urutkan dari lama ke baru
                    'limit' => 1000,
                ],
                'id' => 1,
                'auth' => $authToken,
            ],
        ]);
        $statusData = json_decode($statusResponse->getBody()->getContents(), true)['result'] ?? [];
        // Log::info('ICMP Status Data: ', $statusData);

        // Hitung periode up/down
        $periods = [];
        $lastStatus = null;
        $lastChange = null;

        foreach ($statusData as $entry) {
            $status = (int)$entry['value'];
            $time = (int)$entry['clock'];

            if ($lastStatus === null) {
                $lastStatus = $status;
                $lastChange = $time;
                continue;
            }

            if ($status !== $lastStatus) {
                $periods[] = [
                    'status' => $lastStatus,
                    'start' => $lastChange,
                    'end' => $time,
                    'duration' => $time - $lastChange,
                ];
                $lastStatus = $status;
                $lastChange = $time;
            }
        }
        // Tambahkan periode terakhir
        if ($lastStatus !== null && $lastChange !== null) {
            $periods[] = [
                'status' => $lastStatus,
                'start' => $lastChange,
                'end' => time(),
                'duration' => time() - $lastChange,
            ];
        }

        // Hitung total up/down
        $totalUp = 0;
        $totalDown = 0;
        foreach ($periods as $period) {
            if ($period['status'] == 1) {
                $totalUp += $period['duration'];
            } else {
                $totalDown += $period['duration'];
            }
        }

        // Konversi ke jam:menit:detik
        $formatDuration = function ($seconds) {
            $h = floor($seconds / 3600);
            $m = floor(($seconds % 3600) / 60);
            $s = $seconds % 60;
            return sprintf('%02d:%02d:%02d', $h, $m, $s);
        };

        return [
            Stat::make('Active Leases', 'active_leases')
                ->description('Jumlah DHCP aktif')
                ->label('Active Leases')
                ->value($activeLeases)
                ->color('info'),
            Stat::make('Total Up', $formatDuration($totalUp))
                ->description('Durasi status UP')
                ->color('success'),
            Stat::make('Total Down', $formatDuration($totalDown))
                ->description('Durasi status DOWN')
                ->color('danger'),
        ];
    }
}
