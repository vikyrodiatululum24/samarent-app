<?php

namespace App\Filament\Absensi\Resources\Signatures\Pages;

use App\Filament\Absensi\Resources\Signatures\SignatureResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSignature extends EditRecord
{
    protected static string $resource = SignatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
