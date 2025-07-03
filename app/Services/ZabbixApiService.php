<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ZabbixApiService
{
    protected string $url;
    protected string $username;
    protected string $password;

    /**
     * ZabbixApiService constructor.
     * Initializes the Zabbix API URL, username, and password from environment variables.
     */
    public function __construct()
    {
        $this->url = env('ZABBIX_URL', 'http://127.0.0.1:8080/zabbix/api_jsonrpc.php');
        $this->username = env('ZABBIX_USERNAME', 'Admin');
        $this->password = env('ZABBIX_PASSWORD', 'zabbix');
    }

    /**
     * Get the Zabbix API URL.
     * @return string The Zabbix API URL.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get the authentication token for Zabbix API.
     * This method sends a login request to the Zabbix API and retrieves the authentication token.
     * @return string The authentication token.
     */
    public function getAuthToken(): string
    {
        $client = new Client();

        $loginResponse = $client->request('POST', $this->url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'user.login',
                'params' => [
                    'username' => $this->username,
                    'password' => $this->password,
                ],
                'id' => 1,
            ],
        ]);

        $loginData = json_decode($loginResponse->getBody()->getContents(), true);

        if (!isset($loginData['result'])) {
            Log::error('Failed to login to Zabbix API', [
                'response' => $loginData,
            ]);
            throw new \Exception('Failed to login to Zabbix API');
        }

        return $loginData['result'];
    }

    /**
     * Get all hosts from Zabbix.
     *
     * @return array An array of hosts with their details.
     */
    public function getHosts(): array
    {
        $client = new Client();
        $authToken = $this->getAuthToken();

        $response = $client->request('POST', $this->url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'host.get',
                'params' => [
                    'output' => 'extend',
                    'selectTags' => 'extend',
                ],
                'id' => 2,
                'auth' => $authToken,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['result'] ?? [];
    }

    /**
     * Get the API version.
     *
     * @return string The API version.
     */
    public function getApiVersion(): string
    {
        $client = new Client();
        $authToken = $this->getAuthToken();

        $response = $client->request('POST', $this->url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'apiinfo.version',
                'id' => 3,
                'auth' => $authToken,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['result'] ?? '';
    }

    /**
     * Get time range based on the active filter.
     *
     * @param string $activeFilter The active filter (e.g., 'today', 'yesterday', 'last_week').
     * @return array An array containing the start and end timestamps.
     */
    public static function getTimeRange($activeFilter): array
    {
        $timeFrom = null;
        $timeTill = time();
        switch ($activeFilter) {
            case 'today':
                $timeFrom = strtotime('today');
                break;
            case 'yesterday':
                $timeFrom = strtotime('yesterday');
                $timeTill = strtotime('today');
                break;
            case 'last_week':
                $timeFrom = strtotime('-6 days');
                $timeTill = strtotime('now');
                break;
            case 'last_month':
                $timeFrom = strtotime('-1 month');
                $timeTill = strtotime('now');
                break;
            case 'last_3_month':
                $timeFrom = strtotime('-3 months');
                $timeTill = strtotime('now');
                break;
            case 'last_6_month':
                $timeFrom = strtotime('-6 months');
                $timeTill = strtotime('now');
                break;
            case 'last_year':
                $timeFrom = strtotime('-1 year');
                $timeTill = strtotime('now');
                break;
            case '1hour':
                $timeFrom = strtotime('-1 hour');
                $timeTill = strtotime('now');
                break;
            case '2hours':
                $timeFrom = strtotime('-2 hours');
                $timeTill = strtotime('now');
                break;
            case '3hours':
                $timeFrom = strtotime('-3 hours');
                $timeTill = strtotime('now');
                break;
            case '4hours':
                $timeFrom = strtotime('-4 hours');
                $timeTill = strtotime('now');
                break;
            case '5hours':
                $timeFrom = strtotime('-5 hours');
                $timeTill = strtotime('now');
                break;
            case '6hours':
                $timeFrom = strtotime('-6 hours');
                $timeTill = strtotime('now');
                break;
            case '12hours':
                $timeFrom = strtotime('-12 hours');
                $timeTill = strtotime('now');
                break;
            default:
                $timeFrom = strtotime('today');
        }
        return [$timeFrom, $timeTill];
    }

    /**
     * Get CPU history for a specific host.
     *
     * @param string $hostName The name of the host.
     * @param string $filter Time filter (e.g., '1hour', 'today').
     * @param string $itemKey The item key for CPU usage.
     * @return array An array containing labels, data, and item name.
     */
    public function getCpuHistoryByHost($hostName, $filter = '1hour', $itemKey = 'system.cpu.util[hrProcessorLoad.1]')
    {
        $hostId = $this->getHostIdByName($hostName);
        if (!$hostId) {
            return ['labels' => [], 'data' => [], 'itemName' => 'CPU Usage'];
        }

        $itemId = $this->getItemIdByKey($hostId, $itemKey);
        if (!$itemId) {
            return ['labels' => [], 'data' => [], 'itemName' => 'CPU Usage'];
        }

        [$timeFrom, $timeTill] = self::getTimeRange($filter);

        // Ambil data history (float = 0)
        $history = $this->getHistoryData($itemId, $timeFrom, $timeTill, 0, 100);

        $labels = [];
        $data = [];
        foreach ($history as $row) {
            $labels[] = date('H:i', $row['clock']);
            $data[] = (float)$row['value'];
        }

        // Dapatkan nama item (opsional)
        $itemName = 'CPU Usage';
        if (!empty($history)) {
            $itemName = 'CPU Usage (%)';
        }

        return [
            'labels' => array_reverse($labels),
            'data' => array_reverse($data),
            'itemName' => $itemName,
            'tension' => 0.5,
        ];
    }

    public function getDhcpLeaseAndUptimeStats($hostName)
    {
        $hostId = $this->getHostIdByName($hostName);

        // Active Leases
        $activeLeases = $this->getLastItemValue($hostId, 'mtxrDHCPLeaseCount', 3);

        // Uptime Hardware
        $uptimeSeconds = $this->getLastItemValue($hostId, 'system.hw.uptime[hrSystemUptime.0]', 3);

        // Uptime Network
        $netUptimeSeconds = $this->getLastItemValue($hostId, 'system.net.uptime[sysUpTime.0]', 3);

        return [
            'activeLeases' => $activeLeases,
            'uptimeSeconds' => $uptimeSeconds,
            'netUptimeSeconds' => $netUptimeSeconds,
        ];
    }

    public function getLastItemValue($hostId, $itemKey, $historyType = 3)
    {
        $itemId = $this->getItemIdByKey($hostId, $itemKey);
        if (!$itemId) return 0;

        $history = $this->getHistoryData($itemId, time() - 7 * 24 * 3600, time(), $historyType, 1); // last 7 days, limit 1
        return isset($history[0]['value']) ? (int)$history[0]['value'] : 0;
    }

    // public function getMemoryHistoryByHost($hostName, $filter = '1hour', $itemKey = 'vm.memory.util[memoryUsedPercentage.Memory]')
    // {
    //     $hostId = $this->getHostIdByName($hostName);
    //     if (!$hostId) {
    //         return ['labels' => [], 'data' => [], 'itemName' => 'Memory Usage'];
    //     }

    //     $itemId = $this->getItemIdByKey($hostId, $itemKey);
    //     if (!$itemId) {
    //         return ['labels' => [], 'data' => [], 'itemName' => 'Memory Usage'];
    //     }

    //     [$timeFrom, $timeTill] = self::getTimeRange($filter);

    //     // Ambil data history (float = 0)
    //     $history = $this->getHistoryData($itemId, $timeFrom, $timeTill, 0, 100);

    //     $labels = [];
    //     $data = [];
    //     foreach ($history as $row) {
    //         $labels[] = date('H:i', $row['clock']);
    //         $data[] = (float)$row['value'];
    //     }

    //     // Dapatkan nama item (opsional)
    //     $itemName = 'Memory Usage (%)';

    //     return [
    //         'labels' => array_reverse($labels),
    //         'data' => array_reverse($data),
    //         'itemName' => $itemName,
    //         'tension' => 0.5,
    //     ];
    // }

    public function getInterfaceTraffic($hostName, $filter = '1hour', $etherName = 'ether1')
    {
        $hostId = $this->getHostIdByName($hostName);
        if (!$hostId) {
            return ['labels' => [], 'datasets' => []];
        }

        $client = new \GuzzleHttp\Client();
        $authToken = $this->getAuthToken();

        $hostResponse = $client->request('POST', $this->url, [
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

        // Filter item untuk Bits sent dan Bits received pada etherX
        $targetItems = [
            'Bits sent' => null,
            'Bits received' => null,
        ];

        foreach ($items as $item) {
            if (isset($item['name'])) {
                if (str_contains($item['name'], "Interface {$etherName}(): Bits sent")) {
                    $targetItems['Bits sent'] = $item;
                } elseif (str_contains($item['name'], "Interface {$etherName}(): Bits received")) {
                    $targetItems['Bits received'] = $item;
                }
            }
        }

        [$timeFrom, $timeTill] = self::getTimeRange($filter);

        $labels = [];
        $datasets = [];

        // Bits received
        $receivedLabels = [];
        $receivedData = [];
        if ($targetItems['Bits received']) {
            $item = $targetItems['Bits received'];
            $historyData = $this->getHistoryData($item['itemid'], $timeFrom, $timeTill, 3, 100);
            foreach ($historyData as $history) {
                $receivedLabels[] = date('H:i', $history['clock']);
                $receivedData[] = $history['value'] / 1000000;
            }
            $datasets[] = [
                'label' => "Bits received {$etherName} (Mbps)",
                'data' => array_reverse($receivedData),
                'borderColor' => '#4CAF50',
                'backgroundColor' => 'rgba(76, 175, 80, 0.2)',
                'tension' => 0.5,
            ];
        }

        // Bits sent
        $sentData = [];
        if ($targetItems['Bits sent']) {
            $item = $targetItems['Bits sent'];
            $historyData = $this->getHistoryData($item['itemid'], $timeFrom, $timeTill, 3, 100);
            foreach ($historyData as $history) {
                $sentData[] = $history['value'] / 1000000;
            }
            $datasets[] = [
                'label' => "Bits sent {$etherName} (Mbps)",
                'data' => array_reverse($sentData),
                'borderColor' => '#2196F3',
                'backgroundColor' => 'rgba(33, 150, 243, 0.2)',
                'tension' => 0.5,
            ];
        }

        $labels = array_reverse($receivedLabels);
        if (empty($labels) && !empty($sentData)) {
            // Jika tidak ada received, gunakan sent (tambahkan label dari sent jika perlu)
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    public function getMemoryHistoryByHost($hostName, $filter = '1hour', $itemKey = 'vm.memory.util[memoryUsedPercentage.Memory]', $intervalMinutes = 15)
    {
        $hostId = $this->getHostIdByName($hostName);
        if (!$hostId) {
            return ['labels' => [], 'data' => [], 'itemName' => 'Memory Usage'];
        }

        $itemId = $this->getItemIdByKey($hostId, $itemKey);
        if (!$itemId) {
            return ['labels' => [], 'data' => [], 'itemName' => 'Memory Usage'];
        }

        [$timeFrom, $timeTill] = self::getTimeRange($filter);

        // Ambil data history (float = 0)
        $history = $this->getHistoryData($itemId, $timeFrom, $timeTill, 0, 1000);

        // Agregasi per 15 menit
        $interval = $intervalMinutes * 60;
        $buckets = [];
        foreach ($history as $row) {
            $bucket = floor($row['clock'] / $interval) * $interval;
            $buckets[$bucket][] = (float)$row['value'];
        }

        $labels = [];
        $data = [];
        foreach ($buckets as $bucketTime => $values) {
            $labels[] = date('d M H:i', $bucketTime);
            $data[] = round(array_sum($values) / count($values), 2); // rata-rata per 15 menit
        }

        $itemName = 'Memory Usage (%)';

        return [
            'labels' => $labels,
            'data' => $data,
            'itemName' => $itemName,
            'tension' => 0.5,
        ];
    }

    public function getLinkStatusHistoryByHost($hostName, $filter = '1hour', $intervalMinutes = 30)
    {
        $hostId = $this->getHostIdByName($hostName);
        if (!$hostId) {
            return ['labels' => [], 'datasets' => []];
        }

        $client = new \GuzzleHttp\Client();
        $authToken = $this->getAuthToken();

        // Ambil semua item status interface
        $response = $client->request('POST', $this->url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'item.get',
                'params' => [
                    'output' => ['itemid', 'name', 'key_'],
                    'hostids' => $hostId,
                    'search' => ['key_' => 'net.if.status[ifOperStatus'],
                ],
                'id' => 1,
                'auth' => $authToken,
            ],
        ]);
        $data = json_decode($response->getBody()->getContents(), true);

        // Susun array itemid dan label interface
        $interfaces = [];
        if (!empty($data['result'])) {
            foreach ($data['result'] as $item) {
                if (preg_match('/Interface ([\w\d]+)\(\): Operational status/', $item['name'], $matches)) {
                    $label = $matches[1];
                } else {
                    $label = $item['name'];
                }
                $interfaces[$item['itemid']] = $label;
            }
        }

        [$timeFrom, $timeTill] = self::getTimeRange($filter);

        $labels = [];
        $datasets = [];

        foreach ($interfaces as $itemid => $label) {
            $historyData = $this->getHistoryData($itemid, $timeFrom, $timeTill, 3, 1000);

            // Agregasi per 30 menit
            $interval = $intervalMinutes * 60;
            $buckets = [];
            foreach ($historyData as $row) {
                $bucket = floor($row['clock'] / $interval) * $interval;
                $buckets[$bucket][] = (int)$row['value'];
            }

            $labelSet = [];
            $dataSet = [];
            foreach ($buckets as $bucketTime => $values) {
                $labelSet[] = date('H:i', $bucketTime);
                // Ambil status terakhir pada interval
                $dataSet[] = end($values);
            }

            if (empty($labels) && !empty($labelSet)) {
                $labels = $labelSet;
            }
            $datasets[] = [
                'label' => $label,
                'data' => $dataSet,
                'borderColor' => '#' . substr(md5($label), 0, 6),
                'backgroundColor' => 'rgba(0,0,0,0)',
                'stepped' => true,
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    public function getHostInterfaces(array $hostIds): array
    {
        $client = new Client();
        $authToken = $this->getAuthToken();

        $response = $client->request('POST', $this->url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'hostinterface.get',
                'params' => [
                    'output' => 'extend',
                    'hostids' => $hostIds,
                ],
                'id' => 4,
                'auth' => $authToken,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['result'] ?? [];
    }

    public function getHostIdByName(string $hostName): ?string
    {
        $client = new Client();
        $authToken = $this->getAuthToken();

        $response = $client->request('POST', $this->url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'host.get',
                'params' => [
                    'output' => ['hostid'],
                    'filter' => ['host' => [$hostName]],
                ],
                'id' => 5,
                'auth' => $authToken,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['result'][0]['hostid'] ?? null;
    }

    public function getItemIdByKey(string $hostId, string $itemKey): ?string
    {
        $client = new Client();
        $authToken = $this->getAuthToken();

        $response = $client->request('POST', $this->url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'item.get',
                'params' => [
                    'output' => ['itemid'],
                    'hostids' => $hostId,
                    'filter' => ['key_' => [$itemKey]],
                ],
                'id' => 6,
                'auth' => $authToken,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['result'][0]['itemid'] ?? null;
    }

    public function getTrends(string $itemId, int $timeFrom, int $timeTill): array
    {
        $client = new Client();
        $authToken = $this->getAuthToken();

        $response = $client->request('POST', $this->url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'trend.get',
                'params' => [
                    'output' => ['itemid', 'clock', 'value_avg', "value_min", "value_max"],
                    'itemids' => $itemId,
                    'time_from' => $timeFrom,
                    'time_till' => $timeTill,
                ],
                'id' => 7,
                'auth' => $authToken,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['result'] ?? [];
    }

    public function getTrendData($itemId, $from, $till)
    {
        $client = new Client();
        $authToken = $this->getAuthToken();

        $response = $client->post($this->getUrl(), [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'trend.get',
                'params' => [
                    'output' => ['clock', 'value_avg', 'value_min', 'value_max'],
                    'itemids' => [$itemId],
                    'time_from' => $from,
                    'time_till' => $till,
                    'limit' => 100,
                    'sortfield' => 'clock',
                    'sortorder' => 'ASC',
                ],
                'id' => 1,
                'auth' => $this->getAuthToken(),
            ]
        ]);
        return json_decode($response->getBody(), true)['result'] ?? [];
    }


    public function getInterfaceBandwidthHistory(string $hostId, string $interfaceId, string $filter): array
    {
        $client = new Client();
        $authToken = $this->getAuthToken();

        [$timeFrom, $timeTill] = self::getTimeRange($filter);

        $response = $client->request('POST', $this->url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'history.get',
                'params' => [
                    'output' => 'extend',
                    'history' => 3, // 3 for uint64
                    'hostids' => [$hostId],
                    'itemids' => [$interfaceId],
                    'time_from' => $timeFrom,
                    'time_till' => $timeTill,
                    'sortfield' => 'clock',
                    'sortorder' => 'ASC',
                ],
                'id' => 8,
                'auth' => $authToken,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['result'] ?? [];
    }

    public function getHistoryData(string $itemId, int $timeFrom, int $timeTill, int $history, int $limit): array
    {
        $client = new Client();
        $authToken = $this->getAuthToken();

        $response = $client->request('POST', $this->url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'history.get',
                'params' => [
                    'output' => 'extend',
                    'history' => $history,
                    'itemids' => [$itemId],
                    'time_from' => $timeFrom,
                    'time_till' => $timeTill,
                    'limit' => $limit,
                    'sortfield' => 'clock',
                    'sortorder' => 'ASC',
                ],
                'id' => 9,
                'auth' => $authToken,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['result'] ?? [];
    }
}
