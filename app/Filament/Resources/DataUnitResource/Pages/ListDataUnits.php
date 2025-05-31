<?php

namespace App\Filament\Resources\DataUnitResource\Pages;

use App\Filament\Resources\DataUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\ImportAction;
use App\Filament\Imports\DataUnitImporter;

class ListDataUnits extends ListRecords
{
    protected static string $resource = DataUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
            ->label('Import Unit')
            ->importer(DataUnitImporter::class),
            Actions\CreateAction::make()
            ->label('Tambah Unit'),
        ];
    }
}
