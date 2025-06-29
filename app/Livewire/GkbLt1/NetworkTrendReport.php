<?php

namespace App\Livewire\GkbLt1;

use Livewire\Component;
use App\Services\ZabbixApiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class NetworkTrendReport extends Component
{
    public string $filter = 'today';
    public array $trendData = [];

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
        $hostId = $zabbix->getHostIdByName('mikrotik-gkb-lt1'); // ganti sesuai host
        $itemIn = $zabbix->getItemIdByKey($hostId, 'net.if.in[ifHCInOctets.2]');
        $itemOut = $zabbix->getItemIdByKey($hostId, 'net.if.out[ifHCOutOctets.2]');

        [$from, $till] = $this->getTimeRange($this->filter);

        $trendIn = $zabbix->getTrends($itemIn, $from, $till);
        $trendOut = $zabbix->getTrends($itemOut, $from, $till);

        $totalIn = collect($trendIn)->sum('value_avg') / 1048576; // MB/s
        $totalOut = collect($trendOut)->sum('value_avg') / 1048576; // MB/s

        $this->trendData = [
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'from' => date('Y-m-d H:i', $from),
            'till' => date('Y-m-d H:i', $till),
        ];
    }

    public function getTimeRange($filter): array
    {
        $now = now();
        switch ($filter) {
            case 'yesterday':
                $from = $now->copy()->subDay()->startOfDay()->timestamp;
                $till = $now->copy()->subDay()->endOfDay()->timestamp;
                break;
            case 'today':
                $from = $now->copy()->startOfDay()->timestamp;
                $till = $now->timestamp;
                break;
            case 'last_week':
                $from = $now->copy()->subDays(6)->startOfDay()->timestamp;
                $till = $now->timestamp;
            case 'last_month':
                $from = $now->copy()->subMonth()->startOfDay()->timestamp;
                $till = $now->timestamp;
                break;
            case 'last_3_month':
                $from = $now->copy()->subMonths(3)->startOfDay()->timestamp;
                $till = $now->timestamp;
                break;
            case 'last_6_month':
                $from = $now->copy()->subMonths(6)->startOfDay()->timestamp;
                $till = $now->timestamp;
            case 'last_year':
                $from = $now->copy()->subYear()->startOfDay()->timestamp;
                $till = $now->timestamp;
            default:
                $from = $now->copy()->startOfDay()->timestamp;
                $till = $now->timestamp;
                break;
        }
        return [$from, $till];
    }

    public function downloadPdf()
    {
        $pdf = Pdf::loadView('exports.traffic-trend-report', [
            'trendData' => $this->trendData,
            'filter' => $this->filter,
        ]);
        return Response::streamDownload(
            fn() => print($pdf->output()),
            'traffic-trend-report_' . now()->format('Ymd_His') . '.pdf'
        );
    }

    public function render()
    {
        return view('livewire.gkb-lt1.network-trend-report');
    }
}
