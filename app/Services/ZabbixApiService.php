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
            case '1hour':
                $timeFrom = strtotime('-1 hour');
                $timeTill = strtotime('-0 hour');
                break;
            case '2hours':
                $timeFrom = strtotime('-2 hours');
                $timeTill = strtotime('-1 hour');
                break;
            case '3hours':
                $timeFrom = strtotime('-3 hours');
                $timeTill = strtotime('-2 hours');
                break;
            case '4hours':
                $timeFrom = strtotime('-4 hours');
                $timeTill = strtotime('-3 hours');
                break;
            case '5hours':
                $timeFrom = strtotime('-5 hours');
                $timeTill = strtotime('-4 hours');
                break;
            case '6hours':
                $timeFrom = strtotime('-6 hours');
                $timeTill = strtotime('-5 hours');
                break;
            case '12hours':
                $timeFrom = strtotime('-12 hours');
                $timeTill = strtotime('-11 hours');
                break;
            default:
                $timeFrom = strtotime('today');
        }
        return [$timeFrom, $timeTill];
    }
}
