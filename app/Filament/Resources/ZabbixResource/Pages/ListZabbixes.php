<?php

namespace App\Filament\Resources\ZabbixResource\Pages;

use App\Filament\Resources\ZabbixResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListZabbixes extends ListRecords
{
    protected static string $resource = ZabbixResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ZabbixResource\Widgets\ZabbixWidget::class,
        ];
    }
}