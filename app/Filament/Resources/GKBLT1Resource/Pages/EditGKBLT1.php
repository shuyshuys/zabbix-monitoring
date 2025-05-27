<?php

namespace App\Filament\Resources\GKBLT1Resource\Pages;

use App\Filament\Resources\GKBLT1Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGKBLT1 extends EditRecord
{
    protected static string $resource = GKBLT1Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
