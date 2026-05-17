<?php

namespace App\Filament\Absensi\Resources\SetSalaryResource\Pages;

use App\Filament\Absensi\Resources\SetSalaryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateSetSalary extends CreateRecord
{
    protected static string $resource = SetSalaryResource::class;

    public function getMaxContentWidth(): Width
    {
        return Width::SevenExtraLarge;
    }
}
