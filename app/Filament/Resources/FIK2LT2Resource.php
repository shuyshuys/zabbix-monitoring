<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FIK2LT2Resource\Pages;
use App\Filament\Resources\FIK2LT2Resource\RelationManagers;
use App\Models\FIK2LT2;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FIK2LT2Resource extends Resource
{
    // protected static ?string $model = FIK2LT2::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $title = 'Mikrotik FIK2 LT2';
    // start
    protected static ?string $slug = 'mikrotik-fik2-lt2';

    protected static ?string $label = 'Mikrotik FIK2 LT2';

    protected static ?string $navigationLabel = 'LT2 - CCR1009-7G-1C-1S+';

    protected static ?string $pluralLabel = 'FIK2 LT2';

    protected static ?string $navigationGroup = 'FIK';

    protected static ?int $navigationSort = 1;

    protected static ?int $sort = 4;
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
            'index' => Pages\FIK2LT2::route('/'),
        ];
    }
}
