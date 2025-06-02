<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Services\ZabbixApiService;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\ReportResource;
use Illuminate\Support\Facades\Log;

class DeviceStatusReport extends Page
{
    protected static string $resource = ReportResource::class;

    protected static string $view = 'filament.resources.report-resource.pages.device-status-report';

    public $devices = [];

    public function mount()
    {
        $zabbix = new ZabbixApiService();
        $hosts = $zabbix->getHosts();
        Log::info('All hosts from Zabbix API:', $hosts);
        $hostIds = array_column($hosts, 'hostid');
        $interfaces = $zabbix->getHostInterfaces($hostIds);

        Log::info('Fetching device status report', [
            'host_count' => count($hosts),
            'interface_count' => count($interfaces),
        ]);

        // Gabungkan data host dan interface
        $interfaceMap = [];
        foreach ($interfaces as $iface) {
            $interfaceMap[$iface['hostid']] = $iface;
        }

        $this->devices = [];
        foreach ($hosts as $host) {
            $iface = $interfaceMap[$host['hostid']] ?? null;
            $this->devices[] = [
                'gedung' => $host['groups'][0]['name'] ?? '-', // Atur sesuai struktur group Anda
                'lantai' => $host['groups'][1]['name'] ?? '-',
                'nama' => $host['name'] ?? $host['host'],
                'ip' => $iface['ip'] ?? '-',
                'status' => $host['status'] == 0 ? 'Up' : 'Down',
                // 'available' => $host['available'] == 1 ? 'Available' : 'Unavailable',
                'last_down' => $host['error'] ?? '-',
            ];
        }
    }
}
