<?php

namespace App\Filament\Resources\AngsuranKendaraans;

use App\Exports\AngsuranKendaraanExport;
use App\Filament\Resources\AngsuranKendaraans\Pages\CreateAngsuranKendaraan;
use App\Filament\Resources\AngsuranKendaraans\Pages\EditAngsuranKendaraan;
use App\Filament\Resources\AngsuranKendaraans\Pages\ListAngsuranKendaraans;
use App\Filament\Resources\AngsuranKendaraans\Pages\ViewAngsuranKendaraan;
use App\Filament\Resources\AngsuranKendaraans\RelationManagers\PengeluaransRelationManager;
use App\Filament\Resources\AngsuranKendaraans\RelationManagers\SetoransRelationManager;
use App\Models\AngsuranKendaraan;
use App\Models\Driver;
use App\Models\Project;
use App\Models\Unit;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class AngsuranKendaraanResource extends Resource
{
    protected static ?string $model = AngsuranKendaraan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string | \UnitEnum | null $navigationGroup = 'Sewa & Angsuran';

    protected static ?string $navigationLabel = 'Angsuran Kendaraan';

    protected static ?string $modelLabel = 'Angsuran Kendaraan';

    protected static ?string $pluralModelLabel = 'Angsuran Kendaraan';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Section::make('Ringkasan Realisasi Proses Angsuran')
                //     ->description('Akumulasi pembayaran riil, status sisa kontrak, dan performa bulan berjalan')
                //     ->icon('heroicon-o-chart-bar')
                //     ->schema([
                //         Grid::make(3)
                //             ->schema([
                //                 TextInput::make('summary_setoran')
                //                     ->label('Total Setoran Terkumpul')
                //                     ->prefix('Rp')
                //                     ->disabled()
                //                     ->dehydrated(false)
                //                     ->afterStateHydrated(fn ($component, $record) => $component->state($record ? number_format($record->total_setoran_keseluruhan, 0, ',', '.') : '-')),

                //                 TextInput::make('summary_persen')
                //                     ->label('Progres Pelunasan Kontrak')
                //                     ->disabled()
                //                     ->dehydrated(false)
                //                     ->afterStateHydrated(function ($component, $record) {
                //                         if (! $record || $record->nilai_kontrak <= 0) return $component->state('0%');
                //                         $persen = round(($record->total_setoran_keseluruhan / $record->nilai_kontrak) * 100, 1);
                //                         return $component->state($persen . '%');
                //                     }),

                //                 TextInput::make('summary_sisa')
                //                     ->label('Sisa Nilai Kontrak')
                //                     ->prefix('Rp')
                //                     ->disabled()
                //                     ->dehydrated(false)
                //                     ->afterStateHydrated(fn ($component, $record) => $component->state($record ? number_format($record->sisa_kontrak, 0, ',', '.') : '-')),

                //                 TextInput::make('summary_bulan_ini')
                //                     ->label('Setoran Bulan Ini (' . Carbon::now()->locale('id')->translatedFormat('F Y') . ')')
                //                     ->prefix('Rp')
                //                     ->disabled()
                //                     ->dehydrated(false)
                //                     ->afterStateHydrated(fn ($component, $record) => $component->state($record ? number_format($record->getTotalSetoranBulan(now()->month, now()->year), 0, ',', '.') : '-')),

                //                 TextInput::make('summary_status_bulan_ini')
                //                     ->label('Status Setoran Bulan Ini')
                //                     ->disabled()
                //                     ->dehydrated(false)
                //                     ->afterStateHydrated(function ($component, $record) {
                //                         if (! $record) return $component->state('-');
                //                         $setor = (float) $record->getTotalSetoranBulan(now()->month, now()->year);
                //                         $target = (float) $record->normal_angsuran;
                //                         $selisih = $setor - $target;
                //                         if ($setor >= $target && $target > 0) {
                //                             return $component->state('Lunas Bulan Ini (+Rp ' . number_format($selisih, 0, ',', '.') . ')');
                //                         } elseif ($setor > 0) {
                //                             return $component->state('Kurang Bayar (Kurang Rp ' . number_format(abs($selisih), 0, ',', '.') . ')');
                //                         } else {
                //                             return $component->state('Belum Ada Setoran (Target: Rp ' . number_format($target, 0, ',', '.') . ')');
                //                         }
                //                     }),

                //                 TextInput::make('summary_service')
                //                     ->label('Klaim Service Kendaraan (Penyewa)')
                //                     ->disabled()
                //                     ->dehydrated(false)
                //                     ->afterStateHydrated(function ($component, $record) {
                //                         if (! $record) return $component->state('-');
                //                         $totalKlaim = (float) $record->pengeluarans()->sum('nominal');
                //                         $sisaKlaim = (float) $record->pengeluarans->sum(fn ($p) => $p->sisa_kompensasi);
                //                         return $component->state('Total: Rp ' . number_format($totalKlaim, 0, ',', '.') . ' (Sisa: Rp ' . number_format($sisaKlaim, 0, ',', '.') . ')');
                //                     }),
                //             ]),
                //     ])
                //     ->visible(fn ($record) => $record !== null),

                Section::make('Identitas Penyewa & Kendaraan')
                    ->description('Data driver penyewa dan unit kendaraan yang disewa')
                    ->schema([
                        Select::make('project_id')
                            ->label('Project')
                            ->options(fn () => Project::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('driver_id', null))
                            ->formatStateUsing(fn ($state, ?AngsuranKendaraan $record) => $state ?? $record?->driver?->project_id)
                            ->dehydrated(false)
                            ->required(),

                        Select::make('driver_id')
                            ->label('Nama Penyewa (Driver)')
                            ->options(function (callable $get, ?AngsuranKendaraan $record) {
                                $projectId = $get('project_id') ?? $record?->driver?->project_id;
                                if (! $projectId) {
                                    return [];
                                }

                                return Driver::query()
                                    ->where('drivers.project_id', $projectId)
                                    ->join('users', 'drivers.user_id', '=', 'users.id')
                                    ->orderBy('users.name')
                                    ->pluck('users.name', 'drivers.id');
                            })
                            ->searchable()
                            ->preload()
                            ->disabled(fn (callable $get) => blank($get('project_id')))
                            ->placeholder(fn (callable $get) => blank($get('project_id')) ? 'Pilih project terlebih dahulu' : 'Pilih Driver')
                            ->required(),

                        Select::make('unit_id')
                            ->label('Unit Kendaraan')
                            ->options(fn () => Unit::query()
                                ->orderBy('nopol')
                                ->get()
                                ->mapWithKeys(fn (Unit $unit) => [
                                    $unit->id => "{$unit->nopol} - {$unit->merk} {$unit->type}"
                                ])
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        DatePicker::make('tgl_serah_terima')
                            ->label('Tgl Serah Terima')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state && $get('jangka_waktu_tahun')) {
                                    $tahun = (int) $get('jangka_waktu_tahun');
                                    $set('tgl_jatuh_tempo_akhir', Carbon::parse($state)->addYears($tahun)->format('Y-m-d'));
                                }
                            }),

                        TextInput::make('jangka_waktu_tahun')
                            ->label('Jangka Waktu (Tahun)')
                            ->numeric()
                            ->default(5)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $tahun = (int) ($state ?: 0);
                                if ($get('tgl_serah_terima') && $tahun > 0) {
                                    $set('tgl_jatuh_tempo_akhir', Carbon::parse($get('tgl_serah_terima'))->addYears($tahun)->format('Y-m-d'));
                                }
                                $normal = (float) ($get('normal_angsuran') ?: 0);
                                if ($normal > 0 && $tahun > 0) {
                                    $set('nilai_kontrak', $normal * 12 * $tahun);
                                }
                            }),

                        DatePicker::make('tgl_jatuh_tempo_akhir')
                            ->label('Masa Akhir Kontrak Sewa')
                            ->required(),

                        Select::make('status')
                            ->label('Status Kontrak')
                            ->options([
                                'aktif' => 'Aktif',
                                'lunas' => 'Lunas',
                                'menunggak' => 'Menunggak',
                                'batal' => 'Dibatalkan',
                            ])
                            ->default('aktif')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Skema Perhitungan Angsuran')
                    ->description('Perhitungan otomatis: Normal Angsuran & Nilai Kontrak')
                    ->schema([
                        TextInput::make('frekuensi_per_bulan')
                            ->label('Frekuensi Per Bulan (Hari)')
                            ->numeric()
                            ->default(28)
                            ->suffix('Hari')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $frek = (int) ($state ?: 0);
                                $angsHarian = (float) ($get('angsuran_per_hari') ?: 0);
                                $normal = $frek * $angsHarian;
                                $set('normal_angsuran', $normal);

                                $tahun = (int) ($get('jangka_waktu_tahun') ?: 5);
                                $set('nilai_kontrak', $normal * 12 * $tahun);
                            }),

                        TextInput::make('angsuran_per_hari')
                            ->label('Angsuran Per Hari')
                            ->numeric()
                            ->formatStateUsing(fn ($state) => $state !== null ? (int) round((float) $state) : null)
                            ->dehydrateStateUsing(fn ($state) => $state !== null ? (float) $state : null)
                            ->prefix('Rp')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $angsHarian = (float) ($state ?: 0);
                                $frek = (int) ($get('frekuensi_per_bulan') ?: 28);
                                $normal = $frek * $angsHarian;
                                $set('normal_angsuran', (int) $normal);

                                $tahun = (int) ($get('jangka_waktu_tahun') ?: 5);
                                $set('nilai_kontrak', (int) ($normal * 12 * $tahun));

                                // Rekomendasi Deposit 3x Angsuran jika deposit masih kosong
                                if (!$get('deposit') && $angsHarian > 0) {
                                    $set('deposit', (int) (3 * $angsHarian));
                                }
                            }),

                        TextInput::make('normal_angsuran')
                            ->label('Normal Angsuran (Per Bulan)')
                            ->numeric()
                            ->formatStateUsing(fn ($state) => $state !== null ? (int) round((float) $state) : null)
                            ->dehydrateStateUsing(fn ($state) => $state !== null ? (float) $state : null)
                            ->prefix('Rp')
                            ->helperText('Otomatis: Frekuensi × Angsuran Per Hari')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $normal = (float) ($state ?: 0);
                                $tahun = (int) ($get('jangka_waktu_tahun') ?: 5);
                                $set('nilai_kontrak', (int) ($normal * 12 * $tahun));
                            }),

                        TextInput::make('nilai_kontrak')
                            ->label('Nilai Kontrak')
                            ->numeric()
                            ->formatStateUsing(fn ($state) => $state !== null ? (int) round((float) $state) : null)
                            ->dehydrateStateUsing(fn ($state) => $state !== null ? (float) $state : null)
                            ->prefix('Rp')
                            ->helperText('Otomatis: Normal Angsuran × 12 Bulan × Jangka Waktu')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Uang Muka (DP) & Deposit (Opsional)')
                    ->description('Field opsional/nullable jika belum ada data')
                    ->schema([
                        TextInput::make('dp')
                            ->label('DP (Uang Muka)')
                            ->numeric()
                            ->formatStateUsing(fn ($state) => $state !== null ? (int) round((float) $state) : null)
                            ->dehydrateStateUsing(fn ($state) => $state !== null ? (float) $state : null)
                            ->prefix('Rp')
                            ->nullable(),

                        DatePicker::make('tgl_dp')
                            ->label('Tanggal Pembayaran DP')
                            ->nullable(),

                        TextInput::make('deposit')
                            ->label('Deposit (3x Angsuran)')
                            ->numeric()
                            ->formatStateUsing(fn ($state) => $state !== null ? (int) round((float) $state) : null)
                            ->dehydrateStateUsing(fn ($state) => $state !== null ? (float) $state : null)
                            ->prefix('Rp')
                            ->helperText('Dapat dikosongkan atau disesuaikan')
                            ->nullable(),

                        DatePicker::make('tgl_deposit')
                            ->label('Tanggal Pembayaran Deposit')
                            ->nullable(),

                        Textarea::make('catatan')
                            ->label('Catatan Tambahan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->nullable(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('driver.user.name')
                    ->label('Nama Penyewa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('unit.nopol')
                    ->label('No. Pol')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('tgl_serah_terima')
                    ->label('Tgl Serah Terima')
                    ->date('d-M-y')
                    ->sortable(),

                TextColumn::make('tgl_jatuh_tempo_akhir')
                    ->label('Akhir Kontrak')
                    ->date('d-M-y')
                    ->sortable(),

                TextColumn::make('jangka_waktu_tahun')
                    ->label('Waktu')
                    ->suffix(' Thn')
                    ->alignCenter(),

                TextColumn::make('dp')
                    ->label('DP')
                    ->money('IDR', locale: 'id')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('frekuensi_per_bulan')
                    ->label('Frek/Bln')
                    ->suffix(' hr')
                    ->alignCenter(),

                TextColumn::make('angsuran_per_hari')
                    ->label('Angs / Hari')
                    ->money('IDR', locale: 'id'),

                TextColumn::make('normal_angsuran')
                    ->label('Normal Angsuran')
                    ->money('IDR', locale: 'id')
                    ->weight('bold'),

                TextColumn::make('nilai_kontrak')
                    ->label('Nilai Kontrak')
                    ->money('IDR', locale: 'id')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deposit')
                    ->label('Deposit')
                    ->money('IDR', locale: 'id')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_setoran_keseluruhan')
                    ->label('Total Setor')
                    ->money('IDR', locale: 'id')
                    ->color('success')
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'info',
                        'lunas' => 'success',
                        'menunggak' => 'danger',
                        'batal' => 'gray',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'lunas' => 'Lunas',
                        'menunggak' => 'Menunggak',
                        'batal' => 'Dibatalkan',
                    ]),
            ])
            ->headerActions([
                Action::make('export_data')
                    ->label('Ekspor Data')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->modalHeading('Ekspor Data Sewa & Angsuran Kendaraan')
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
                    ->action(function (array $data) {
                        $format = $data['format'] ?? 'excel';

                        if ($format === 'pdf') {
                            $records = AngsuranKendaraan::with(['driver.user', 'unit', 'setorans', 'pengeluarans'])->orderBy('created_at', 'desc')->get();
                            $pdf = Pdf::loadView('pdf.angsuran-master', ['records' => $records, 'status' => null])->setPaper('a4', 'landscape');
                            $filename = 'laporan_angsuran_kendaraan_' . now()->format('Ymd_His') . '.pdf';

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, $filename);
                        } else {
                            $filename = 'data_angsuran_kendaraan_' . now()->format('Ymd_His') . '.xlsx';
                            return response()->streamDownload(function () {
                                echo Excel::raw(new AngsuranKendaraanExport(), \Maatwebsite\Excel\Excel::XLSX);
                            }, $filename);
                        }
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('export_row')
                    ->label('Ekspor')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->tooltip('Ekspor data unit ini ke format Excel atau PDF')
                    ->modalHeading(fn ($record) => 'Ekspor Data Kontrak: ' . ($record->unit?->nopol ?? 'Unit'))
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
                    ->action(function (array $data, $record) {
                        $format = $data['format'] ?? 'excel';
                        $nopol = $record->unit?->nopol ? str_replace(' ', '_', $record->unit->nopol) : 'unit';

                        if ($format === 'pdf') {
                            $pdf = Pdf::loadView('pdf.angsuran-master', ['records' => collect([$record]), 'status' => $record->status])->setPaper('a4', 'landscape');
                            $filename = 'kontrak_' . $nopol . '_' . now()->format('Ymd_His') . '.pdf';

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, $filename);
                        } else {
                            $filename = 'kontrak_' . $nopol . '_' . now()->format('Ymd_His') . '.xlsx';
                            return response()->streamDownload(function () use ($record) {
                                echo Excel::raw(new AngsuranKendaraanExport([$record->id]), \Maatwebsite\Excel\Excel::XLSX);
                            }, $filename);
                        }
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('export_terpilih')
                        ->label('Ekspor Baris Terpilih')
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
                        ->action(function (\Illuminate\Support\Collection $records, array $data) {
                            $ids = $records->pluck('id')->toArray();
                            $format = $data['format'] ?? 'excel';

                            if ($format === 'pdf') {
                                $selected = AngsuranKendaraan::whereIn('id', $ids)->with(['driver.user', 'unit', 'setorans', 'pengeluarans'])->get();
                                $pdf = Pdf::loadView('pdf.angsuran-master', ['records' => $selected, 'status' => null])->setPaper('a4', 'landscape');
                                $filename = 'kontrak_terpilih_' . now()->format('Ymd_His') . '.pdf';

                                return response()->streamDownload(function () use ($pdf) {
                                    echo $pdf->output();
                                }, $filename);
                            } else {
                                $filename = 'kontrak_terpilih_' . now()->format('Ymd_His') . '.xlsx';
                                return response()->streamDownload(function () use ($ids) {
                                    echo Excel::raw(new AngsuranKendaraanExport($ids), \Maatwebsite\Excel\Excel::XLSX);
                                }, $filename);
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            SetoransRelationManager::class,
            PengeluaransRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAngsuranKendaraans::route('/'),
            'create' => CreateAngsuranKendaraan::route('/create'),
            'view' => ViewAngsuranKendaraan::route('/{record}'),
            'edit' => EditAngsuranKendaraan::route('/{record}/edit'),
        ];
    }
}
