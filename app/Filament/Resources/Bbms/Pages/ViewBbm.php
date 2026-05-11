<?php

namespace App\Filament\Resources\Bbms\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\Bbms\BbmResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBbm extends ViewRecord
{
    protected static string $resource = BbmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

