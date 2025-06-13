<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Services\ZabbixApiService;

class DeviceOnlineOverview extends BaseWidget
{
    protected function getHeading(): ?string
    {
        return 'Device Online Overview';
    }

    protected function getDescription(): ?string
    {
        return 'Overview of online mikrotik devices in the network';
    }

    protected function getStats(): array
    {
        $zabbix = new ZabbixApiService();
        $hosts = $zabbix->getHosts();

        $client = new \GuzzleHttp\Client();
        $authToken = $zabbix->getAuthToken();

        $stats = [];

        foreach ($hosts as $host) {
            // Ambil gedung & lantai dari tags
            $gedung = '-';
            $lantai = '-';
            if (!empty($host['tags'])) {
                foreach ($host['tags'] as $tag) {
                    if (strtolower($tag['tag']) === 'gedung') {
                        $gedung = $tag['value'];
                        break;
                    }
                }
                foreach ($host['tags'] as $tag) {
                    if (strtolower($tag['tag']) === 'lantai') {
                        $lantai = $tag['value'];
                        break;
                    }
                }
            }
            // --- SNMP ---
            try {
                $snmpResponse = $client->request('POST', $zabbix->getUrl(), [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'jsonrpc' => '2.0',
                        'method' => 'item.get',
                        'params' => [
                            'output' => 'extend',
                            'hostids' => $host['hostid'],
                            'search' => ['key_' => 'zabbix[host,snmp,available]'],
                        ],
                        'id' => 10,
                        'auth' => $authToken,
                    ],
                ]);
                $snmpItems = json_decode($snmpResponse->getBody()->getContents(), true)['result'] ?? [];
                if (!empty($snmpItems)) {
                    $snmpItemId = $snmpItems[0]['itemid'];
                    $snmpHistoryResponse = $client->request('POST', $zabbix->getUrl(), [
                        'headers' => [
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'jsonrpc' => '2.0',
                            'method' => 'history.get',
                            'params' => [
                                'output' => 'extend',
                                'history' => 3,
                                'itemids' => [$snmpItemId],
                                'sortfield' => 'clock',
                                'sortorder' => 'DESC',
                                'limit' => 1,
                            ],
                            'id' => 11,
                            'auth' => $authToken,
                        ],
                    ]);
                    $snmpHistory = json_decode($snmpHistoryResponse->getBody()->getContents(), true)['result'] ?? [];
                    // ... SNMP logic ...
                    if (!empty($snmpHistory)) {
                        $isOnline = $snmpHistory[0]['value'] == 1;
                        $status = $isOnline ? 'Online (SNMP)' : 'Offline (SNMP)';
                        $color = $isOnline ? 'success' : 'danger';
                        $icon = $isOnline ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle';

                        $stats[] = Stat::make("{$gedung} [{$lantai}]", $host['host'])
                            ->description($status)
                            ->color($color)
                            ->descriptionIcon($icon);
                            // ->icon('heroicon-o-building-office');
                    }
                }
            } catch (\Exception $e) {
                // Optional: handle error
            }

            // --- AGENT ---
            try {
                $agentResponse = $client->request('POST', $zabbix->getUrl(), [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'jsonrpc' => '2.0',
                        'method' => 'item.get',
                        'params' => [
                            'output' => 'extend',
                            'hostids' => $host['hostid'],
                            'search' => ['key_' => 'agent.available'],
                        ],
                        'id' => 12,
                        'auth' => $authToken,
                    ],
                ]);
                $agentItems = json_decode($agentResponse->getBody()->getContents(), true)['result'] ?? [];
                if (!empty($agentItems)) {
                    $agentItemId = $agentItems[0]['itemid'];
                    $agentHistoryResponse = $client->request('POST', $zabbix->getUrl(), [
                        'headers' => [
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'jsonrpc' => '2.0',
                            'method' => 'history.get',
                            'params' => [
                                'output' => 'extend',
                                'history' => 3,
                                'itemids' => [$agentItemId],
                                'sortfield' => 'clock',
                                'sortorder' => 'DESC',
                                'limit' => 1,
                            ],
                            'id' => 13,
                            'auth' => $authToken,
                        ],
                    ]);
                    $agentHistory = json_decode($agentHistoryResponse->getBody()->getContents(), true)['result'] ?? [];
                    // ... Agent logic ...
                    if (!empty($agentHistory)) {
                        $isOnline = $agentHistory[0]['value'] == 1;
                        $status = $isOnline ? 'Online (Agent)' : 'Offline (Agent)';
                        $color = $isOnline ? 'success' : 'danger';
                        $icon = $isOnline ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle';

                        $stats[] = Stat::make("{$gedung} [{$lantai}]", $host['host'])
                            ->description($status)
                            ->color($color)
                            ->descriptionIcon($icon);
                            // ->icon('heroicon-o-computer-desktop');
                    }
                }
            } catch (\Exception $e) {
                // Optional: handle error
            }
        }

        return $stats;
    }
}