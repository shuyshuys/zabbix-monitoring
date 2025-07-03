<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ZabbixApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class LinkStatusTrendReport extends Component
{
    public $filter = 'today';
    public string $hostName;
    public array $trendData = [];
    public array $labels = [];
    public array $statusData = [];
    public array $responseTimeData = [];

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
        $authToken = $zabbix->getAuthToken();
        $hostId = $zabbix->getHostIdByName($this->hostName);
        [$from, $till] = ZabbixApiService::getTimeRange($this->filter);

        // Ambil itemid untuk Link status dan response time
        $linkStatusItemId = $zabbix->getItemIdByKey($hostId, 'icmpping');
        $linkResponseTimeItemId = $zabbix->getItemIdByKey($hostId, 'icmppingsec');

        // Ambil data history
        $statusHistory = $zabbix->getHistoryData($linkStatusItemId, $from, $till, 3, 50); // 3: integer
        $responseTimeHistory = $zabbix->getHistoryData($linkResponseTimeItemId, $from, $till, 0, 50); // 0: float

        // Sinkronisasi data berdasarkan waktu (clock)
        $count = min(count($statusHistory), count($responseTimeHistory));
        $labels = [];
        $statusData = [];
        $responseTimeData = [];
        for ($i = 0; $i < $count; $i++) {
            $labels[] = date('d M H:i', $statusHistory[$i]['clock']);
            $statusData[] = (int) $statusHistory[$i]['value'];
            $responseTimeData[] = round((float) $responseTimeHistory[$i]['value'] * 1000, 2); // detik ke ms
        }

        // Reverse agar urutan waktu dari lama ke baru
        $this->labels = array_reverse($labels);
        $this->statusData = array_reverse($statusData);
        $this->responseTimeData = array_reverse($responseTimeData);

        // Data untuk grafik
        $this->trendData = [
            'itemIdStatus' => $linkStatusItemId,
            'itemIdResponse' => $linkResponseTimeItemId,
            'statusData' => $this->statusData,
            'responseTimeData' => $this->responseTimeData,
            'labels' => $this->labels,
            'from' => date('Y-m-d H:i', $from),
            'till' => date('Y-m-d H:i', $till),
        ];
    }
    public function downloadPdf()
    {
        $pdf = Pdf::loadView('exports.link-status-trend-report', [
            'trendData' => $this->trendData,
            'labels' => $this->labels,
            'statusData' => $this->statusData,
            'responseTimeData' => $this->responseTimeData,
            'filter' => $this->filter,
        ]);
        return response()->streamDownload(fn() => print($pdf->output()), 'link-status-trend-report.pdf');
    }
    public function render()
    {
        return view('livewire.link-status-trend-report', [
            'trendData' => $this->trendData,
            'labels' => $this->labels,
            'statusData' => $this->statusData,
            'responseTimeData' => $this->responseTimeData,
        ]);
    }
}
