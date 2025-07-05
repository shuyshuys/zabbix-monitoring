<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ZabbixApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class MemoryTrendReport extends Component
{
    public $filter = 'today';
    public string $hostName;
    public array $trendData = [];
    public array $labels = [];
    public array $usedMemoryData = [];

    public function mount($hostName)
    {
        $this->hostName = $hostName;
        $this->loadData();
    }

    public function updatedFilter()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $zabbix = new ZabbixApiService();
        $hostId = $zabbix->getHostIdByName($this->hostName);
        [$from, $till] = ZabbixApiService::getTimeRange($this->filter);

        // Ambil itemid untuk memory usage
        $usedMemoryItemId = $zabbix->getItemIdByKey($hostId, 'vm.memory.util[memoryUsedPercentage.Memory]');

        // ambil trend data
        $this->trendData = [];
        $usedMemory = $zabbix->getTrendData($usedMemoryItemId, $from, $till);

        $valueAvgsUsed = array_column($usedMemory, 'value_avg');
        $maxUsed = !empty($valueAvgsUsed) ? round(max($valueAvgsUsed), 2) : 0;
        $minUsed = !empty($valueAvgsUsed) ? round(min($valueAvgsUsed), 2) : 0;

        $this->trendData = [
            'itemIdUsed' => $usedMemoryItemId,
            'maxUsed' => $maxUsed,
            'minUsed' => $minUsed,
            'from' => date('Y-m-d H:i', $from),
            'till' => date('Y-m-d H:i', $till),
        ];

        $this->labels = array_map(fn($d) => date('d M H:i', $d['clock']), $usedMemory);
        $this->usedMemoryData = array_map(fn($d) => round((float)$d['value_avg'], 2), $usedMemory);


        // // Reverse agar urutan waktu dari lama ke baru
        // $this->labels = array_reverse($this->labels);
        // $this->usedMemoryData = array_reverse($this->usedMemoryData);
        // $this->freeMemoryData = array_reverse($this->freeMemoryData);


        // $freeMemoryHistory = $zabbix->getTrendData($freeMemoryItemId, $from, $till);
        // $this->freeMemoryData = array_map(fn($d) => round((float)$d['value'], 2), $freeMemoryHistory);

        // $this->trendData = [
        //     'itemIdUsed' => $usedMemoryItemId,
        //     'itemIdFree' => $freeMemoryItemId,
        //     'maxUsed' => round(max($this->usedMemoryData), 2),
        //     'minUsed' => round(min($this->usedMemoryData), 2),
        //     'from' => date('Y-m-d H:i', $from),
        //     'till' => date('Y-m-d H:i', $till),
        // ];
    }

    public function downloadPdf()
    {
        $pdf = Pdf::loadView('exports.memory-trend-report', [
            'trendData' => $this->trendData,
            'labels' => $this->labels,
            'usedMemoryData' => $this->usedMemoryData,
            'filter' => $this->filter,
        ]);
        return response()->streamDownload(
            fn() => print($pdf->output()),
            'memory-trend-report_' . now()->format('Ymd_His') . '.pdf'
        );
    }

    public function render()
    {
        return view('livewire.memory-trend-report', [
            'trendData' => $this->trendData,
            'labels' => $this->labels,
            'usedMemoryData' => $this->usedMemoryData,
        ]);
    }
}
