<?php

namespace App\Filament\Resources\KunciSereps;

use App\Filament\Resources\KunciSereps\Pages;
use App\Filament\Resources\KunciSereps\Schemas\KunciSerepForm;
use App\Filament\Resources\KunciSereps\Schemas\KunciSerepInfolist;
use App\Filament\Resources\KunciSereps\Tables\KunciSerepTable;
use App\Models\KunciSerep;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class KunciSerepResource extends Resource
{
    protected static ?string $model = KunciSerep::class;
    
    protected static string | \UnitEnum | null $navigationGroup = 'Master Data';
    
    protected static ?string $navigationLabel = 'Data Kunci';
    
    protected static ?string $pluralLabel = 'Data Kunci';

    public static function form(Schema $schema): Schema
    {
        return KunciSerepForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KunciSerepTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KunciSerepInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKunciSereps::route('/'),
            'create' => Pages\CreateKunciSerep::route('/create'),
            'view' => Pages\ViewKunciSerep::route('/{record}'),
            'edit' => Pages\EditKunciSerep::route('/{record}/edit'),
        ];
    }

    public static function getCreateButtonLabel(): string
    {
        return 'Input Data Kunci';
    }
}
