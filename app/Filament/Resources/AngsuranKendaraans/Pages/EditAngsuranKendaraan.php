<?php

namespace App\Filament\Resources\AngsuranKendaraans\Pages;

use App\Filament\Resources\AngsuranKendaraans\AngsuranKendaraanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAngsuranKendaraan extends EditRecord
{
    protected static string $resource = AngsuranKendaraanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
