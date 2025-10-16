<?php

namespace App\Filament\Absensi\Resources\SetSalaryResource\Pages;

use App\Filament\Absensi\Resources\SetSalaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSetSalary extends EditRecord
{
    protected static string $resource = SetSalaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
