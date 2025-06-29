<?php

namespace App\Filament\Resources\ReportResource\Pages;

use Filament\Tables;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ZabbixApiService;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Log;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\ReportResource;
use Filament\Tables\Concerns\InteractsWithTable;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DeviceStatusReport extends Page implements Tables\Contracts\HasTable
{
    use InteractsWithTable;


    protected static string $resource = ReportResource::class;
    protected static string $view = 'filament.resources.report-resource.pages.device-status-report';

    public array $devices = [];

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $zabbix = new ZabbixApiService();
        $hosts = $zabbix->getHosts();
        $hostIds = array_column($hosts, 'hostid');
        $interfaces = $zabbix->getHostInterfaces($hostIds);

        $interfaceMap = [];
        foreach ($interfaces as $iface) {
            $interfaceMap[$iface['hostid']] = $iface;
        }

        $this->devices = [];
        foreach ($hosts as $host) {
            $iface = $interfaceMap[$host['hostid']] ?? null;
            $this->devices[] = [
                'gedung' => $host['groups'][0]['name'] ?? '-',
                'lantai' => $host['groups'][1]['name'] ?? '-',
                'nama' => $host['name'] ?? $host['host'],
                'ip' => $iface['ip'] ?? '-',
                'status' => $host['status'] == 0 ? 'Up' : 'Down',
                'last_down' => $host['error'] ?? '-',
            ];
        }
    }

    // Filament Table definition
    protected function getTableQuery()
    {
        // Data array as a collection
        return collect($this->devices);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('gedung')->label('Gedung'),
            TextColumn::make('lantai')->label('Lantai'),
            TextColumn::make('nama')->label('Nama Perangkat'),
            TextColumn::make('ip')->label('IP'),
            TextColumn::make('status')->label('Status')
                ->color(fn($record) => $record['status'] === 'Up' ? 'success' : 'danger')
                ->badge(),
            TextColumn::make('last_down')->label('Waktu Terakhir Down'),
        ];
    }

    // Tombol download PDF
    public function downloadPdf(): StreamedResponse
    {
        $this->loadData();

        $pdf = Pdf::loadView('exports.device-status-report', [
            'devices' => $this->devices,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan-status-perangkat_' . now()->format('Ymd_His') . '.pdf');
    }

    public function getActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->action('downloadPdf')
                ->color('primary')
                ->icon('heroicon-o-arrow-down-tray'),
        ];
    }
}
