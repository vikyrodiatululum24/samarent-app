<?php

namespace App\Filament\Resources\BengkelResource\Pages;

use App\Filament\Resources\BengkelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBengkel extends EditRecord
{
    protected static string $resource = BengkelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
