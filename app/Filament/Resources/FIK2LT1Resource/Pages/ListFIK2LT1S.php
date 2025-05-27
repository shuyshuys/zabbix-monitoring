<?php

namespace App\Filament\Resources\FIK2LT1Resource\Pages;

use App\Filament\Resources\FIK2LT1Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFIK2LT1S extends ListRecords
{
    protected static string $resource = FIK2LT1Resource::class;

    protected static ?string $title = 'Mikrotik GKB LT1';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
