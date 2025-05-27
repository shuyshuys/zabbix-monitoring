<?php

namespace App\Filament\Resources\ZabbixResource\Widgets;

use Filament\Widgets\Widget;
use App\Services\ZabbixApiService;

class ZabbixWidget extends Widget
{
    protected static string $view = 'filament.resources.zabbix-resource.widgets.zabbix-widget';

    public function getViewData(): array
    {
        $zabbixService = new ZabbixApiService();

        // Ambil semua host
        $hosts = $zabbixService->getHosts();

        // Filter host dengan nama "Zabbix server"
        $filteredHosts = array_filter($hosts, function ($host) {
            return isset($host['host']) && $host['host'] === 'Zabbix server';
        });

        // Jika ingin mengambil hanya satu host (misal yang pertama)
        $filteredHosts = array_values($filteredHosts);

        return [
            // 'data' => (object)['result' => $filteredHosts],
        ];
    }

    public function getTitle(): string
    {
        return 'Zabbix Widget';
    }
}
