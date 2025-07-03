<?php

namespace App\Filament\Resources\ReportCompareResource\Pages;

use App\Filament\Resources\ReportCompareResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReportCompares extends ListRecords
{
    protected static string $resource = ReportCompareResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
