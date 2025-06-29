<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ZabbixApiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class TrafficTrendReport extends Component
{
    public string $filter = 'today';
    public string $hostName;
    public string $interfaceIn;
    public string $interfaceOut;

    public array $trendData = [];
    public array $dataIn = [];
    public array $dataOut = [];
    public array $labels = [];

    public function mount($hostName, $interfaceIn, $interfaceOut)
    {
        $this->hostName = $hostName;
        $this->interfaceIn = $interfaceIn;
        $this->interfaceOut = $interfaceOut;
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
        $itemIn = $zabbix->getItemIdByKey($hostId, $this->interfaceIn);
        $itemOut = $zabbix->getItemIdByKey($hostId, $this->interfaceOut);

        [$from, $till] = $zabbix->getTimeRange($this->filter);

        $trendIn = $zabbix->getTrends($itemIn, $from, $till);
        $trendOut = $zabbix->getTrends($itemOut, $from, $till);

        $totalIn = collect($trendIn)->sum('value_avg') / 1048576; // MB/s
        $totalOut = collect($trendOut)->sum('value_avg') / 1048576; // MB/s

        $this->labels = [];
        foreach ($trendIn as $trend) {
            $this->labels[] = date('d M H', $trend['clock']);
        }

        $this->dataIn = [];
        foreach ($trendIn as $trend) {
            $this->dataIn[] = round($trend['value_avg'] / 1048576, 2); // dari byte ke MB
        }
        $this->dataOut = [];
        foreach ($trendOut as $trend) {
            $this->dataOut[] = round($trend['value_avg'] / 1048576, 2); // dari byte ke MB
        }

        $this->trendData = [
            'itemIdIn' => $itemIn,
            'itemIdOut' => $itemOut,
            'trendIn' => $trendIn ? collect($trendIn)->sum('value_avg') / 1048576 : 0, // MB/s
            'trendOut' => $trendOut ? collect($trendOut)->sum('value_avg') / 1048576 : 0, // MB/s
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'from' => date('Y-m-d H:i', $from),
            'till' => date('Y-m-d H:i', $till),
        ];
    }

    public function downloadPdf()
    {
        $pdf = Pdf::loadView('exports.traffic-trend-report', [
            'labels' => $this->labels,
            'dataIn' => $this->dataIn,
            'dataOut' => $this->dataOut,
            'trendData' => $this->trendData,
            'filter' => $this->filter,
        ]);
        return Response::streamDownload(
            fn() => print($pdf->output()),
            'traffic-trend-report_' . now()->format('Ymd_His') . '.pdf'
        );
    }

    // public function render()
    // {
    //     return view('livewire.fik-lt1.traffic-trend-report');
    // }

    public function render()
    {
        return view(
            'livewire.traffic-trend-report',
            [
                'trendData' => $this->trendData,
                'filter' => $this->filter,
                'labels' => $this->labels,
                'dataIn' => $this->dataIn,
                'dataOut' => $this->dataOut,
            ]
        );
    }
}
