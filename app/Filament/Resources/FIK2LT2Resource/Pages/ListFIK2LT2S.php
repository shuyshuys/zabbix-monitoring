<?php

namespace App\Filament\Resources\FIK2LT2Resource\Pages;

use App\Filament\Resources\FIK2LT2Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFIK2LT2S extends ListRecords
{
    protected static string $resource = FIK2LT2Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
