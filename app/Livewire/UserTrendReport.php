<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ZabbixApiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;


class UserTrendReport extends Component
{
    public string $filter = 'today';
    public string $hostName;
    public array $trendData = [];
    public array $labels = [];
    public array $values = [];

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
        $hostId = $zabbix->getHostIdByName($this->hostName); // ganti sesuai host
        $itemId = $zabbix->getItemIdByKey($hostId, 'mtxrDHCPLeaseCount');

        [$from, $till] = ZabbixApiService::getTimeRange($this->filter);

        $trends = $zabbix->getTrends($itemId, $from, $till);

        $valueAvgs = array_column($trends, 'value_avg');
        $maxValue = !empty($valueAvgs) ? round(max($valueAvgs), 2) : 0;
        $minValue = !empty($valueAvgs) ? round(min($valueAvgs), 2) : 0;

        $this->labels = [];
        $this->values = [];
        foreach ($trends as $trend) {
            $this->labels[] = date('d-M H:i', $trend['clock']);
            $this->values[] = $trend['value_avg'];
        }

        $this->trendData = [
            'itemId' => $itemId,
            'total' => collect($this->values)->sum(),
            'max' => $maxValue,
            'min' => $minValue,
            'from' => date('Y-m-d H:i', $from),
            'till' => date('Y-m-d H:i', $till),
        ];
    }

    public function downloadPdf()
    {
        Log::info('downloadPDF User dipanggil');
        $pdf = Pdf::loadView('exports.dhcp-lease-trend-pdf', [
            'trendData' => $this->trendData,
            'labels' => $this->labels,
            'values' => $this->values,
            'filter' => $this->filter,
        ]);
        return Response::streamDownload(
            fn() => print($pdf->output()),
            'dhcp-lease-trend-report_' . now()->format('Ymd_His') . '.pdf'
        );
    }

    public function render()
    {
        return view('livewire.user-trend-report', [
            'labels' => $this->labels,
            'values' => $this->values,
            'trendData' => $this->trendData,
        ]);
    }
}
