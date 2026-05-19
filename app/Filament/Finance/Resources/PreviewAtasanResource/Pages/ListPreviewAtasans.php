<?php

namespace App\Filament\Finance\Resources\PreviewAtasanResource\Pages;

use App\Filament\Finance\Resources\PreviewAtasanResource;
use App\Filament\Finance\Resources\PengajuanResource\Pages\ListPengajuans;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Maatwebsite\Excel\Facades\Excel;

class ListPreviewAtasans extends ListPengajuans
{
    protected static string $resource = PreviewAtasanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportFiltered')
                ->label('Export Data Pengajuan')
                ->form([
                    DatePicker::make('from_date')->label('Dari Tanggal')->required(),
                    DatePicker::make('to_date')->label('Sampai Tanggal')->required(),
                ])
                ->action(function (array $data) {
                    $from = $data['from_date'];
                    $to = $data['to_date'];
                    $filename = 'service_units_' . now()->format('Ymd_His') . '.xlsx';

                    return response()->streamDownload(function () use ($from, $to) {
                        echo Excel::raw(new \App\Exports\ServiceUnitExport($from, $to), \Maatwebsite\Excel\Excel::XLSX);
                    }, $filename);
                })
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success'),
        ];
    }
}
