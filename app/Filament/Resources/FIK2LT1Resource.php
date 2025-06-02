<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FIK2LT1Resource\Pages;
use App\Filament\Resources\FIK2LT1Resource\RelationManagers;
use App\Models\FIK2LT1;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FIK2LT1Resource extends Resource
{
    // protected static ?string $model = FIK2LT1::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $title = 'Mikrotik FIK2 LT1';
    // start
    protected static ?string $slug = 'mikrotik-fik2-lt1';

    protected static ?string $label = 'Mikrotik FIK2 LT1';

    protected static ?string $navigationLabel = 'FIK2 LT1';

    protected static ?string $pluralLabel = 'Mikrotik FIK2 LT1';

    // protected static ?string $navigationGroup = 'Informasi Publik';

    protected static ?int $navigationSort = 4;

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
            'index' => Pages\FIK2LT1::route('/'),
        ];
    }
}
