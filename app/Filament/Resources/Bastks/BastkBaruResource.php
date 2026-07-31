<?php

namespace App\Filament\Resources\Bastks;

use App\Filament\Resources\Bastks\Pages\CreateBastkBaru;
use App\Filament\Resources\Bastks\Pages\EditBastkBaru;
use App\Filament\Resources\Bastks\Pages\ListBastkBarus;
use App\Filament\Resources\Bastks\Pages\ViewBastkBaru;
use App\Filament\Resources\Bastks\Schemas\BastkForm;
use App\Filament\Resources\Bastks\Schemas\BastkInfolist;
use App\Filament\Resources\Bastks\Tables\BastksTableBaru;
use App\Models\Bastk;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BastkBaruResource extends Resource
{
    protected static ?string $model = Bastk::class;

    protected static ?string $recordTitleAttribute = 'no_bastk';

    protected static string | \UnitEnum | null $navigationGroup = 'BASTK';

    protected static ?string $navigationLabel = 'BASTK Baru';

    protected static ?string $modelLabel = 'BASTK Baru';

    protected static ?string $pluralModelLabel = 'BASTK Baru';

    protected static ?string $slug = 'bastk-baru';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('jenis_bastk', 'new');
    }

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
        return BastksTableBaru::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBastkBarus::route('/'),
            'create' => CreateBastkBaru::route('/create'),
            'view' => ViewBastkBaru::route('/{record}'),
            'edit' => EditBastkBaru::route('/{record}/edit'),
        ];
    }
}
