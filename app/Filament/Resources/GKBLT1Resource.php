<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GKBLT1Resource\Pages;
use App\Filament\Resources\GKBLT1Resource\RelationManagers;
use App\Models\GKBLT1;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GKBLT1Resource extends Resource
{
    protected static ?string $model = GKBLT1::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $title = 'Mikrotik GKB LT1';
    // start
    protected static ?string $slug = 'mikrotik-gkb-lt1';

    protected static ?string $label = 'Mikrotik GKB LT1';

    protected static ?string $navigationLabel = 'GKB LT1';

    protected static ?string $pluralLabel = 'Mikrotik GKB LT1';

    // protected static ?string $navigationGroup = 'Informasi Publik';

    protected static ?int $navigationSort = 1;

    protected static ?int $sort = 1;
    // end

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
                //
            ])
            ->bulkActions([
                //
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
            'index' => Pages\GKBLT1::route('/'),
        ];
    }
}
