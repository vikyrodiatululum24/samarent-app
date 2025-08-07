<?php

namespace App\Filament\Asuransi\Resources\AsuransiResource\Pages;

use App\Filament\Asuransi\Resources\AsuransiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAsuransis extends ListRecords
{
    protected static string $resource = AsuransiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
