<?php

namespace App\Filament\Resources\KunciSereps\Pages;

use App\Filament\Resources\KunciSereps\KunciSerepResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKunciSerep extends ViewRecord
{
    protected static string $resource = KunciSerepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
