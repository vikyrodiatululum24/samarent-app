<?php

namespace App\Filament\Penjualan\Resources\JualUnitResource\Pages;

use App\Filament\Penjualan\Resources\JualUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewJualUnit extends ViewRecord
{
    protected static string $resource = JualUnitResource::class;

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
