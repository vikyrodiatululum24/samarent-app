<?php

namespace App\Filament\Manager\Resources\PengajuanResource\Pages;

use App\Filament\Manager\Resources\PengajuanResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ListPengajuans extends ListRecords
{
    protected static string $resource = PengajuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('exportFiltered')
            ->label('Export Data Pengajuan')
            ->form([
                DatePicker::make('from_date')->label('Dari Tanggal')->required(),
                DatePicker::make('to_date')->label('Sampai Tanggal')->required(),
            ])
            ->action(function (array $data) {
                $project = Auth::user()->manager->perusahaan ?? "";
                $up = Auth::user()->manager->up ?? "";
                $from = $data['from_date'];
                $to = $data['to_date'];
                $filename = 'service_units_' . now()->format('Ymd_His') . '.xlsx';

                return response()->streamDownload(function () use ($from, $to, $project, $up) {
                    echo Excel::raw(new \App\Exports\ServiceUnitExportManager($from, $to, $project, $up), \Maatwebsite\Excel\Excel::XLSX);
                }, $filename);
            })
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success'),
        ];
    }
}
