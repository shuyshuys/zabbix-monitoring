<?php

namespace App\Filament\Resources\GKBLT1Resource\Pages;

use App\Filament\Resources\GKBLT1Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGKBLT1S extends ListRecords
{
    protected static string $resource = GKBLT1Resource::class;

    protected static ?string $title = 'Mikrotik GKB LT1';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
