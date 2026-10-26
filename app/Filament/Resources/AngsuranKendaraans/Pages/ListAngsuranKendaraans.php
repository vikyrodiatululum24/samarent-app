<?php

namespace App\Filament\Resources\AngsuranKendaraans\Pages;

use App\Exports\AngsuranKendaraanExport;
use App\Filament\Resources\AngsuranKendaraans\AngsuranKendaraanResource;
use App\Models\AngsuranKendaraan;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListAngsuranKendaraans extends ListRecords
{
    protected static string $resource = AngsuranKendaraanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_data')
                ->label('Ekspor Data')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->modalHeading('Ekspor Data Sewa & Angsuran Kendaraan')
                ->modalDescription('Pilih format dokumen yang ingin diekspor.')
                ->form([
                    Select::make('format')
                        ->label('Pilih Format Dokumen')
                        ->options([
                            'excel' => 'Microsoft Excel (.xlsx)',
                            'pdf' => 'Dokumen PDF (.pdf)',
                        ])
                        ->default('excel')
                        ->required(),

                    Select::make('status')
                        ->label('Filter Status Kontrak (Opsional)')
                        ->options([
                            '' => 'Semua Status',
                            'aktif' => 'Aktif',
                            'lunas' => 'Lunas',
                            'menunggak' => 'Menunggak',
                            'batal' => 'Dibatalkan',
                        ])
                        ->default(''),
                ])
                ->action(function (array $data) {
                    $format = $data['format'] ?? 'excel';
                    $status = !empty($data['status']) ? $data['status'] : null;

                    if ($format === 'pdf') {
                        $query = AngsuranKendaraan::with(['driver.user', 'unit', 'setorans', 'pengeluarans']);
                        if ($status) {
                            $query->where('status', $status);
                        }
                        $records = $query->orderBy('created_at', 'desc')->get();
                        $pdf = Pdf::loadView('pdf.angsuran-master', compact('records', 'status'))->setPaper('a4', 'landscape');
                        $filename = 'laporan_angsuran_kendaraan_' . now()->format('Ymd_His') . '.pdf';

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, $filename);
                    } else {
                        $filename = 'data_angsuran_kendaraan_' . now()->format('Ymd_His') . '.xlsx';
                        return response()->streamDownload(function () use ($status) {
                            echo Excel::raw(new AngsuranKendaraanExport(null, $status), \Maatwebsite\Excel\Excel::XLSX);
                        }, $filename);
                    }
                }),

            CreateAction::make()
                ->label('Tambah Kontrak Angsuran'),
        ];
    }
}
