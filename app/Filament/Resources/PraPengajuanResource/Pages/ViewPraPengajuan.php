<?php

namespace App\Filament\Resources\PraPengajuanResource\Pages;

use App\Filament\Resources\PraPengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPraPengajuan extends ViewRecord
{
    protected static string $resource = PraPengajuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('pengajuan')
                ->label('Ajukan')
                ->url(fn() => route('ajukan-pra-pengajuan', $this->record)),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
