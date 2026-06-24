<?php

namespace App\Filament\Absensi\Resources\Signatures\Pages;

use App\Filament\Absensi\Resources\Signatures\SignatureResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSignature extends ViewRecord
{
    protected static string $resource = SignatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
