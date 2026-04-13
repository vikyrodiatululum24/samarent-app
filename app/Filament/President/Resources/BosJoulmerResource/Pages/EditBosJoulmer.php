<?php

namespace App\Filament\President\Resources\BosJoulmerResource\Pages;

use App\Filament\President\Resources\BosJoulmerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBosJoulmer extends EditRecord
{
    protected static string $resource = BosJoulmerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
