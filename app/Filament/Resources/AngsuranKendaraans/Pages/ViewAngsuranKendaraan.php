<?php

namespace App\Filament\Resources\AngsuranKendaraans\Pages;

use App\Exports\AngsuranKendaraanExport;
use App\Exports\AngsuranLengkapExport;
use App\Exports\AngsuranPengeluaranExport;
use App\Exports\AngsuranSetoranExport;
use App\Filament\Resources\AngsuranKendaraans\AngsuranKendaraanResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;
use Maatwebsite\Excel\Facades\Excel;

class ViewAngsuranKendaraan extends ViewRecord
{
    protected static string $resource = AngsuranKendaraanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_record')
                ->label('Ekspor Data')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->modalHeading(fn () => 'Ekspor Data Kontrak: ' . ($this->getRecord()->unit?->nopol ?? 'Unit'))
                ->modalDescription('Pilih format dokumen, cakupan data, dan rentang tanggal transaksi.')
                ->form([
                    Select::make('format')
                        ->label('Pilih Format Dokumen')
                        ->options([
                            'excel' => 'Microsoft Excel (.xlsx)',
                            'pdf' => 'Dokumen PDF (.pdf)',
                        ])
                        ->default('excel')
                        ->required(),

                    Select::make('tipe')
                        ->label('Cakupan Data')
                        ->options([
                            'lengkap' => 'Lengkap (Kontrak + Histori Setoran + Biaya Service)',
                            'master' => 'Ringkasan Kontrak Saja',
                            'setoran' => 'Riwayat Setoran Harian Saja',
                            'pengeluaran' => 'Riwayat Biaya & Service Saja',
                        ])
                        ->default('lengkap')
                        ->required(),

                    Grid::make(2)
                        ->schema([
                            DatePicker::make('start_date')
                                ->label('Tanggal Mulai (Start Date)')
                                ->placeholder('Pilih tanggal awal (opsional)'),

                            DatePicker::make('end_date')
                                ->label('Tanggal Akhir (End Date)')
                                ->placeholder('Pilih tanggal akhir (opsional)'),
                        ]),
                ])
                ->action(function (array $data) {
                    $record = $this->getRecord();
                    $format = $data['format'] ?? 'excel';
                    $tipe = $data['tipe'] ?? 'lengkap';
                    $startDate = !empty($data['start_date']) ? $data['start_date'] : null;
                    $endDate = !empty($data['end_date']) ? $data['end_date'] : null;
                    $nopol = $record->unit?->nopol ? str_replace(' ', '_', $record->unit->nopol) : 'unit';

                    if ($format === 'pdf') {
                        if ($tipe === 'lengkap') {
                            $pdf = Pdf::loadView('pdf.angsuran-lengkap', [
                                'angsurans' => collect([$record]),
                                'startDate' => $startDate,
                                'endDate' => $endDate,
                            ])->setPaper('a4', 'landscape');
                            $filename = 'kontrak_lengkap_' . $nopol . '_' . now()->format('Ymd_His') . '.pdf';
                        } elseif ($tipe === 'master') {
                            $pdf = Pdf::loadView('pdf.angsuran-master', [
                                'records' => collect([$record]),
                                'status' => $record->status,
                                'startDate' => $startDate,
                                'endDate' => $endDate,
                            ])->setPaper('a4', 'landscape');
                            $filename = 'kontrak_' . $nopol . '_' . now()->format('Ymd_His') . '.pdf';
                        } elseif ($tipe === 'setoran') {
                            $query = $record->setorans()->orderBy('tgl_setor', 'asc');
                            if ($startDate) {
                                $query->whereDate('tgl_setor', '>=', $startDate);
                            }
                            if ($endDate) {
                                $query->whereDate('tgl_setor', '<=', $endDate);
                            }
                            $records = $query->get();
                            $pdf = Pdf::loadView('pdf.angsuran-setoran', [
                                'records' => $records,
                                'angsuran' => $record,
                                'startDate' => $startDate,
                                'endDate' => $endDate,
                            ])->setPaper('a4', 'landscape');
                            $filename = 'setoran_' . $nopol . '_' . now()->format('Ymd_His') . '.pdf';
                        } else {
                            $query = $record->pengeluarans()->orderBy('tgl_pengeluaran', 'asc');
                            if ($startDate) {
                                $query->whereDate('tgl_pengeluaran', '>=', $startDate);
                            }
                            if ($endDate) {
                                $query->whereDate('tgl_pengeluaran', '<=', $endDate);
                            }
                            $records = $query->get();
                            $pdf = Pdf::loadView('pdf.angsuran-pengeluaran', [
                                'records' => $records,
                                'angsuran' => $record,
                                'startDate' => $startDate,
                                'endDate' => $endDate,
                            ])->setPaper('a4', 'landscape');
                            $filename = 'biaya_service_' . $nopol . '_' . now()->format('Ymd_His') . '.pdf';
                        }

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, $filename);
                    } else {
                        if ($tipe === 'lengkap') {
                            $filename = 'kontrak_lengkap_' . $nopol . '_' . now()->format('Ymd_His') . '.xlsx';
                            return response()->streamDownload(function () use ($record, $startDate, $endDate) {
                                echo Excel::raw(new AngsuranLengkapExport($record->id, null, $startDate, $endDate), \Maatwebsite\Excel\Excel::XLSX);
                            }, $filename);
                        } elseif ($tipe === 'master') {
                            $filename = 'kontrak_' . $nopol . '_' . now()->format('Ymd_His') . '.xlsx';
                            return response()->streamDownload(function () use ($record) {
                                echo Excel::raw(new AngsuranKendaraanExport([$record->id]), \Maatwebsite\Excel\Excel::XLSX);
                            }, $filename);
                        } elseif ($tipe === 'setoran') {
                            $filename = 'setoran_' . $nopol . '_' . now()->format('Ymd_His') . '.xlsx';
                            return response()->streamDownload(function () use ($record, $startDate, $endDate) {
                                echo Excel::raw(new AngsuranSetoranExport($record->id, null, $startDate, $endDate), \Maatwebsite\Excel\Excel::XLSX);
                            }, $filename);
                        } else {
                            $filename = 'biaya_service_' . $nopol . '_' . now()->format('Ymd_His') . '.xlsx';
                            return response()->streamDownload(function () use ($record, $startDate, $endDate) {
                                echo Excel::raw(new AngsuranPengeluaranExport($record->id, null, $startDate, $endDate), \Maatwebsite\Excel\Excel::XLSX);
                            }, $filename);
                        }
                    }
                }),
            EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\AngsuranKendaraans\Widgets\AngsuranOverviewWidget::class,
        ];
    }
}
