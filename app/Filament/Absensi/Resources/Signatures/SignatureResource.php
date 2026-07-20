<?php

namespace App\Filament\Absensi\Resources\Signatures;

use App\Filament\Absensi\Resources\Signatures\Pages\CreateSignature;
use App\Filament\Absensi\Resources\Signatures\Pages\EditSignature;
use App\Filament\Absensi\Resources\Signatures\Pages\ListSignatures;
use App\Filament\Absensi\Resources\Signatures\Pages\ViewSignature;
use App\Filament\Absensi\Resources\Signatures\Schemas\SignatureForm;
use App\Filament\Absensi\Resources\Signatures\Schemas\SignatureInfolist;
use App\Filament\Absensi\Resources\Signatures\Tables\SignaturesTable;
use App\Models\GroupSignature;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SignatureResource extends Resource
{
    protected static ?string $model = GroupSignature::class;

    protected static ?string $recordTitleAttribute = 'signature';

    protected static ?string $label = 'Signature';

    protected static string | \UnitEnum | null $navigationGroup = 'Master Data';

    protected static ?string $pluralModelLabel = 'Signature';

    public static function form(Schema $schema): Schema
    {
        return SignatureForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SignatureInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SignaturesTable::configure($table);
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
            'index' => ListSignatures::route('/'),
            'create' => CreateSignature::route('/create'),
            'view' => ViewSignature::route('/{record}'),
            'edit' => EditSignature::route('/{record}/edit'),
        ];
    }
}
