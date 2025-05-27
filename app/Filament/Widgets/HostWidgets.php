<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Services\ZabbixApiService;
use Illuminate\Support\Facades\Log;

class HostWidgets extends Widget
{
    protected static string $view = 'filament.widgets.host-widgets';
    public function getViewData(): array
    {
        $zabbixService = new ZabbixApiService();
        $hosts = $zabbixService->getHosts();
        // Log::info('Retrieved hosts from Zabbix API', [
        //     'hosts' => $hosts,
        // ]);

        return [
            'hosts' => $hosts,
        ];
    }
}
