<?php

namespace App\Filament\Resources\ReportCompareResource\Pages;

use App\Filament\Resources\ReportCompareResource;
use Filament\Resources\Pages\Page;

class ReportCompare extends Page
{
    protected static string $resource = ReportCompareResource::class;

    protected static string $view = 'filament.resources.report-compare-resource.pages.report-compare';

    public function getBreadcrumb(): string
    {
        return 'Compare';
    }

    public function getTitle(): string
    {
        return 'Report Comparison';
    }
}
