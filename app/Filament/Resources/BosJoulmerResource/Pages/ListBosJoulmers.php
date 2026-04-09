<?php

namespace App\Filament\Resources\BosJoulmerResource\Pages;

use App\Filament\Resources\BosJoulmerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBosJoulmers extends ListRecords
{
    protected static string $resource = BosJoulmerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('info')
                ->label('Review Pengajuan Bos')
                ->disabled(),
        ];
    }
}
