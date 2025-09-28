<?php

namespace App\Filament\Resources\UnitJualResource\Pages;

use App\Filament\Resources\UnitJualResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUnitJual extends ViewRecord
{
    protected static string $resource = UnitJualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('laporan')
                    ->label('Print Laporan')
                    ->icon('heroicon-o-printer')
                    ->url(fn($record) => route('laporan-jualunit', $record->id))
                    ->openUrlInNewTab()
            ])
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('primary')
        ];
    }
}
