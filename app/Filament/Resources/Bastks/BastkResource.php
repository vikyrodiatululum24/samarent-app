<?php

namespace App\Filament\Resources\Bastks;

use App\Filament\Resources\Bastks\Pages\CreateBastk;
use App\Filament\Resources\Bastks\Pages\EditBastk;
use App\Filament\Resources\Bastks\Pages\ListBastks;
use App\Filament\Resources\Bastks\Pages\ViewBastk;
use App\Filament\Resources\Bastks\Schemas\BastkForm;
use App\Filament\Resources\Bastks\Schemas\BastkInfolist;
use App\Filament\Resources\Bastks\Tables\BastksTable;
use App\Models\Bastk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BastkResource extends Resource
{
    protected static ?string $model = Bastk::class;

    protected static ?string $recordTitleAttribute = 'kode';

    public static function form(Schema $schema): Schema
    {
        return BastkForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BastkInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BastksTable::configure($table);
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
            'index' => ListBastks::route('/'),
            'create' => CreateBastk::route('/create'),
            'view' => ViewBastk::route('/{record}'),
            'edit' => EditBastk::route('/{record}/edit'),
        ];
    }
}
