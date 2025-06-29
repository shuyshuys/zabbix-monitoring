<?php

namespace App\Livewire\GkbLt2;

use Livewire\Component;
use App\Services\ZabbixApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class IcmpPingTrendReport extends Component
{
    public $filter = 'last_week';
    public array $trendData = [];
    public array $labels = [];
    public array $statusData = [];
    public array $responseTimeData = [];

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
        $hostId = $zabbix->getHostIdByName('mikrotik-gkb-lt2');
        [$from, $till] = ZabbixApiService::getTimeRange($this->filter);

        // Ambil itemid untuk ICMP status dan response time
        $icmpStatusItemId = $zabbix->getItemIdByKey($hostId, 'icmpping');
        $icmpResponseTimeItemId = $zabbix->getItemIdByKey($hostId, 'icmppingsec');

        // Ambil data history
        $statusHistory = $zabbix->getHistoryData($icmpStatusItemId, $from, $till, 3, 50); // 3: integer
        $responseTimeHistory = $zabbix->getHistoryData($icmpResponseTimeItemId, $from, $till, 0, 50); // 0: float

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

        $this->trendData = [
            'itemIdStatus' => $icmpStatusItemId,
            'itemIdResponse' => $icmpResponseTimeItemId,
            'from' => date('Y-m-d H:i', $from),
            'till' => date('Y-m-d H:i', $till),
            'maxStatus' => !empty($statusData) ? max($statusData) : null,
            'minStatus' => !empty($statusData) ? min($statusData) : null,
            'maxResponse' => !empty($responseTimeData) ? max($responseTimeData) : null,
            'minResponse' => !empty($responseTimeData) ? min($responseTimeData) : null,
        ];
    }

    public function downloadPdf()
    {
        $pdf = Pdf::loadView('exports.icmp-ping-trend-report', [
            'trendData' => $this->trendData,
            'labels' => $this->labels,
            'statusData' => $this->statusData,
            'responseTimeData' => $this->responseTimeData,
            'filter' => $this->filter,
        ]);
        return response()->streamDownload(fn() => print($pdf->output()), 'icmp-ping-trend-report.pdf');
    }

    public function render()
    {
        return view('livewire.icmp-ping-trend-report', [
            'trendData' => $this->trendData,
            'labels' => $this->labels,
            'statusData' => $this->statusData,
            'responseTimeData' => $this->responseTimeData,
        ]);
    }
}
