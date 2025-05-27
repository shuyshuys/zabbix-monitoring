<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ZabbixApiService
{
    protected string $url = 'http://192.168.192.114:8080/api_jsonrpc.php';
    protected string $username = 'Admin';
    protected string $password = 'zabbix';

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

    // public function getCpuUsage(string $hostId, string $authToken): array
    // {
    //     $client = new Client();

    //     $response = $client->request('POST', $this->url, [
    //         'headers' => [
    //             'Content-Type' => 'application/json',
    //         ],
    //         'json' => [
    //             'jsonrpc' => '2.0',
    //             'method' => 'item.get',
    //             'params' => [
    //                 'output' => ['itemid', 'name'],
    //                 'hostids' => $hostId,
    //                 'search' => ['key_' => 'system.cpu.util'],
    //             ],
    //             'id' => 4,
    //             'auth' => $authToken,
    //         ],
    //     ]);

    //     $data = json_decode($response->getBody()->getContents(), true);

    //     if (empty($data['result'])) {
    //         Log::warning('No CPU usage items found', ['hostId' => $hostId]);
    //         return [];
    //     }

    //     Log::info('CPU Item Data', ['data' => $data]);

    //     $allHistory = [];
    //     foreach ($data['result'] as $item) {
    //         $itemId = $item['itemid'];
    //         Log::info('CPU Item ID', ['itemId' => $itemId]);

    //         $historyResponse = $client->request('POST', $this->url, [
    //             'headers' => [
    //                 'Content-Type' => 'application/json',
    //             ],
    //             'json' => [
    //                 'jsonrpc' => '2.0',
    //                 'method' => 'history.get',
    //                 'params' => [
    //                     'output' => 'extend',
    //                     'itemids' => $itemId,
    //                     'sortfield' => 'clock',
    //                     'sortorder' => 'DESC',
    //                     'limit' => 10,
    //                 ],
    //                 'id' => 5,
    //                 'auth' => $authToken,
    //             ],
    //         ]);

    //         $historyData = json_decode($historyResponse->getBody()->getContents(), true)['result'] ?? [];
    //         Log::info('CPU History Data', ['itemId' => $itemId, 'history' => $historyData]);
    //         $allHistory[$itemId] = $historyData;
    //     }

    //     return $allHistory;
    // }

    public function getUrl(): string
    {
        return $this->url;
    }
}
