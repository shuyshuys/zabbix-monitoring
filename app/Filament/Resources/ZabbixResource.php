<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ZabbixResource\Pages;
use App\Filament\Resources\ZabbixResource\RelationManagers;
use App\Models\Zabbix;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ZabbixResource extends Resource
{
    protected static ?string $model = Zabbix::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
            'index' => Pages\ListZabbixes::route('/'),
            'create' => Pages\CreateZabbix::route('/create'),
            'edit' => Pages\EditZabbix::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Resources\ZabbixResource\Widgets\ZabbixWidget::class,
        ];
    }
}