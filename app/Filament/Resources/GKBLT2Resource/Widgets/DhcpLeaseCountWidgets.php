<?php

namespace App\Filament\Resources\GKBLT2Resource\Widgets;

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
            if ($host['host'] === 'mikrotik-gkb-lt2') {
                $hostId = $host['hostid'];
                Log::info('Found host ID for mikrotik-gkb-lt2', ['hostId' => $hostId]);
                break;
            } else {
                Log::info('Host not found', ['host' => $host['host']]);
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
        Log::info('Item Data', ['data' => $data]);

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

        // Ambil Uptime Hardware (Total Up)
        $uptimeSeconds = 0;
        try {
            $uptimeItemResponse = $client->request('POST', $zabbixService->getUrl(), [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'item.get',
                    'params' => [
                        'output' => ['itemid', 'name', 'key_'],
                        'hostids' => $hostId,
                        'search' => ['key_' => 'system.hw.uptime[hrSystemUptime.0]'],
                    ],
                    'id' => 20,
                    'auth' => $authToken,
                ],
            ]);
            $uptimeItemData = json_decode($uptimeItemResponse->getBody()->getContents(), true);
            $uptimeItemId = $uptimeItemData['result'][0]['itemid'] ?? null;

            if ($uptimeItemId) {
                $uptimeHistoryResponse = $client->request('POST', $zabbixService->getUrl(), [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'jsonrpc' => '2.0',
                        'method' => 'history.get',
                        'params' => [
                            'output' => 'extend',
                            'history' => 3,
                            'itemids' => [$uptimeItemId],
                            'sortfield' => 'clock',
                            'sortorder' => 'DESC',
                            'limit' => 1,
                        ],
                        'id' => 21,
                        'auth' => $authToken,
                    ],
                ]);
                $uptimeHistoryData = json_decode($uptimeHistoryResponse->getBody()->getContents(), true)['result'] ?? [];
                $uptimeSeconds = isset($uptimeHistoryData[0]['value']) ? (int)$uptimeHistoryData[0]['value'] : 0;
            }
        } catch (\Exception $e) {
            // Optional: handle error
        }

        // Ambil Uptime Network (Total Down)
        $netUptimeSeconds = 0;
        try {
            $netUptimeItemResponse = $client->request('POST', $zabbixService->getUrl(), [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'item.get',
                    'params' => [
                        'output' => ['itemid', 'name', 'key_'],
                        'hostids' => $hostId,
                        'search' => ['key_' => 'system.net.uptime[sysUpTime.0]'],
                    ],
                    'id' => 30,
                    'auth' => $authToken,
                ],
            ]);
            $netUptimeItemData = json_decode($netUptimeItemResponse->getBody()->getContents(), true);
            $netUptimeItemId = $netUptimeItemData['result'][0]['itemid'] ?? null;

            if ($netUptimeItemId) {
                $netUptimeHistoryResponse = $client->request('POST', $zabbixService->getUrl(), [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'jsonrpc' => '2.0',
                        'method' => 'history.get',
                        'params' => [
                            'output' => 'extend',
                            'history' => 3,
                            'itemids' => [$netUptimeItemId],
                            'sortfield' => 'clock',
                            'sortorder' => 'DESC',
                            'limit' => 1,
                        ],
                        'id' => 31,
                        'auth' => $authToken,
                    ],
                ]);
                $netUptimeHistoryData = json_decode($netUptimeHistoryResponse->getBody()->getContents(), true)['result'] ?? [];
                $netUptimeSeconds = isset($netUptimeHistoryData[0]['value']) ? (int)$netUptimeHistoryData[0]['value'] : 0;
            }
        } catch (\Exception $e) {
            // Optional: handle error
        }

        // Konversi ke days, jam:menit:detik
        $formatDuration = function ($seconds) {
            $days = floor($seconds / 86400);
            $h = floor(($seconds % 86400) / 3600);
            $m = floor(($seconds % 3600) / 60);
            $s = $seconds % 60;
            return sprintf('%d days, %02d:%02d:%02d', $days, $h, $m, $s);
        };

        return [
            Stat::make('Active Leases', 'active_leases')
                ->description('Jumlah DHCP aktif')
                ->label('Active Leases')
                ->value($activeLeases)
                ->color('info'),
            Stat::make('Uptime (Device)', $formatDuration($uptimeSeconds))
                ->description('Uptime perangkat (system.hw.uptime)')
                ->color('success'),
            Stat::make('Uptime (Network)', $formatDuration($netUptimeSeconds))
                ->description('Uptime jaringan (system.net.uptime)')
                ->color('info'),
        ];
    }
}
