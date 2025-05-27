<?php

namespace App\Filament\Resources\GKBLT3Resource\Pages;

use App\Filament\Resources\GKBLT3Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGKBLT3S extends ListRecords
{
    protected static string $resource = GKBLT3Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
