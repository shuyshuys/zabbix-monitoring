<?php

namespace App\Filament\Resources\GKBLT2Resource\Pages;

use App\Filament\Resources\GKBLT2Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGKBLT2 extends ListRecords
{
    protected static string $resource = GKBLT2Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
