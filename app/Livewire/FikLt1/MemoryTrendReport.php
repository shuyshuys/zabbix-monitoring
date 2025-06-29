<?php

namespace App\Livewire\FikLt1;

use Livewire\Component;
use App\Services\ZabbixApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class MemoryTrendReport extends Component
{
    public $filter = 'today';
    public array $trendData = [];
    public array $labels = [];
    public array $usedMemoryData = [];
    public array $freeMemoryData = [];

    public function mount()
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
        $authToken = $zabbix->getAuthToken();
        $hostId = $zabbix->getHostIdByName('mikrotik-fik-2');
        [$from, $till] = ZabbixApiService::getTimeRange($this->filter);

        // Ambil itemid untuk memory usage
        $usedMemoryItemId = $zabbix->getItemIdByKey($hostId, 'vm.memory.util[memoryUsedPercentage.Memory]');
        $freeMemoryItemId = $zabbix->getItemIdByKey($hostId, 'vm.memory.total[hrStorageSize.Memory]');

        // Ambil data history
        $usedMemoryHistory = $zabbix->getHistoryData($usedMemoryItemId, $from, $till, 0, 50); // 0: float
        $freeMemoryHistory = $zabbix->getHistoryData($freeMemoryItemId, $from, $till, 0, 50); // 0: float

        $this->labels = array_map(fn($d) => date('d M H:i', $d['clock']), $usedMemoryHistory);
        $this->usedMemoryData = array_map(fn($d) => round((float)$d['value'], 2), $usedMemoryHistory);
        $this->freeMemoryData = array_map(fn($d) => round((float)$d['value'], 2), $freeMemoryHistory);

        // Sinkronisasi data berdasarkan waktu (clock)
        $count = min(count($usedMemoryHistory), count($freeMemoryHistory));
        for ($i = 0; $i < $count; $i++) {
            if (isset($usedMemoryHistory[$i]['clock']) && isset($freeMemoryHistory[$i]['clock'])) {
                if ($usedMemoryHistory[$i]['clock'] === $freeMemoryHistory[$i]['clock']) {
                    $this->labels[] = date('d M H:i', $usedMemoryHistory[$i]['clock']);
                    $this->usedMemoryData[] = round((float) $usedMemoryHistory[$i]['value'], 2);
                    $this->freeMemoryData[] = round((float) $freeMemoryHistory[$i]['value'], 2);
                }
            }
        }

        // Reverse agar urutan waktu dari lama ke baru
        $this->labels = array_reverse($this->labels);
        $this->usedMemoryData = array_reverse($this->usedMemoryData);
        $this->freeMemoryData = array_reverse($this->freeMemoryData);
        $this->trendData = [
            'itemIdUsed' => $usedMemoryItemId,
            'itemIdFree' => $freeMemoryItemId,
            'maxUsed' => round(max($this->usedMemoryData), 2),
            'minUsed' => round(min($this->usedMemoryData), 2),
            'from' => date('Y-m-d H:i', $from),
            'till' => date('Y-m-d H:i', $till),
        ];
    }

    public function downloadPdf()
    {
        $pdf = Pdf::loadView('exports.memory-trend-report', [
            'trendData' => $this->trendData,
            'labels' => $this->labels,
            'usedMemoryData' => $this->usedMemoryData,
            'freeMemoryData' => $this->freeMemoryData,
            'filter' => $this->filter,
        ]);
        return response()->streamDownload(
            fn() => print($pdf->output()),
            'memory-trend-report.pdf'
        );
    }

    public function render()
    {
        return view('livewire.memory-trend-report', [
            'trendData' => $this->trendData,
            'labels' => $this->labels,
            'usedMemoryData' => $this->usedMemoryData,
            'freeMemoryData' => $this->freeMemoryData,
        ]);
    }
}
