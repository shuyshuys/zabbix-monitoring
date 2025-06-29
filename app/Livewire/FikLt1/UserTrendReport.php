<?php

namespace App\Livewire\FikLt1;

use Livewire\Component;
use App\Services\ZabbixApiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class UserTrendReport extends Component
{
    public string $filter = 'today';
    public array $trendData = [];
    public array $labels = [];
    public array $values = [];

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
        $hostId = $zabbix->getHostIdByName('mikrotik-fik-2'); // ganti sesuai host
        $itemId = $zabbix->getItemIdByKey($hostId, 'mtxrDHCPLeaseCount');

        [$from, $till] = ZabbixApiService::getTimeRange($this->filter);

        $trends = $zabbix->getTrends($itemId, $from, $till);

        $maxValue = round(max(array_column($trends, 'value_avg')), 2);

        $minValue = round(min(array_column($trends, 'value_avg')), 2);

        $this->labels = [];
        $this->values = [];
        foreach ($trends as $trend) {
            $this->labels[] = date('d-M H', $trend['clock']);
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
        $pdf = Pdf::loadView('exports.dhcp-lease-trend-report', [
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
