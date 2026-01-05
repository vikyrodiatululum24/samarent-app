<?php

namespace App\Filament\User\Resources\BbmResource\Pages;

use App\Filament\User\Resources\BbmResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBbm extends ViewRecord
{
    protected static string $resource = BbmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
