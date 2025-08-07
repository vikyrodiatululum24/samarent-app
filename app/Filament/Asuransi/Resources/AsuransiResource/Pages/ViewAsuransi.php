<?php

namespace App\Filament\Asuransi\Resources\AsuransiResource\Pages;

use App\Filament\Asuransi\Resources\AsuransiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAsuransi extends ViewRecord
{
    protected static string $resource = AsuransiResource::class;

    //bottun untuk print asuransi
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('printAsuransi')
                ->label('Print Asuransi')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('print.asuransi', $this->record->id))
                ->openUrlInNewTab()
                ->color('primary')
                ->requiresConfirmation(),
        ];
    }
}
