<?php

namespace App\Filament\President\Resources\BosJoulmerApprovedResource\Pages;

use App\Filament\President\Resources\BosJoulmerApprovedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBosJoulmersApproved extends ListRecords
{
    protected static string $resource = BosJoulmerApprovedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('info')
                ->label('Pengajuan Disetujui')
                ->disabled(),
        ];
    }
}
