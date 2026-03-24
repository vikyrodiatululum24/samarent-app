<?php

namespace App\Filament\Resources\PraPengajuanResource\Pages;

use App\Exports\PraPengajuanExport;
use App\Filament\Resources\PraPengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListPraPengajuans extends ListRecords
{
    protected static string $resource = PraPengajuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $selectedUp = data_get($this->tableFilters, 'up.value');
                    $filenameSuffix = $selectedUp ? ('_' . preg_replace('/[^A-Za-z0-9_-]/', '-', $selectedUp)) : '_all';
                    $filename = 'pra_pengajuan' . $filenameSuffix . '_' . now()->format('Ymd_His') . '.xlsx';

                    return response()->streamDownload(function () use ($selectedUp) {
                        echo Excel::raw(new PraPengajuanExport($selectedUp), \Maatwebsite\Excel\Excel::XLSX);
                    }, $filename);
                }),
            Actions\CreateAction::make(),
        ];
    }
}
