<?php

namespace App\Filament\Resources\FIK2LT1Resource\Pages;

use App\Filament\Resources\FIK2LT1Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFIK2LT1 extends EditRecord
{
    protected static string $resource = FIK2LT1Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
