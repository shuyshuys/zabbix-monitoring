<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Services\ZabbixApiService;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\ReportResource;

class Report extends Page
{
    protected static string $resource = ReportResource::class;

    protected static string $view = 'filament.resources.report-resource.pages.report';

    public function getTitle(): string
    {
        return 'Laporan Trend Jaringan';
    }
}
