<?php

namespace App\Filament\President\Resources\BosJoulmerResource\Pages;

use App\Filament\President\Resources\BosJoulmerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBosJoulmers extends ListRecords
{
    protected static string $resource = BosJoulmerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('info')
                ->label('Review Pengajuan')
                ->disabled(),
        ];
    }
}
