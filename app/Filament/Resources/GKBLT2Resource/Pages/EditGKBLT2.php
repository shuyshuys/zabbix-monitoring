<?php

namespace App\Filament\Resources\GKBLT2Resource\Pages;

use App\Filament\Resources\GKBLT2Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGKBLT2 extends EditRecord
{
    protected static string $resource = GKBLT2Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
