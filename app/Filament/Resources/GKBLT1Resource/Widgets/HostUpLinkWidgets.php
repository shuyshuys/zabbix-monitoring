<?php

namespace App\Filament\Resources\GKBLT1Resource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Services\ZabbixApiService;
use Illuminate\Support\Facades\Log;

class HostUpLinkWidgets extends ChartWidget
{
    protected static ?string $heading = 'Host UpLink Widgets for Mikrotik GKB LT1';
    protected static ?string $pollingInterval = '180s';

    protected function getData(): array
    {
        $zabbixService = new ZabbixApiService();
        $authToken = $zabbixService->getAuthToken();
        $hosts = $zabbixService->getHosts();
        $hostId = null;

        foreach ($hosts as $host) {
            if ($host['host'] === 'mikrotik-gkb-lt1') {
                $hostId = $host['hostid'];
                break;
            }
        }
        if (!$hostId) {
            return [
                'labels' => [],
                'datasets' => [],
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
                    'search' => ['key_' => 'ifHCOutOctets'],
                ],
                'id' => 1,
                'auth' => $authToken,
            ],
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        if (empty($data['result'])) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }
        $itemIds = [];
        foreach ($data['result'] as $item) {
            $itemIds[] = $item['itemid'];
            $itemKeyMap[$item['itemid']] = $item['key_'];
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
                    'itemids' => $itemIds,
                    'sortfield' => 'clock',
                    'sortorder' => 'ASC',
                    'time_from' => strtotime('-1 hour'),
                    'time_till' => time(),
                ],
                'id' => 2,
                'auth' => $authToken,
            ],
        ]);
        $historyData = json_decode($response->getBody()->getContents(), true)['result'] ?? [];
        // Log::info('History Data: ', $historyData);
        if (empty($historyData)) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }
        $labels = [];
        $upLinkData = [];
        $downLinkData = [];
        $totalData = [];
        foreach ($historyData as $history) {
            $timestamp = date('Y-m-d H:i:s', $history['clock']);
            if (!in_array($timestamp, $labels)) {
                $labels[] = $timestamp;
            }
            $key = $itemKeyMap[$history['itemid']] ?? '';
            if (str_contains($key, 'net.if.out[ifHCOutOctets')) {
                $upLinkData[$timestamp] = ($upLinkData[$timestamp] ?? 0) + (float)$history['value'];
            } elseif (str_contains($key, 'net.if.in[ifHCInOctets')) {
                $downLinkData[$timestamp] = ($downLinkData[$timestamp] ?? 0) + (float)$history['value'];
            }
        }
        foreach ($labels as $label) {
            $upLinkValue = $upLinkData[$label] ?? 0;
            $downLinkValue = $downLinkData[$label] ?? 0;
            $totalData[] = $upLinkValue + $downLinkValue;
        }
        $upLinkData = array_values($upLinkData);
        $downLinkData = array_values($downLinkData);
        $totalData = array_values($totalData);
        if (empty($labels) || empty($upLinkData) || empty($downLinkData) || empty($totalData)) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }
        // Convert timestamps to the format required by Chart.js
        $labels = array_map(function ($label) {
            return date('Y-m-d H:i:s', strtotime($label));
        }, $labels);
        // Prepare datasets for Chart.js
        $datasets = [
            [
                'label' => 'UpLink',
                'data' => $upLinkData,
                'borderColor' => '#4CAF50',
                'backgroundColor' => '#4CAF50',
                'fill' => false,
            ],
            [
                'label' => 'DownLink',
                'data' => $downLinkData,
                'borderColor' => '#2196F3',
                'backgroundColor' => '#2196F3',
                'fill' => false,
            ],
            [
                'label' => 'Total',
                'data' => $totalData,
                'borderColor' => '#FF9800',
                'backgroundColor' => '#FF9800',
                'fill' => false,
            ],
        ];
        // Return the data in the format required by Chart.js
        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
