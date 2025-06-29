<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ZabbixApiService;

class DeviceStatusWidget extends Component
{
    public array $devices = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $zabbix = new ZabbixApiService();
        $hosts = $zabbix->getHosts();
        $hostIds = array_column($hosts, 'hostid');
        $interfaces = $zabbix->getHostInterfaces($hostIds);

        $interfaceMap = [];
        foreach ($interfaces as $iface) {
            $interfaceMap[$iface['hostid']] = $iface;
        }

        $this->devices = [];
        foreach ($hosts as $host) {
            $iface = $interfaceMap[$host['hostid']] ?? null;
            $this->devices[] = [
                'gedung' => $host['groups'][0]['name'] ?? '-',
                'lantai' => $host['groups'][1]['name'] ?? '-',
                'nama' => $host['name'] ?? $host['host'],
                'ip' => $iface['ip'] ?? '-',
                'status' => $host['status'] == 0 ? 'Up' : 'Down',
                'last_down' => $host['error'] ?? '-',
            ];
        }
    }

    public function render()
    {
        return view('livewire.device-status-widget', [
            'devices' => $this->devices,
        ]);
    }
}
