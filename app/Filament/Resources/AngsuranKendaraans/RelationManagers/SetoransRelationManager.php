<?php

namespace App\Filament\Resources\AngsuranKendaraans\RelationManagers;

use App\Exports\AngsuranSetoranExport;
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
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class SetoransRelationManager extends RelationManager
{
    protected static string $relationship = 'setorans';

    protected static ?string $title = 'Riwayat Setoran Harian';

    protected static ?string $modelLabel = 'Setoran Harian';

    protected static ?string $pluralModelLabel = 'Setoran Harian';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tgl_setor')
                    ->label('Tanggal Setor')
                    ->default(now())
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $parsed = Carbon::parse($state);
                            $set('periode_bulan', $parsed->month);
                            $set('periode_tahun', $parsed->year);
                        }
                    }),

                TextInput::make('nominal')
                    ->label(fn(callable $get) => $get('metode_setor') === 'kombinasi' ? 'Total Setoran Hari Ini' : 'Nominal Setoran')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => $state !== null ? (int) round((float) $state) : null)
                    ->dehydrateStateUsing(fn ($state) => $state !== null ? (float) $state : null)
                    ->prefix('Rp')
                    ->default(fn($livewire) => $livewire->ownerRecord ? (int) round((float) $livewire->ownerRecord->angsuran_per_hari) : null)
                    ->required()
                    ->readOnly(fn(callable $get) => $get('metode_setor') === 'kombinasi')
                    ->helperText(fn(callable $get) => $get('metode_setor') === 'kombinasi' ? 'Otomatis dihitung dari Total (Potong Nota + Sisa Bayar).' : null),

                Select::make('metode_setor')
                    ->label('Metode Pembayaran')
                    ->options(function ($record) {
                        $opts = [
                            'transfer' => 'Transfer Bank',
                            'tunai' => 'Tunai',
                            'kompensasi_biaya' => 'Kompensasi Biaya / Service (Penuh)',
                        ];
                        if (!$record) {
                            $opts['kombinasi'] = 'Kombinasi (Kompensasi Nota + Transfer / Tunai)';
                        }
                        $opts['lainnya'] = 'Lainnya';
                        return $opts;
                    })
                    ->default('transfer')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire) {
                        $angsHarian = (float) ($livewire->ownerRecord?->angsuran_per_hari ?: 0);
                        if ($state === 'kombinasi') {
                            $notaId = $get('angsuran_pengeluaran_id');
                            $pengeluaran = $notaId ? AngsuranPengeluaran::find($notaId) : null;
                            $sisaNota = $pengeluaran ? $pengeluaran->sisa_kompensasi : 0;
                            $nomKomp = ($sisaNota > 0) ? min($sisaNota, $angsHarian) : 0;
                            $nomSisa = max(0, $angsHarian - $nomKomp);

                            $set('nominal_kompensasi', (int) $nomKomp);
                            $set('nominal_sisa', (int) $nomSisa);
                            $set('nominal', (int) ($nomKomp + $nomSisa));
                        } elseif ($state === 'kompensasi_biaya') {
                            $notaId = $get('angsuran_pengeluaran_id');
                            $pengeluaran = $notaId ? AngsuranPengeluaran::find($notaId) : null;
                            if ($pengeluaran) {
                                $sisa = $pengeluaran->sisa_kompensasi;
                                $nom = ($angsHarian > 0 && $sisa > $angsHarian) ? $angsHarian : $sisa;
                                $set('nominal', (int) $nom);
                            }
                        } else {
                            $set('nominal', (int) $angsHarian);
                        }
                    }),

                Select::make('angsuran_pengeluaran_id')
                    ->label('Pilih Nota Service / Pengeluaran')
                    ->visible(fn(callable $get) => in_array($get('metode_setor'), ['kompensasi_biaya', 'kombinasi']))
                    ->required(fn(callable $get) => in_array($get('metode_setor'), ['kompensasi_biaya', 'kombinasi']))
                    ->searchable()
                    ->options(function ($record, $livewire, callable $get) {
                        $currentId = $record?->angsuran_pengeluaran_id ?: $get('angsuran_pengeluaran_id');
                        $owner = $livewire?->ownerRecord ?: $record?->angsuranKendaraan;

                        if (!$owner) {
                            return [];
                        }

                        return $owner->pengeluarans()
                            ->where('status', '!=', 'ditolak')
                            ->get()
                            ->filter(function (AngsuranPengeluaran $p) use ($currentId) {
                                // Selalu tampilkan nota yang saat ini sedang dipilih oleh setoran
                                if ($currentId && (int) $p->id === (int) $currentId) {
                                    return true;
                                }
                                // Untuk opsi baru, hanya yang masih ada sisa saldo
                                return $p->sisa_kompensasi > 0;
                            })
                            ->mapWithKeys(function (AngsuranPengeluaran $p) use ($currentId) {
                                if ($currentId && (int) $p->id === (int) $currentId) {
                                    $label = "{$p->tgl_pengeluaran->format('d/m/Y')} - {$p->jenis_pengeluaran} (Total Nota: Rp " . number_format($p->nominal, 0, ',', '.') . " • Terpilih)";
                                } else {
                                    $label = "{$p->tgl_pengeluaran->format('d/m/Y')} - {$p->jenis_pengeluaran} (Total Nota: Rp " . number_format($p->nominal, 0, ',', '.') . " • Sisa Saldo: Rp " . number_format($p->sisa_kompensasi, 0, ',', '.') . ")";
                                }
                                return [$p->id => $label];
                            })
                            ->toArray();
                    })
                    ->helperText('Pilih nota perbaikan yang dikompensasikan.')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire) {
                        if ($state) {
                            $pengeluaran = AngsuranPengeluaran::find($state);
                            if ($pengeluaran) {
                                $angsHarian = (float) ($livewire->ownerRecord?->angsuran_per_hari ?: 0);
                                $sisa = $pengeluaran->sisa_kompensasi;

                                if ($get('metode_setor') === 'kombinasi') {
                                    $nomKomp = min($sisa, $angsHarian > 0 ? $angsHarian : $sisa);
                                    $nomSisa = max(0, $angsHarian - $nomKomp);

                                    $set('nominal_kompensasi', (int) $nomKomp);
                                    $set('nominal_sisa', (int) $nomSisa);
                                    $set('nominal', (int) ($nomKomp + $nomSisa));
                                    $set('keterangan', "Kombinasi: Kompensasi {$pengeluaran->jenis_pengeluaran} Rp " . number_format($nomKomp, 0, ',', '.') . " + Sisa Bayar Rp " . number_format($nomSisa, 0, ',', '.'));
                                } else {
                                    $nominalHariIni = ($angsHarian > 0 && $sisa > $angsHarian) ? $angsHarian : $sisa;
                                    $set('nominal', (int) $nominalHariIni);
                                    $set('keterangan', "Kompensasi {$pengeluaran->jenis_pengeluaran} (Nota tgl {$pengeluaran->tgl_pengeluaran->format('d/m/Y')})");
                                }
                            }
                        }
                    })
                    ->columnSpanFull(),

                Grid::make(3)
                    ->visible(fn(callable $get) => $get('metode_setor') === 'kombinasi')
                    ->schema([
                        TextInput::make('nominal_kompensasi')
                            ->label('1. Potong Nota (Kompensasi)')
                            ->numeric()
                            ->formatStateUsing(fn ($state) => $state !== null ? (int) round((float) $state) : null)
                            ->prefix('Rp')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire) {
                                $angsHarian = (float) ($livewire->ownerRecord?->angsuran_per_hari ?: 0);
                                $komp = (float) $state;
                                $sisa = max(0, $angsHarian - $komp);
                                $set('nominal_sisa', (int) $sisa);
                                $set('nominal', (int) ($komp + $sisa));
                            })
                            ->helperText('Nominal dipotong dari nota.'),

                        Select::make('metode_sisa')
                            ->label('2. Metode Sisa Bayar')
                            ->options([
                                'transfer' => 'Transfer Bank',
                                'tunai' => 'Tunai',
                            ])
                            ->default('transfer')
                            ->required(fn(callable $get) => $get('metode_setor') === 'kombinasi'),

                        TextInput::make('nominal_sisa')
                            ->label('3. Nominal Sisa Bayar')
                            ->numeric()
                            ->formatStateUsing(fn ($state) => $state !== null ? (int) round((float) $state) : null)
                            ->prefix('Rp')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $komp = (float) ($get('nominal_kompensasi') ?: 0);
                                $sisa = (float) $state;
                                $set('nominal', (int) ($komp + $sisa));
                            })
                            ->helperText('Kekurangan via tunai/transfer.'),
                    ])
                    ->columnSpanFull(),

                Grid::make(2)
                    ->schema([
                        Select::make('periode_bulan')
                            ->label('Periode Bulan')
                            ->options([
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ])
                            ->default(now()->month)
                            ->required(),

                        TextInput::make('periode_tahun')
                            ->label('Periode Tahun')
                            ->numeric()
                            ->default(now()->year)
                            ->required(),
                    ]),

                FileUpload::make('bukti_setor')
                    ->label(fn(callable $get) => $get('metode_setor') === 'kombinasi' ? 'Bukti Transfer Sisa Bayar (Struk / Resi)' : 'Bukti Transfer / Resi')
                    ->image()
                    ->disk('public')
                    ->directory('bukti_setoran')
                    ->resize(50)
                    ->nullable()
                    ->columnSpanFull(),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(2)
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tgl_setor')
            ->description(function ($livewire) {
                $record = $livewire->ownerRecord;
                if (! $record) return null;
                $total = number_format($record->total_setoran_keseluruhan, 0, ',', '.');
                $sisa = number_format($record->sisa_kontrak, 0, ',', '.');
                $target = number_format($record->normal_angsuran, 0, ',', '.');
                $bulanIni = number_format($record->getTotalSetoranBulan(now()->month, now()->year), 0, ',', '.');
                $namaBln = Carbon::now()->locale('id')->translatedFormat('F Y');
                return "Ringkasan Setoran: Total Terkumpul Rp {$total} • Sisa Kontrak Rp {$sisa} • Bulan {$namaBln} Rp {$bulanIni} (Target Bulanan: Rp {$target})";
            })
            ->columns([
                TextColumn::make('tgl_setor')
                    ->label('Tgl Setor')
                    ->date('d-M-Y')
                    ->sortable(),

                TextColumn::make('periode_bulan')
                    ->label('Periode')
                    ->formatStateUsing(function ($record) {
                        return Carbon::create()->month($record->periode_bulan)->locale('id')->translatedFormat('F') . ' ' . $record->periode_tahun;
                    }),

                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('IDR', locale: 'id')
                    ->weight('bold'),

                TextColumn::make('metode_setor')
                    ->label('Metode')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'transfer' => 'info',
                        'tunai' => 'success',
                        'kompensasi_biaya' => 'warning',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'transfer' => 'Transfer',
                        'tunai' => 'Tunai',
                        'kompensasi_biaya' => 'Kompensasi Biaya',
                        default => ucfirst($state),
                    }),

                TextColumn::make('pengeluaran.jenis_pengeluaran')
                    ->label('Kompensasi Dari')
                    ->placeholder('-')
                    ->color('warning'),

                ImageColumn::make('bukti_setor')
                    ->label('Bukti')
                    ->disk('public')
                    
                    ->width(50)
                    ->tooltip(fn($record) => $record->bukti_setor ? 'Klik untuk memperbesar bukti' : null)
                    ->action(
                        Action::make('preview_bukti')
                            ->modalHeading(fn($record) => 'Bukti Setoran: ' . ($record->tgl_setor ? Carbon::parse($record->tgl_setor)->format('d F Y') : ''))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup')
                            ->modalContent(function ($record) {
                                if (!$record->bukti_setor) {
                                    return new HtmlString('<div class="p-6 text-center text-gray-500">Tidak ada gambar bukti setoran yang diunggah.</div>');
                                }
                                $url = asset('storage/' . $record->bukti_setor);
                                return new HtmlString("
                                    <div class='flex flex-col items-center justify-center p-2'>
                                        <div class='overflow-hidden rounded-xl border border-gray-200 bg-gray-50 p-2 shadow-sm dark:border-gray-700 dark:bg-gray-800'>
                                            <img src='{$url}' alt='Bukti Setoran' class='max-h-[70vh] max-w-full rounded-lg object-contain' />
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
                SelectFilter::make('periode_bulan')
                    ->label('Filter Bulan')
                    ->options([
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember',
                    ]),
                SelectFilter::make('metode_setor')
                    ->label('Metode Setoran')
                    ->options([
                        'transfer' => 'Transfer',
                        'tunai' => 'Tunai',
                        'kompensasi_biaya' => 'Kompensasi Biaya',
                        'lainnya' => 'Lainnya',
                    ]),
            ])
            ->headerActions([
                Action::make('export_setoran')
                    ->label('Ekspor Setoran')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->modalHeading('Ekspor Riwayat Setoran Harian')
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
                            $records = $angsuran ? $angsuran->setorans()->orderBy('tgl_setor', 'asc')->get() : collect();
                            $pdf = Pdf::loadView('pdf.angsuran-setoran', ['records' => $records, 'angsuran' => $angsuran])->setPaper('a4', 'landscape');
                            $filename = 'setoran_' . $cleanNopol . '_' . now()->format('Ymd_His') . '.pdf';

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, $filename);
                        } else {
                            $filename = 'setoran_' . $cleanNopol . '_' . now()->format('Ymd_His') . '.xlsx';
                            return response()->streamDownload(function () use ($angsuranId) {
                                echo Excel::raw(new AngsuranSetoranExport($angsuranId), \Maatwebsite\Excel\Excel::XLSX);
                            }, $filename);
                        }
                    }),

                CreateAction::make()
                    ->label('Tambah Setoran Harian')
                    ->using(function (array $data, $livewire) {
                        if (($data['metode_setor'] ?? '') === 'kombinasi') {
                            return DB::transaction(function () use ($data, $livewire) {
                                $angsuran = $livewire->ownerRecord;
                                $tglSetor = $data['tgl_setor'];
                                $periodeBulan = $data['periode_bulan'];
                                $periodeTahun = $data['periode_tahun'];
                                $ket = $data['keterangan'] ?? '';

                                $nomKompensasi = (float) ($data['nominal_kompensasi'] ?? 0);
                                $nomSisa = (float) ($data['nominal_sisa'] ?? 0);
                                $metodeSisa = $data['metode_sisa'] ?? 'transfer';

                                // 1. Catat transaksi kompensasi nota
                                $setoranKompensasi = null;
                                if ($nomKompensasi > 0) {
                                    $setoranKompensasi = $angsuran->setorans()->create([
                                        'tgl_setor' => $tglSetor,
                                        'periode_bulan' => $periodeBulan,
                                        'periode_tahun' => $periodeTahun,
                                        'nominal' => $nomKompensasi,
                                        'metode_setor' => 'kompensasi_biaya',
                                        'angsuran_pengeluaran_id' => $data['angsuran_pengeluaran_id'] ?? null,
                                        'bukti_setor' => null,
                                        'keterangan' => ($ket ? $ket . ' • ' : '') . 'Kompensasi Nota Service (Bagian 1/2 Kombinasi)',
                                    ]);
                                }

                                // 2. Catat transaksi sisa bayar (transfer / tunai)
                                $setoranSisa = null;
                                if ($nomSisa > 0) {
                                    $setoranSisa = $angsuran->setorans()->create([
                                        'tgl_setor' => $tglSetor,
                                        'periode_bulan' => $periodeBulan,
                                        'periode_tahun' => $periodeTahun,
                                        'nominal' => $nomSisa,
                                        'metode_setor' => $metodeSisa,
                                        'angsuran_pengeluaran_id' => null,
                                        'bukti_setor' => $data['bukti_setor'] ?? null,
                                        'keterangan' => ($ket ? $ket . ' • ' : '') . 'Pelunasan Angsuran via ' . ($metodeSisa === 'tunai' ? 'Tunai' : 'Transfer') . ' (Bagian 2/2 Kombinasi)',
                                    ]);
                                }

                                // 3. Sinkronkan status nota pengeluaran
                                if (!empty($data['angsuran_pengeluaran_id'])) {
                                    $pengeluaran = AngsuranPengeluaran::find($data['angsuran_pengeluaran_id']);
                                    $pengeluaran?->sinkronkanStatus();
                                }

                                Notification::make()
                                    ->title('Setoran kombinasi berhasil dicatat!')
                                    ->body("Tercatat 2 baris transaksi: Kompensasi Nota Rp " . number_format($nomKompensasi, 0, ',', '.') . " & " . ucfirst($metodeSisa) . " Rp " . number_format($nomSisa, 0, ',', '.'))
                                    ->success()
                                    ->send();

                                return $setoranSisa ?? $setoranKompensasi;
                            });
                        }

                        // Mode reguler (bukan kombinasi)
                        $record = $livewire->ownerRecord->setorans()->create([
                            'tgl_setor' => $data['tgl_setor'],
                            'periode_bulan' => $data['periode_bulan'],
                            'periode_tahun' => $data['periode_tahun'],
                            'nominal' => (float) $data['nominal'],
                            'metode_setor' => $data['metode_setor'],
                            'angsuran_pengeluaran_id' => $data['angsuran_pengeluaran_id'] ?? null,
                            'bukti_setor' => $data['bukti_setor'] ?? null,
                            'keterangan' => $data['keterangan'] ?? null,
                        ]);

                        if (!empty($record->angsuran_pengeluaran_id)) {
                            $pengeluaran = AngsuranPengeluaran::find($record->angsuran_pengeluaran_id);
                            $pengeluaran?->sinkronkanStatus();
                        }

                        return $record;
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->before(function ($record) {
                        session(['old_pengeluaran_id_' . $record->id => $record->angsuran_pengeluaran_id]);
                    })
                    ->after(function ($record) {
                        $oldId = session()->pull('old_pengeluaran_id_' . $record->id);
                        if ($oldId && (int) $oldId !== (int) $record->angsuran_pengeluaran_id) {
                            $oldPengeluaran = AngsuranPengeluaran::find($oldId);
                            $oldPengeluaran?->sinkronkanStatus();
                        }
                        if ($record->angsuran_pengeluaran_id) {
                            $pengeluaran = AngsuranPengeluaran::find($record->angsuran_pengeluaran_id);
                            $pengeluaran?->sinkronkanStatus();
                        }
                    }),
                DeleteAction::make()
                    ->before(function ($record) {
                        session(['delete_pengeluaran_id_' . $record->id => $record->angsuran_pengeluaran_id]);
                    })
                    ->after(function ($record) {
                        $pengeluaranId = session()->pull('delete_pengeluaran_id_' . $record->id);
                        if ($pengeluaranId) {
                            $pengeluaran = AngsuranPengeluaran::find($pengeluaranId);
                            $pengeluaran?->sinkronkanStatus();
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('export_setoran_terpilih')
                        ->label('Ekspor Setoran Terpilih')
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
                                $pdf = Pdf::loadView('pdf.angsuran-setoran', ['records' => $records, 'angsuran' => $angsuran])->setPaper('a4', 'landscape');
                                $filename = 'setoran_terpilih_' . $cleanNopol . '_' . now()->format('Ymd_His') . '.pdf';

                                return response()->streamDownload(function () use ($pdf) {
                                    echo $pdf->output();
                                }, $filename);
                            } else {
                                $filename = 'setoran_terpilih_' . $cleanNopol . '_' . now()->format('Ymd_His') . '.xlsx';
                                return response()->streamDownload(function () use ($ids) {
                                    echo Excel::raw(new AngsuranSetoranExport(null, $ids), \Maatwebsite\Excel\Excel::XLSX);
                                }, $filename);
                            }
                        }),
                ]),
            ])
            ->defaultSort('tgl_setor', 'desc');
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
