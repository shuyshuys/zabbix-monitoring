<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ZabbixApiService
{
    protected string $url;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->url = env('ZABBIX_URL', 'http://localhost/zabbix/api_jsonrpc.php');
        $this->username = env('ZABBIX_USERNAME', 'Admin');
        $this->password = env('ZABBIX_PASSWORD', 'zabbix');
    }

    public function getUrl(): string
    {
        return $this->url;
    }

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
