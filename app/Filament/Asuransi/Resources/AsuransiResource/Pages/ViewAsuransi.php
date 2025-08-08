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
        // return [
        //     Actions\Action::make('printAsuransi')
        //         ->label('Print Asuransi')
        //         ->icon('heroicon-o-printer')
        //         ->url(fn () => route('print.asuransi', $this->record->id))
        //         ->openUrlInNewTab()
        //         ->color('primary')
        //         ->requiresConfirmation(),
        // ];
                return [
            Actions\ActionGroup::make([
                Actions\Action::make('print_spk')
                    ->label('Preview')
                    ->icon('heroicon-o-printer')
                    ->url(fn($record) => route('preview.asuransi', $record->id))
                    ->openUrlInNewTab(),
                Actions\Action::make('print_spk')
                    ->label('Print SPK')
                    ->icon('heroicon-o-printer')
                    ->url(fn($record) => route('print.asuransi', $record->id))
                    ->openUrlInNewTab()
                    ->badge(fn($record) => \App\Models\Cetak::where('asuransi_id', $record->id)->exists() ? 'Sudah di-print' : null)
            ])
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('primary')
        ];
    }
}
