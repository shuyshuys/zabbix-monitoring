<?php

namespace App\Filament\Resources\ReportCompareResource\Pages;

use App\Filament\Resources\ReportCompareResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReportCompare extends EditRecord
{
    protected static string $resource = ReportCompareResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
