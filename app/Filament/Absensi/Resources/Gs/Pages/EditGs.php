<?php

namespace App\Filament\Absensi\Resources\Gs\Pages;

use App\Filament\Absensi\Resources\Gs\GsResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGs extends EditRecord
{
    protected static string $resource = GsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
