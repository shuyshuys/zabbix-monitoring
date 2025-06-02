<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GKBLT3Resource\Pages;
use App\Filament\Resources\GKBLT3Resource\RelationManagers;
use App\Models\GKBLT3;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GKBLT3Resource extends Resource
{
    // protected static ?string $model = GKBLT3::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $title = 'Mikrotik GKB LT3';

    protected static ?string $slug = 'mikrotik-gkb-lt3';

    protected static ?string $label = 'Mikrotik GKB LT3';

    protected static ?string $navigationLabel = 'GKB LT3';

    protected static ?int $navigationSort = 3;

    protected static ?int $sort = 3;

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
            'index' => Pages\GKBLT3::route('/'),
        ];
    }
}
