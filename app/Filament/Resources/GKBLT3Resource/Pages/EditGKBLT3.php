<?php

namespace App\Filament\Resources\GKBLT3Resource\Pages;

use App\Filament\Resources\GKBLT3Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGKBLT3 extends EditRecord
{
    protected static string $resource = GKBLT3Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
