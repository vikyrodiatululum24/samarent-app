<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanKeuanganServiceResource\Pages;
use App\Models\KeuanganService;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LaporanKeuanganServiceResource extends Resource
{
    protected static ?string $model = KeuanganService::class;

    protected static ?string $navigationLabel = 'Laporan Keuangan Service';

    protected static ?string $label = 'Laporan Keuangan Service';

    private static function getTotalAll(): array
    {
        $query = static::getEloquentQuery();
        $totals = $query->with(['pengajuan.complete'])->get()->reduce(function ($carry, $item) {
            $carry['finance'] += $item->pengajuan->complete->nominal_tf_finance ?? 0;
            $carry['bengkel'] += $item->pengajuan->complete->nominal_tf_bengkel ?? 0;
            $carry['selisih'] += $item->pengajuan->complete->selisih_tf ?? 0;
            return $carry;
        }, ['finance' => 0, 'bengkel' => 0, 'selisih' => 0]);

        return $totals;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Select::make('pengajuan_id')
                    ->label('No Pengajuan')
                    ->options(\App\Models\Pengajuan::pluck('no_pengajuan', 'id'))
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        // Get total for header
        $totals = static::getTotalAll();

        $headerText = sprintf(
            'Total Keseluruhan - Nominal TF Finance: Rp %s | Nominal TF Bengkel: Rp %s | Selisih: Rp %s',
            number_format($totals['finance'], 0, ',', '.'),
            number_format($totals['bengkel'], 0, ',', '.'),
            number_format($totals['selisih'], 0, ',', '.')
        );

        return $table
            ->heading($headerText)
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pengajuan.no_pengajuan')
                    ->label('No Pengajuan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rek_penerima')
                    ->label('Rek. Penerima')
                    ->getStateUsing(function ($record) {
                        $complete = $record->pengajuan->complete;
                        if (!$complete) return '';
                        return implode(' - ', [
                            $complete->payment_2,
                            $complete->norek_2,
                            $complete->bank_2,
                        ]);
                    })
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('rek_bengkel')
                    ->label('Rek. Bengkel')
                    ->getStateUsing(function ($record) {
                        $pengajuan = $record->pengajuan;
                        if (!$pengajuan) return '';
                        return implode(' - ', [
                            $pengajuan->payment_1,
                            $pengajuan->norek_1,
                            $pengajuan->bank_1,
                        ]);
                    })
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nopol')
                    ->label('No Polisi')
                    ->getStateUsing(function ($record) {
                        $nopols = $record->pengajuan?->service_unit?->map(function ($service_unit) {
                            return $service_unit->unit?->nopol;
                        })->filter()->join(', ');
                        return $nopols ?: '-';
                    })
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pengajuan.keterangan')
                    ->label('Keterangan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pengajuan.complete.nominal_tf_finance')
                    ->label('Nominal TF Finance')
                    ->formatStateUsing(fn($state) => is_numeric($state) ? 'Rp ' . number_format($state, 0, ',', '.') : '')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pengajuan.complete.nominal_tf_bengkel')
                    ->label('Nominal TF Bengkel')
                    ->formatStateUsing(fn($state) => is_numeric($state) ? 'Rp ' . number_format($state, 0, ',', '.') : '')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pengajuan.complete.selisih_tf')
                    ->label('Selisih')
                    ->formatStateUsing(fn($state) => is_numeric($state) ? 'Rp ' . number_format($state, 0, ',', '.') : '')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        \Filament\Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn($query) => $query->whereDate('created_at', '>=', $data['dari_tanggal'])
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn($query) => $query->whereDate('created_at', '<=', $data['sampai_tanggal'])
                            );
                    })
                    ->indicateUsing(function (array $data): string {
                        if (!$data['dari_tanggal'] && !$data['sampai_tanggal']) {
                            return '';
                        }

                        if (!$data['sampai_tanggal']) {
                            return 'Dari ' . \Carbon\Carbon::parse($data['dari_tanggal'])->format('d M Y');
                        }

                        if (!$data['dari_tanggal']) {
                            return 'Sampai ' . \Carbon\Carbon::parse($data['sampai_tanggal'])->format('d M Y');
                        }

                        return 'Dari ' . \Carbon\Carbon::parse($data['dari_tanggal'])->format('d M Y') . ' sampai ' . \Carbon\Carbon::parse($data['sampai_tanggal'])->format('d M Y');
                    }),
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($livewire) {
                        $data = self::getFilteredData($livewire);
                        $filters = [
                            'dari_tanggal' => $livewire->tableFilters['created_at']['dari_tanggal'] ?? null,
                            'sampai_tanggal' => $livewire->tableFilters['created_at']['sampai_tanggal'] ?? null,
                        ];
                        return response()->streamDownload(function () use ($data, $filters) {
                            echo self::generateExcel($data, $filters);
                        }, 'laporan_keuangan_service_' . date('Y-m-d') . '.xlsx');
                    }),
                \Filament\Tables\Actions\Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-text')
                    ->action(function ($livewire) {
                        $data = self::getFilteredData($livewire);
                        $filters = [
                            'dari_tanggal' => $livewire->tableFilters['created_at']['dari_tanggal'] ?? null,
                            'sampai_tanggal' => $livewire->tableFilters['created_at']['sampai_tanggal'] ?? null,
                        ];
                        return response()->streamDownload(function () use ($data, $filters) {
                            echo self::generatePDF($data, $filters);
                        }, 'laporan_keuangan_service_' . date('Y-m-d') . '.pdf');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanKeuanganServices::route('/'),
            'create' => Pages\CreateLaporanKeuanganService::route('/create'),
            'edit' => Pages\EditLaporanKeuanganService::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->email === 'centralakun@samarent.com';
    }

    private static function getFilteredData($livewire): \Illuminate\Database\Eloquent\Collection
    {
        $query = static::getEloquentQuery();

        // Apply date filter if exists
        if ($livewire->tableFilters['created_at']['dari_tanggal'] ?? null) {
            $query->whereDate('created_at', '>=', $livewire->tableFilters['created_at']['dari_tanggal']);
        }
        if ($livewire->tableFilters['created_at']['sampai_tanggal'] ?? null) {
            $query->whereDate('created_at', '<=', $livewire->tableFilters['created_at']['sampai_tanggal']);
        }

        // Eager load all required relationships
        return $query->with([
            'pengajuan',
            'pengajuan.service_unit',
            'pengajuan.service_unit.unit',
            'pengajuan.complete'
        ])->get();
    }

    private static function generateExcel($data, $filters = [])
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'Laporan Keuangan Service');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set date range
        $fromDate = !empty($filters['dari_tanggal']) ? date('d/m/Y', strtotime($filters['dari_tanggal'])) : '-';
        $toDate = !empty($filters['sampai_tanggal']) ? date('d/m/Y', strtotime($filters['sampai_tanggal'])) : '-';

        $sheet->setCellValue('A2', 'Periode: ' . $fromDate . ' s/d ' . $toDate);
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Add empty row
        $sheet->setCellValue('A3', '');

        // Set header
        $sheet->setCellValue('A4', 'Tanggal');
        $sheet->setCellValue('B4', 'No Pengajuan');
        $sheet->setCellValue('C4', 'Rek. Penerima');
        $sheet->setCellValue('D4', 'Rek. Bengkel');
        $sheet->setCellValue('E4', 'No Polisi');
        $sheet->setCellValue('F4', 'Keterangan');
        $sheet->setCellValue('G4', 'Nominal TF Finance');
        $sheet->setCellValue('H4', 'Nominal TF Bengkel');
        $sheet->setCellValue('I4', 'Selisih');

        // Style header
        $headerRange = 'A4:I4';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E4E4E4');

        $row = 5; // Start from row 5 because we have title and date range
        $totalFinance = 0;
        $totalBengkel = 0;
        $totalSelisih = 0;

        foreach ($data as $item) {
            // Increment row counter
            $currentRow = $row++;

            $sheet->setCellValue('A' . $currentRow, $item->created_at->format('d/m/Y'));
            $sheet->setCellValue('B' . $currentRow, $item->pengajuan->no_pengajuan);

            // Format rek penerima
            $complete = $item->pengajuan->complete;
            $rekPenerima = $complete ? implode(' - ', [
                $complete->payment_2,
                $complete->norek_2,
                $complete->bank_2,
            ]) : '-';
            $sheet->setCellValue('C' . $currentRow, $rekPenerima);

            // Format rek bengkel
            $pengajuan = $item->pengajuan;
            $rekBengkel = $pengajuan ? implode(' - ', [
                $pengajuan->payment_1,
                $pengajuan->norek_1,
                $pengajuan->bank_1,
            ]) : '-';
            $sheet->setCellValue('D' . $currentRow, $rekBengkel);

            $nopols = $item->pengajuan?->service_unit?->map(function ($service_unit) {
                return $service_unit->unit?->nopol;
            })->filter()->join(', ');
            $sheet->setCellValue('E' . $currentRow, $nopols ?: '-');
            $sheet->setCellValue('F' . $currentRow, $item->pengajuan->keterangan);
            $nominalFinance = $item->pengajuan->complete->nominal_tf_finance ?? 0;
            $nominalBengkel = $item->pengajuan->complete->nominal_tf_bengkel ?? 0;
            $selisihTf = $item->pengajuan->complete->selisih_tf ?? 0;

            $sheet->setCellValue('G' . $currentRow, $nominalFinance);
            $sheet->setCellValue('H' . $currentRow, $nominalBengkel);
            $sheet->setCellValue('I' . $currentRow, $selisihTf);

            // Add to totals
            $totalFinance += $nominalFinance;
            $totalBengkel += $nominalBengkel;
            $totalSelisih += $selisihTf;

            // Format currency cells
            $sheet->getStyle('G' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('H' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('I' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
        }

        // Add totals row
        $lastRow = $row - 1;
        // Merge cells from A to F for the "TOTAL" label
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('G' . $row, "=SUM(G5:G{$lastRow})");
        $sheet->setCellValue('H' . $row, "=SUM(H5:H{$lastRow})");
        $sheet->setCellValue('I' . $row, "=SUM(I5:I{$lastRow})");

        // Format total row
        $sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setBold(true);
        $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // Auto size columns

        // Auto size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to all cells in the table
        $lastRow = $row;
        $tableRange = 'A4:I' . $lastRow;

        // Apply thin borders to all cells
        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Apply medium border only to the outer edges of the table
        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                ],
            ],
        ]);

        // Center align certain columns
        $sheet->getStyle('A5:A' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Tanggal
        $sheet->getStyle('B5:B' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // No Pengajuan
        $sheet->getStyle('E5:E' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // No Polisi

        // Right align numeric columns
        $sheet->getStyle('G5:I' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }

    private static function generatePDF($data, $filters = [])
    {
        // Set tanggal filter
        $dari_tanggal = !empty($filters['dari_tanggal']) ? date('d/m/Y', strtotime($filters['dari_tanggal'])) : '-';
        $sampai_tanggal = !empty($filters['sampai_tanggal']) ? date('d/m/Y', strtotime($filters['sampai_tanggal'])) : '-';

        // Hitung total terlebih dahulu
        $totalFinance = 0;
        $totalBengkel = 0;
        $totalSelisih = 0;

        foreach ($data as $item) {
            $totalFinance += $item->pengajuan->complete->nominal_tf_finance ?? 0;
            $totalBengkel += $item->pengajuan->complete->nominal_tf_bengkel ?? 0;
            $totalSelisih += $item->pengajuan->complete->selisih_tf ?? 0;
        }

        // Helper functions untuk format data
        $getNopols = function ($item) {
            $nopols = $item->pengajuan?->service_unit?->map(function ($service_unit) {
                return $service_unit->unit?->nopol;
            })->filter()->join(', ');
            return $nopols ?: '-';
        };

        $rekPenerima = function ($item) {
            $complete = $item->pengajuan->complete;
            return $complete ? implode(' - ', [
                $complete->payment_2,
                $complete->norek_2,
                $complete->bank_2,
            ]) : '-';
        };

        $rekBengkel = function ($item) {
            $pengajuan = $item->pengajuan;
            return $pengajuan ? implode(' - ', [
                $pengajuan->payment_1,
                $pengajuan->norek_1,
                $pengajuan->bank_1,
            ]) : '-';
        };

        $nominalFinance = function ($item) {
            return $item->pengajuan->complete->nominal_tf_finance ?? 0;
        };

        $nominalBengkel = function ($item) {
            return $item->pengajuan->complete->nominal_tf_bengkel ?? 0;
        };

        $selisihTf = function ($item) {
            return $item->pengajuan->complete->selisih_tf ?? 0;
        };

        // Render view ke HTML
        $html = view('filament.laporan-keuangan.pdf', compact(
            'data',
            'totalFinance',
            'totalBengkel',
            'totalSelisih',
            'getNopols',
            'rekPenerima',
            'rekBengkel',
            'nominalFinance',
            'nominalBengkel',
            'selisihTf'
        ))
            ->with([
                'tanggal' => date('d/m/Y'),
                'dari_tanggal' => $dari_tanggal,
                'sampai_tanggal' => $sampai_tanggal
            ])
            ->render();

        // Generate PDF using Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->output();
    }
}
