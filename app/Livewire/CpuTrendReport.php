<?php

namespace App\Livewire;

use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ZabbixApiService;
use Illuminate\Support\Facades\Log;


class CpuTrendReport extends Component
{
    public $filter = 'today';
    public $hostName;
    public array $trendData = [];
    public $data = [];
    public $labels = [];

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

        $itemId = $zabbix->getItemIdByKey($hostId, 'system.cpu.util[hrProcessorLoad.1]');
        $trendData = $zabbix->getTrendData($itemId, $from, $till);

        $valueAvgs = array_column($trendData, 'value_avg');
        $maxValue = !empty($valueAvgs) ? round(max($valueAvgs), 2) : 0;
        $minValue = !empty($valueAvgs) ? round(min($valueAvgs), 2) : 0;

        $this->labels = array_map(fn($d) => date('d M H:i', $d['clock']), $trendData);
        $this->data = array_map(fn($d) => round($d['value_avg'], 2), $trendData);

        $this->trendData = [
            'itemId' => $itemId,
            'total' => collect($this->data)->sum(),
            'maxValue' => $maxValue,
            'minValue' => $minValue,
            'from' => date('Y-m-d H:i', $from),
            'till' => date('Y-m-d H:i', $till),
        ];
    }

    public function downloadPdf()
    {
        Log::info('downloadPDF CPU dipanggil');
        $pdf = Pdf::loadView('exports.cpu-trend-pdf', [
            'trendData' => $this->trendData,
            'labels' => $this->labels,
            'data' => $this->data,
            'filter' => $this->filter,
        ]);
        return response()->streamDownload(
            fn() => print($pdf->output()),
            'cpu-trend-report_' . now()->format('Ymd_His') . '.pdf'
        );
    }

    public function render()
    {
        return view('livewire.cpu-trend-report', [
            'trendData' => $this->trendData,
            'labels' => $this->labels,
            'data' => $this->data,
        ]);
    }
}
