<?php

namespace App\Filament\Resources\ZabbixResource\Widgets;

use Filament\Widgets\Widget;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;


class ZabbixWidget extends Widget
{
    protected static string $view = 'filament.resources.zabbix-resource.widgets.zabbix-widget';

    public function getViewData(): array
    {
        $client = new Client();
        $response = $client->request('GET', 'http://zabbix.isslab.web.id/zabbix/api_jsonrpc.php', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'apiinfo.version',
                'id' => 1,
                'auth' => '112b73fb134a1770d4667bf3fc0aca5897f4e5cab972d5b1e27534d68c92f7ca', // Assuming no authentication is needed for this request
            ],
        ]);
        if ($response->getStatusCode() !== 200) {
            Log::error('Zabbix API request failed', [
                'status_code' => $response->getStatusCode(),
                'reason_phrase' => $response->getReasonPhrase(),
            ]);
            throw new \Exception('Failed to fetch data from Zabbix API');
        }
        if (!$response->getBody()) {
            Log::error('Zabbix API response body is empty');
            throw new \Exception('Empty response from Zabbix API');
        }
        if (empty($response->getBody()->getContents())) {
            Log::error('Zabbix API response body is empty');
            throw new \Exception('Empty response from Zabbix API');
        }
        if (!is_string($response->getBody()->getContents())) {
            Log::error('Zabbix API response body is not a string', [
                'response_body' => $response->getBody()->getContents(),
            ]);
            throw new \Exception('Invalid response format from Zabbix API');
        }

        // Decode the JSON response
        $data = json_decode($response->getBody()->getContents());
        Log::info('Zabbix API response', [
            'data' => $data,
        ]);

        return [
            'data' => $data,
        ];
    }
}