<?php

namespace App\Filament\Resources\Bastks;

use App\Filament\Resources\Bastks\Pages\CreateBastkLama;
use App\Filament\Resources\Bastks\Pages\EditBastkLama;
use App\Filament\Resources\Bastks\Pages\ListBastkLamas;
use App\Filament\Resources\Bastks\Pages\ViewBastkLama;
use App\Filament\Resources\Bastks\Schemas\BastkFormLama;
use App\Filament\Resources\Bastks\Schemas\BastkInfolist;
use App\Filament\Resources\Bastks\Tables\BastksTableLama;
use App\Models\Bastk;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BastkLamaResource extends Resource
{
    protected static ?string $model = Bastk::class;

    protected static ?string $recordTitleAttribute = 'no_bastk';

    protected static string | \UnitEnum | null $navigationGroup = 'BASTK';

    protected static ?string $navigationLabel = 'BASTK Lama';

    protected static ?string $modelLabel = 'BASTK Lama';

    protected static ?string $pluralModelLabel = 'BASTK Lama';

    protected static ?string $slug = 'bastk-lama';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('jenis_bastk', 'old');
    }

    public static function form(Schema $schema): Schema
    {
        return BastkFormLama::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BastkInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BastksTableLama::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBastkLamas::route('/'),
            'create' => CreateBastkLama::route('/create'),
            'view' => ViewBastkLama::route('/{record}'),
            'edit' => EditBastkLama::route('/{record}/edit'),
        ];
    }
}
