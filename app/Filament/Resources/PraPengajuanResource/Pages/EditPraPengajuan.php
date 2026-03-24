<?php

namespace App\Filament\Resources\PraPengajuanResource\Pages;

use App\Filament\Resources\PraPengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPraPengajuan extends EditRecord
{
    protected static string $resource = PraPengajuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
