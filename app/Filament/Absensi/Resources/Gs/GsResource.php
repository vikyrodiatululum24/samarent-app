<?php

namespace App\Filament\Absensi\Resources\Gs;

use App\Filament\Absensi\Resources\Gs\Pages\CreateGs;
use App\Filament\Absensi\Resources\Gs\Pages\EditGs;
use App\Filament\Absensi\Resources\Gs\Pages\ListGs;
use App\Filament\Absensi\Resources\Gs\Pages\ViewGs;
use App\Filament\Absensi\Resources\Gs\Schemas\GsForm;
use App\Filament\Absensi\Resources\Gs\Schemas\GsInfolist;
use App\Filament\Absensi\Resources\Gs\Tables\GsTable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Gs;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GsResource extends Resource
{
    protected static ?string $model = Gs::class;

    protected static ?string $recordTitleAttribute = 'record-title';

    public static function getRecordTitle(?Model $record): string
    {
        return $record?->driver?->user->name ?? 'Tidak ada driver';
    }

    public static function form(Schema $schema): Schema
    {
        return GsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GsTable::configure($table);
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
            'index' => ListGs::route('/'),
            'create' => CreateGs::route('/create'),
            'view' => ViewGs::route('/{record}'),
            'edit' => EditGs::route('/{record}/edit'),
        ];
    }
}
