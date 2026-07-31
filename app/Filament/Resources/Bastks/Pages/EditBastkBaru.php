<?php

namespace App\Filament\Resources\Bastks\Pages;

use App\Filament\Resources\Bastks\BastkBaruResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBastkBaru extends EditRecord
{
    protected static string $resource = BastkBaruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
