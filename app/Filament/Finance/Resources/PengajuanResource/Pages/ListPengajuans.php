<?php

namespace App\Filament\Finance\Resources\PengajuanResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Finance\Resources\PengajuanResource;

class ListPengajuans extends ListRecords
{
    protected static string $resource = PengajuanResource::class;

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

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'OP' => Tab::make('OP')
                ->modifyQueryUsing(fn ($query) =>
                    $query->whereHas('complete', fn ($q) => $q->where('kode', 'op'))
                ),
            'SC' => Tab::make('SC')
                ->modifyQueryUsing(fn ($query) =>
                    $query->whereHas('complete', fn ($q) => $q->where('kode', 'sc'))
                ),
            'SP' => Tab::make('SP')
                ->modifyQueryUsing(fn ($query) =>
                    $query->whereHas('complete', fn ($q) => $q->where('kode', 'sp'))
                ),
            'STNK' => Tab::make('STNK')
                ->modifyQueryUsing(fn ($query) =>
                    $query->whereHas('complete', fn ($q) => $q->where('kode', 'stnk'))
                ),
        ];
    }
}
