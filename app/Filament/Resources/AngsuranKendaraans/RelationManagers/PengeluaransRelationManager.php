<?php

namespace App\Filament\Resources\AngsuranKendaraans\RelationManagers;

use App\Exports\AngsuranPengeluaranExport;
use App\Models\AngsuranPengeluaran;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class PengeluaransRelationManager extends RelationManager
{
    protected static string $relationship = 'pengeluarans';

    protected static ?string $title = 'Biaya & Service Kendaraan (Klaim Penyewa)';

    protected static ?string $modelLabel = 'Biaya Service';

    protected static ?string $pluralModelLabel = 'Biaya Service';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tgl_pengeluaran')
                    ->label('Tanggal Pengeluaran')
                    ->default(now())
                    ->required(),

                TextInput::make('jenis_pengeluaran')
                    ->label('Jenis Pengeluaran / Perbaikan')
                    ->placeholder('Contoh: Ganti Oli Mesin, Ganti Ban, Service Rutin')
                    ->required(),

                TextInput::make('nominal')
                    ->label('Nominal Biaya Dikeluarkan')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => $state !== null ? (int) round((float) $state) : null)
                    ->dehydrateStateUsing(fn ($state) => $state !== null ? (float) $state : null)
                    ->prefix('Rp')
                    ->required(),

                Select::make('status')
                    ->label('Status Penggantian')
                    ->options([
                        'diajukan' => 'Diajukan',
                        'disetujui' => 'Disetujui',
                        'dikompensasi_sebagian' => 'Sebagian Dikompensasi ke Setoran',
                        'sudah_dikompensasi' => 'Sudah Dikompensasi ke Setoran',
                        'ditolak' => 'Ditolak',
                    ])
                    ->default('disetujui')
                    ->required(),

                FileUpload::make('bukti_nota')
                    ->label('Foto Nota / Kuitansi Bengkel')
                    ->image()
                    ->disk('public')
                    ->directory('bukti_nota_angsuran')
                    ->nullable()
                    ->columnSpanFull(),

                Textarea::make('keterangan')
                    ->label('Keterangan Tambahan')
                    ->rows(2)
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('jenis_pengeluaran')
            ->description(function ($livewire) {
                $record = $livewire->ownerRecord;
                if (! $record) return null;
                $totalKlaim = number_format($record->pengeluarans()->sum('nominal'), 0, ',', '.');
                $terkompensasi = number_format($record->pengeluarans->sum(fn ($p) => $p->total_terkompensasi), 0, ',', '.');
                $sisaKlaim = number_format($record->pengeluarans->sum(fn ($p) => $p->sisa_kompensasi), 0, ',', '.');
                return "Ringkasan Biaya Service: Total Klaim Rp {$totalKlaim} • Telah Terkompensasi Rp {$terkompensasi} • Sisa Saldo Nota Rp {$sisaKlaim}";
            })
            ->columns([
                TextColumn::make('tgl_pengeluaran')
                    ->label('Tanggal')
                    ->date('d-M-Y')
                    ->sortable(),

                TextColumn::make('jenis_pengeluaran')
                    ->label('Jenis Perbaikan / Service')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('nominal')
                    ->label('Total Biaya')
                    ->money('IDR', locale: 'id')
                    ->weight('bold'),

                TextColumn::make('sisa_kompensasi')
                    ->label('Sisa Saldo')
                    ->money('IDR', locale: 'id')
                    ->weight('bold')
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success'),

                TextColumn::make('status')
                    ->label('Status Klaim')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'diajukan' => 'warning',
                        'disetujui' => 'info',
                        'dikompensasi_sebagian' => 'warning',
                        'sudah_dikompensasi' => 'success',
                        'ditolak' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'diajukan' => 'Diajukan',
                        'disetujui' => 'Disetujui',
                        'dikompensasi_sebagian' => 'Sebagian Terkompensasi',
                        'sudah_dikompensasi' => 'Lunas Dikompensasi',
                        'ditolak' => 'Ditolak',
                        default => ucfirst($state),
                    }),

                ImageColumn::make('bukti_nota')
                    ->label('Nota')
                    ->disk('public')
                    ->circular()
                    ->width(50)
                    ->tooltip(fn ($record) => $record->bukti_nota ? 'Klik untuk memperbesar nota' : null)
                    ->action(
                        Action::make('preview_nota')
                            ->modalHeading(fn ($record) => 'Nota Service: ' . $record->jenis_pengeluaran)
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup')
                            ->modalContent(function ($record) {
                                if (!$record->bukti_nota) {
                                    return new HtmlString('<div class="p-6 text-center text-gray-500">Tidak ada gambar nota yang diunggah.</div>');
                                }
                                $url = asset('storage/' . $record->bukti_nota);
                                return new HtmlString("
                                    <div class='flex flex-col items-center justify-center p-2'>
                                        <div class='overflow-hidden rounded-xl border border-gray-200 bg-gray-50 p-2 shadow-sm dark:border-gray-700 dark:bg-gray-800'>
                                            <img src='{$url}' alt='Nota Service' class='max-h-[70vh] max-w-full rounded-lg object-contain' />
                                        </div>
                                        <a href='{$url}' target='_blank' rel='noopener noreferrer' class='mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-500 hover:underline'>
                                            <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14' />
                                            </svg>
                                            Buka Ukuran Penuh di Tab Baru
                                        </a>
                                    </div>
                                ");
                            })
                    ),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(35)
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'diajukan' => 'Diajukan',
                        'disetujui' => 'Disetujui',
                        'dikompensasi_sebagian' => 'Sebagian Terkompensasi',
                        'sudah_dikompensasi' => 'Lunas Dikompensasi',
                        'ditolak' => 'Ditolak',
                    ]),
            ])
            ->headerActions([
                Action::make('export_pengeluaran')
                    ->label('Ekspor Biaya Service')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->modalHeading('Ekspor Riwayat Biaya Service & Kendala')
                    ->form([
                        Select::make('format')
                            ->label('Pilih Format Dokumen')
                            ->options([
                                'excel' => 'Microsoft Excel (.xlsx)',
                                'pdf' => 'Dokumen PDF (.pdf)',
                            ])
                            ->default('excel')
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) {
                        $angsuran = $livewire->ownerRecord;
                        $angsuranId = $angsuran?->id;
                        $nopol = $angsuran?->unit?->nopol ?? 'unit';
                        $cleanNopol = str_replace(' ', '_', $nopol);
                        $format = $data['format'] ?? 'excel';

                        if ($format === 'pdf') {
                            $records = $angsuran ? $angsuran->pengeluarans()->orderBy('tgl_pengeluaran', 'asc')->get() : collect();
                            $pdf = Pdf::loadView('pdf.angsuran-pengeluaran', ['records' => $records, 'angsuran' => $angsuran])->setPaper('a4', 'landscape');
                            $filename = 'biaya_service_' . $cleanNopol . '_' . now()->format('Ymd_His') . '.pdf';

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, $filename);
                        } else {
                            $filename = 'biaya_service_' . $cleanNopol . '_' . now()->format('Ymd_His') . '.xlsx';
                            return response()->streamDownload(function () use ($angsuranId) {
                                echo Excel::raw(new AngsuranPengeluaranExport($angsuranId), \Maatwebsite\Excel\Excel::XLSX);
                            }, $filename);
                        }
                    }),

                CreateAction::make()
                    ->label('Tambah Nota Service'),
            ])
            ->actions([
                Action::make('pecah_ke_setoran')
                    ->label('Bagi ke Setoran Harian')
                    ->icon('heroicon-o-calendar-days')
                    ->color('success')
                    ->visible(fn (AngsuranPengeluaran $record) => in_array($record->status, ['disetujui', 'diajukan', 'dikompensasi_sebagian']) && $record->sisa_kompensasi > 0)
                    ->modalHeading('Bagi Biaya Service ke Setoran Harian')
                    ->modalDescription(function (AngsuranPengeluaran $record, $livewire) {
                        $angs = (float) ($livewire->ownerRecord?->angsuran_per_hari ?: 0);
                        $sisa = $record->sisa_kompensasi;
                        $estimasiHari = $angs > 0 ? ceil($sisa / $angs) : 1;
                        return "Sisa saldo nota Rp " . number_format($sisa, 0, ',', '.') . " akan dibagi menjadi " . $estimasiHari . " hari setoran berturut-turut (maks Rp " . number_format($angs, 0, ',', '.') . " per hari).";
                    })
                    ->form([
                        DatePicker::make('tgl_mulai')
                            ->label('Tanggal Mulai Setoran')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (AngsuranPengeluaran $record, array $data, $livewire): void {
                        $angsHarian = (float) ($livewire->ownerRecord?->angsuran_per_hari ?: 0);
                        $sisa = $record->sisa_kompensasi;

                        if ($sisa <= 0) {
                            Notification::make()
                                ->title('Biaya nota ini sudah habis dikompensasikan.')
                                ->warning()
                                ->send();
                            return;
                        }

                        if ($angsHarian <= 0) {
                            Notification::make()
                                ->title('Nominal angsuran per hari belum ditentukan pada kontrak ini.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $currentDate = Carbon::parse($data['tgl_mulai']);
                        $count = 0;

                        $adaKurangBayar = false;
                        $sisaKurangBayarHariTerakhir = 0;

                        while ($sisa > 0) {
                            $nominalHariIni = min($angsHarian, $sisa);
                            $kurang = $angsHarian - $nominalHariIni;

                            $ketHari = "Kompensasi {$record->jenis_pengeluaran} (Hari ke-" . ($count + 1) . ", Nota tgl {$record->tgl_pengeluaran->format('d/m/Y')})";
                            if ($kurang > 0) {
                                $adaKurangBayar = true;
                                $sisaKurangBayarHariTerakhir = $kurang;
                                $ketHari .= " • Kurang bayar Rp " . number_format($kurang, 0, ',', '.') . " (disetor via transfer/tunai)";
                            }

                            $record->angsuranKendaraan->setorans()->create([
                                'tgl_setor' => $currentDate->format('Y-m-d'),
                                'periode_bulan' => $currentDate->month,
                                'periode_tahun' => $currentDate->year,
                                'nominal' => $nominalHariIni,
                                'metode_setor' => 'kompensasi_biaya',
                                'angsuran_pengeluaran_id' => $record->id,
                                'keterangan' => $ketHari,
                            ]);

                            $sisa -= $nominalHariIni;
                            $count++;
                            $currentDate->addDay();
                        }

                        $record->sinkronkanStatus();

                        $notifBody = "Telah dibuat {$count} baris setoran kompensasi.";
                        if ($adaKurangBayar) {
                            $notifBody .= " Catatan: Hari terakhir sebesar Rp " . number_format($angsHarian - $sisaKurangBayarHariTerakhir, 0, ',', '.') . " (driver masih perlu setor sisa Rp " . number_format($sisaKurangBayarHariTerakhir, 0, ',', '.') . " via transfer/tunai).";
                        }

                        Notification::make()
                            ->title("Berhasil membagi biaya service!")
                            ->body($notifBody)
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make()
                    ->after(fn ($record) => $record->sinkronkanStatus()),
                DeleteAction::make()
                    ->after(fn ($record) => $record->sinkronkanStatus()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('export_pengeluaran_terpilih')
                        ->label('Ekspor Biaya Terpilih')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->form([
                            Select::make('format')
                                ->label('Pilih Format Dokumen')
                                ->options([
                                    'excel' => 'Microsoft Excel (.xlsx)',
                                    'pdf' => 'Dokumen PDF (.pdf)',
                                ])
                                ->default('excel')
                                ->required(),
                        ])
                        ->action(function (\Illuminate\Support\Collection $records, array $data, $livewire) {
                            $ids = $records->pluck('id')->toArray();
                            $angsuran = $livewire->ownerRecord;
                            $nopol = $angsuran?->unit?->nopol ?? 'unit';
                            $cleanNopol = str_replace(' ', '_', $nopol);
                            $format = $data['format'] ?? 'excel';

                            if ($format === 'pdf') {
                                $pdf = Pdf::loadView('pdf.angsuran-pengeluaran', ['records' => $records, 'angsuran' => $angsuran])->setPaper('a4', 'landscape');
                                $filename = 'biaya_service_terpilih_' . $cleanNopol . '_' . now()->format('Ymd_His') . '.pdf';

                                return response()->streamDownload(function () use ($pdf) {
                                    echo $pdf->output();
                                }, $filename);
                            } else {
                                $filename = 'biaya_service_terpilih_' . $cleanNopol . '_' . now()->format('Ymd_His') . '.xlsx';
                                return response()->streamDownload(function () use ($ids) {
                                    echo Excel::raw(new AngsuranPengeluaranExport(null, $ids), \Maatwebsite\Excel\Excel::XLSX);
                                }, $filename);
                            }
                        }),
                ]),
            ])
            ->defaultSort('tgl_pengeluaran', 'desc');
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
