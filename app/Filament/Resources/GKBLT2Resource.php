<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GKBLT2Resource\Pages;
use App\Filament\Resources\GKBLT2Resource\RelationManagers;
use App\Models\GKBLT2;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GKBLT2Resource extends Resource
{
    // protected static ?string $model = GKBLT2::class;

    // protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $title = 'Mikrotik GKB LT2';

    protected static ?string $slug = 'mikrotik-gkb-lt2';

    protected static ?string $label = 'Mikrotik GKB LT2';

    protected static ?string $navigationLabel = 'Mikrotik LT2';

    protected static ?string $navigationGroup = 'GKB';

    protected static ?int $navigationSort = 2;

    protected static ?int $sort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\GKBLT2::route('/'),
        ];
    }
}