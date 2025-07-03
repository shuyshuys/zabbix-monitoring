<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportCompareResource\Pages;
use App\Filament\Resources\ReportCompareResource\RelationManagers;
use App\Models\ReportCompare;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReportCompareResource extends Resource
{
    // protected static ?string $model = ReportCompare::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $title = 'Report Compare';
    // start
    protected static ?string $slug = 'compare';

    protected static ?string $label = 'Report Compare';

    protected static ?string $navigationLabel = 'Compare';

    protected static ?string $pluralLabel = 'Report';

    protected static ?string $navigationGroup = 'Report';

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
            'index' => Pages\ReportCompare::route('/'),
        ];
    }
}
