<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Services\ZabbixApiService;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\ReportResource;

class Report extends Page
{
    protected static string $resource = ReportResource::class;

    protected static string $view = 'filament.resources.report-resource.pages.report';

    public $bandwidthData = [];
    public array $devices = [];
    public array $pingData = [];

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

        $hostIds = array_column($hosts, 'hostid');
        $interfaces = $zabbix->getHostInterfaces($hostIds);

        // Gabungkan data host dan interface
        $interfaceMap = [];
        foreach ($interfaces as $iface) {
            $interfaceMap[$iface['hostid']] = $iface;
        }

        foreach ($hosts as $host) {
            $iface = $interfaceMap[$host['hostid']] ?? null;

            $gedung = '-';
            $lantai = '-';
            if (!empty($host['tags'])) {
                foreach ($host['tags'] as $tag) {
                    if (strtolower($tag['tag']) === 'gedung') {
                        $gedung = $tag['value'];
                        break;
                    }
                }
            }
            if (!empty($host['tags'])) {
                foreach ($host['tags'] as $tag) {
                    if (strtolower($tag['tag']) === 'lantai') {
                        $lantai = $tag['value'];
                        break;
                    }
                }
            }

            // Ambil status SNMP availability
            $snmpAvailable = '-';
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
                            'selectTriggers' => 'extend',
                        ],
                        'id' => 10,
                        'auth' => $authToken,
                    ],
                ]);
                $snmpItems = json_decode($snmpResponse->getBody()->getContents(), true)['result'] ?? [];
                if (!empty($snmpItems)) {
                    // Ambil value terakhir dari history
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
                    if (!empty($snmpHistory)) {
                        $snmpAvailable = $snmpHistory[0]['value'] == 1 ? 'Available' : 'Unavailable';
                    }
                }
            } catch (\Exception $e) {
                $snmpAvailable = '-';
            }

            $this->devices[] = [
                'gedung' => $gedung ?? '-',
                'lantai' => $lantai ?? '-',
                'nama' => $host['name'] ?? $host['host'],
                'ip' => $iface['ip'] ?? '-',
                // 'status' => $host['status'] == 1 ? 'Up' : 'Down',
                'status' => $snmpAvailable,
                'last_down' => $host['error'] ?? '-',
            ];
        }

        foreach ($hosts as $host) {
            $hostId = $host['hostid'];

            // Ambil item icmppingsec (latency)
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
                        'search' => ['key_' => 'icmppingsec'],
                    ],
                    'id' => 1,
                    'auth' => $authToken,
                ],
            ]);
            $latencyItems = json_decode($response->getBody()->getContents(), true)['result'] ?? [];

            // Ambil item icmpping (ping status)
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
                        'search' => ['key_' => 'icmpping'],
                    ],
                    'id' => 2,
                    'auth' => $authToken,
                ],
            ]);
            $pingItems = json_decode($response->getBody()->getContents(), true)['result'] ?? [];

            // Ambil history latency (limit 20 data terakhir)
            $latencyHistory = [];
            if (!empty($latencyItems)) {
                $latencyItemId = $latencyItems[0]['itemid'];
                $historyResponse = $client->request('POST', $zabbix->getUrl(), [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'jsonrpc' => '2.0',
                        'method' => 'history.get',
                        'params' => [
                            'output' => 'extend',
                            'history' => 0, // float
                            'itemids' => [$latencyItemId],
                            'sortfield' => 'clock',
                            'sortorder' => 'DESC',
                            'limit' => 20,
                        ],
                        'id' => 3,
                        'auth' => $authToken,
                    ],
                ]);
                $latencyHistory = array_reverse(json_decode($historyResponse->getBody()->getContents(), true)['result'] ?? []);
            }

            // Ambil history ping (limit 20 data terakhir)
            $pingHistory = [];
            if (!empty($pingItems)) {
                $pingItemId = $pingItems[0]['itemid'];
                $historyResponse = $client->request('POST', $zabbix->getUrl(), [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'jsonrpc' => '2.0',
                        'method' => 'history.get',
                        'params' => [
                            'output' => 'extend',
                            'history' => 3, // unsigned
                            'itemids' => [$pingItemId],
                            'sortfield' => 'clock',
                            'sortorder' => 'DESC',
                            'limit' => 20,
                        ],
                        'id' => 4,
                        'auth' => $authToken,
                    ],
                ]);
                $pingHistory = array_reverse(json_decode($historyResponse->getBody()->getContents(), true)['result'] ?? []);
            }

            // Hitung rata-rata dan maksimum latency
            $latencies = array_map(fn($h) => (float)$h['value'], $latencyHistory);
            $avgLatency = $latencies ? round(array_sum($latencies) / count($latencies), 3) : null;
            $maxLatency = $latencies ? max($latencies) : null;

            $this->pingData[] = [
                'device' => $host['name'] ?? $host['host'],
                'latency_history' => $latencyHistory,
                'ping_history' => $pingHistory,
                'avg_latency' => $avgLatency,
                'max_latency' => $maxLatency,
            ];
        }
    }
}
