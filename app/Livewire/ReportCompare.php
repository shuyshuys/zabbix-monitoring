<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ZabbixApiService;
use Illuminate\Support\Facades\Log;

class ReportCompare extends Component
{

    public $selectedHosts = [];
    public $filter = 'today';
    public $availableHosts = [];
    public $chartData = [];
    public $metric = 'cpu';

    public function mount()
    {
        $zabbix = new ZabbixApiService();
        $hosts = $zabbix->getHosts();
        $this->availableHosts = collect($hosts)->mapWithKeys(function ($host) {
            return [$host['host'] => $host['name']];
        })->toArray();
    }

    public function updatedSelectedHosts()
    {
        $this->loadData();
    }

    public function updatedFilter()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $zabbix = new ZabbixApiService();
        $this->chartData = [];

        foreach ($this->selectedHosts as $hostName) {
            $hostId = $zabbix->getHostIdByName($hostName);
            $itemId = $zabbix->getItemIdByKey($hostId, 'system.cpu.util[,user]'); // ganti sesuai kebutuhan
            [$from, $till] = ZabbixApiService::getTimeRange($this->filter);
            $trend = $zabbix->getTrendData($itemId, $from, $till);

            $this->chartData[] = [
                'label' => $this->availableHosts[$hostName] ?? $hostName,
                'data' => $trend['values'] ?? [],
                'labels' => $trend['labels'] ?? [],
            ];
        }
    }

    public function generateChart()
    {
        Log::debug('generateChart called', [
            'filter' => $this->filter,
            'metric' => $this->metric,
            'selectedHosts' => $this->selectedHosts
        ]);

        $zabbix = new ZabbixApiService();
        $this->chartData = [];

        $keyMap = [
            'cpu' => 'system.cpu.util[hrProcessorLoad.1]',
            'memory' => 'vm.memory.util[memoryUsedPercentage.Memory]',
            'dhcp' => 'mtxrDHCPLeaseCount', // sesuaikan dengan key milikmu
            'traffic' => 'net.if.in[ifHCInOctets.2]' // contoh: trafik inbound, bisa disesuaikan
        ];

        $selectedKey = $keyMap[$this->metric] ?? null;

        if (!$selectedKey) return;

        Log::debug("filter: {$this->filter}, metric: {$this->metric}, selectedKey: {$selectedKey}");

        [$from, $till] = ZabbixApiService::getTimeRange($this->filter);

        Log::debug('Time range for trend data', [
            'from' => date('Y-m-d H:i', $from),
            'till' => date('Y-m-d H:i', $till)
        ]);

        foreach ($this->selectedHosts as $hostName) {
            $hostId = $zabbix->getHostIdByName($hostName);
            $itemId = $zabbix->getItemIdByKey($hostId, $selectedKey);
            $trend = $zabbix->getTrendData($itemId, $from, $till);

            Log::debug("Trend data for host: {$hostName}", [
                'hostId' => $hostId,
                'itemId' => $itemId,
                'trend' => $trend
            ]);

            // Normalisasi jika metric adalah mtxrDHCPLeaseCount
            $data = array_column($trend, 'value_avg');
            if ($selectedKey === 'net.if.in[ifHCInOctets.2]') {
                $data = array_map(fn($v) => $v / 1_000_000, $data);
            }

            $this->chartData[] = [
                'label' => $this->availableHosts[$hostName] ?? $hostName,
                'labels' => array_map(function ($row) {
                    return date('Y-m-d H:i', $row['clock']);
                }, $trend),
                'data' => $data,
            ];
        }
        Log::debug('Final chartData for view:', $this->chartData);
    }

    public function render()
    {
        return view('livewire.report-compare');
    }
}
