<?php

namespace App\Filament\Absensi\Resources\ProjectDriverManagementResource\Pages;

use App\Filament\Absensi\Resources\ProjectDriverManagementResource;
use Filament\Resources\Pages\ViewRecord;

class ViewProjectDriverManagement extends ViewRecord
{
    protected static string $resource = ProjectDriverManagementResource::class;

    protected static ?string $title = 'Detail Project Driver';

    protected function getSubNavigationItems(): array
    {
        return $this->getRelationManagersSubNavigation();
    }
}
