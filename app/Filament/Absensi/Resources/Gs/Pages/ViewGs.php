<?php

namespace App\Filament\Absensi\Resources\Gs\Pages;

use App\Filament\Absensi\Resources\Gs\GsResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGs extends ViewRecord
{
    protected static string $resource = GsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
