<?php

namespace App\Filament\Resources\ZabbixResource\Pages;

use App\Filament\Resources\ZabbixResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditZabbix extends EditRecord
{
    protected static string $resource = ZabbixResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
