<?php

namespace App\Filament\Resources\ProsesResource\Pages;

use App\Filament\Resources\ProsesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProses extends EditRecord
{
    protected static string $resource = ProsesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
