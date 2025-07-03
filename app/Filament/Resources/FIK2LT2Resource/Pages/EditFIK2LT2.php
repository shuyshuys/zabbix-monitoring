<?php

namespace App\Filament\Resources\FIK2LT2Resource\Pages;

use App\Filament\Resources\FIK2LT2Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFIK2LT2 extends EditRecord
{
    protected static string $resource = FIK2LT2Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
