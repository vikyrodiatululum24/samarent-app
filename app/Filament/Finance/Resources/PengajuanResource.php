<?php

namespace App\Filament\Finance\Resources;

use App\Filament\Finance\Resources\PengajuanResource\Pages;
use App\Models\Pengajuan;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Infolists\Components;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PengajuanResource extends Resource
{
    protected static ?string $model = Pengajuan::class;

    protected static ?string $navigationLabel = 'Pengajuan Finance';
    protected static ?string $label = 'Pengajuan Finance';
    protected static ?string $pluralLabel = 'Pengajuan Finance';
    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
        ->paginated([10, 25, 50, 100])
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('no_pengajuan')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->searchable()
                    ->label('Tanggal Pengajuan')
                    ->date('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('User'),
                Tables\Columns\TextColumn::make('nama')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Nama PIC'),
                Tables\Columns\TextColumn::make('service_unit')
                    ->label('Service - Nopol')
                    ->getStateUsing(function ($record) {
                        // Ambil semua service yang berelasi dengan pengajuan ini
                        $services = $record->service_unit()->with('unit')->get();
                        // Format: [nama_service (nopol)], dipisah baris baru
                        return $services->map(function ($service) {
                            $nopol = $service->unit?->nopol ?? '-';
                            return "{$service->service} - {$nopol}";
                        })->implode('<br>');
                    })
                    ->html()
                    ->searchable(query: function (Builder $query, string $search) {
                        // Join ke tabel service_unit dan unit, lalu filter berdasarkan nama service atau nopol
                        $query->whereHas('service_unit.unit', function ($q) use ($search) {
                            $q->where('service', 'like', "%{$search}%")
                                ->orWhere('nopol', 'like', "%{$search}%");
                        });
                    }),
                Tables\Columns\TextColumn::make('project')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Project'),
                Tables\Columns\TextColumn::make('up')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Unit Pelaksana'),
                Tables\Columns\TextColumn::make('complete.bengkel_estimasi')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Bengkel'),
                Tables\Columns\TextColumn::make('complete.nominal_tf_bengkel')
                    ->sortable()
                    ->searchable()
                    ->money('idr', true)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Nominal Transfer'),
                Tables\Columns\TextColumn::make('complete.nominal_estimasi')
                    ->sortable()
                    ->searchable()
                    ->money('idr', true)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Nominal Estimasi'),
                Tables\Columns\TextColumn::make('keterangan')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('keterangan_proses')
                    ->label('Status Proses')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state) => match (true) {
                        str_contains(strtoupper($state), 'CUSTOMER SERVICE') => 'black',
                        str_contains(strtoupper($state), 'VERIFIKASI') => 'danger',
                        str_contains(strtoupper($state), 'PENGAJUAN ATASAN') => 'info',
                        str_contains(strtoupper($state), 'PENGAJUAN FINANCE') => 'primary',
                        str_contains(strtoupper($state), 'INPUT FINANCE') => 'brown',
                        str_contains(strtoupper($state), 'OTORISASI') => 'yellow',
                        str_contains(strtoupper($state), 'SELESAI') => 'success',
                        default => 'gray',
                    })
                    ->getStateUsing(function ($record) {
                        $prosesPengajuan = $record->keterangan_proses ?? '';

                        $prosesText = match ($prosesPengajuan) {
                            'cs' => 'Customer Service',
                            'checker' => 'Verifikasi',
                            'pengajuan atasan' => 'Pengajuan Atasan',
                            'pengajuan finance' => 'Pengajuan Finance',
                            'finance' => 'Input Finance',
                            'otorisasi' => 'Otorisasi',
                            'done' => 'Selesai',
                            default => 'Tidak Diketahui',
                        };

                        return $prosesText;
                    }),
                Tables\Columns\TextColumn::make('bos_joulmer.is_approved')
                    ->label('Status di Atasan')
                    ->getStateUsing(function ($record) {
                        $statusBos = $record->bos_joulmer?->is_approved;
                        return match ($statusBos) {
                            'pending' => 'Menunggu Approval',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                            default => '-',
                        };
                    })
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'Menunggu Approval' => 'warning',
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('keterangan_proses')
                    ->label('Status Proses')
                    ->options([
                        'cs' => 'Customer Service',
                        'checker' => 'Verifikasi',
                        'pengajuan atasan' => 'Pengajuan Atasan',
                        'pengajuan finance' => 'Pengajuan Finance',
                        'finance' => 'Input Finance',
                        'otorisasi' => 'Otorisasi',
                        'done' => 'Selesai',
                    ]),
                SelectFilter::make('bos_status')
                    ->label('Status di Atasan')
                    ->options([
                        'pending' => 'Menunggu Approval',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value'])) {
                            $query->whereHas('bos_joulmer', function ($q) use ($data) {
                                $q->where('is_approved', $data['value']);
                            });
                        }
                    }),
                SelectFilter::make('keterangan')
                    ->label('Keterangan')
                    ->options([
                        'reimburse' => 'REIMBURSE',
                        'cash advance' => 'CASH ADVANCE',
                        'invoice' => 'INVOICE',
                        'free' => 'FREE',
                    ]),
            ])
            ->actions([
                Action::make('Proses')
                    ->label(fn($record) => 'Proses' . ($record->keterangan_proses === 'finance' ? ' (Otorisasi)' : ''))
                    ->icon('heroicon-o-pencil')
                    ->url(fn(Pengajuan $record): string => static::getUrl('proses', ['record' => $record])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Umum')
                    ->schema([
                        Components\TextEntry::make('nama')
                            ->label('Nama PIC')
                            ->getStateUsing(fn($record) => strtoupper($record->nama)),
                        Components\TextEntry::make('no_wa')
                            ->label('Nomor WhatsApp')
                            ->getStateUsing(fn($record) => strtoupper($record->no_wa)),
                        Components\TextEntry::make('project')
                            ->label('Project')
                            ->getStateUsing(fn($record) => strtoupper($record->project)),
                        Components\TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->getStateUsing(fn($record) => strtoupper($record->keterangan)),
                        Components\TextEntry::make('up')
                            ->label('Unit Pelaksana')
                            ->getStateUsing(function ($record) {
                                if ($record->up === 'manual') {
                                    return $record->up_lainnya ?? 'Lainnya';
                                }
                                return $record->up;
                            })
                            ->badge()
                            ->formatStateUsing(fn($state) => strtoupper($state)),
                        Components\TextEntry::make('provinsi')
                            ->label('Provinsi')
                            ->getStateUsing(fn($record) => strtoupper($record->provinsi)),
                        Components\TextEntry::make('kota')
                            ->label('Kota')
                            ->getStateUsing(fn($record) => strtoupper($record->kota)),
                        Components\TextEntry::make('keterangan_proses')
                            ->label('Status Proses')
                            ->badge()
                            ->getStateUsing(function ($record) {
                                return match ($record->keterangan_proses) {
                                    'cs' => 'Customer Service',
                                    'checker' => 'Verifikasi',
                                    'pengajuan atasan' => 'Pengajuan Atasan',
                                    'pengajuan finance' => 'Pengajuan Finance',
                                    'finance' => 'Input Finance',
                                    'otorisasi' => 'Otorisasi',
                                    'done' => 'Selesai',
                                    default => 'Tidak Diketahui',
                                };
                            })
                            ->color(fn(string $state) => match ($state) {
                                'Customer Service' => 'black',
                                'Verifikasi' => 'danger',
                                'Pengajuan Atasan' => 'info',
                                'Pengajuan Finance' => 'primary',
                                'Input Finance' => 'brown',
                                'Otorisasi' => 'yellow',
                                'Selesai' => 'success',
                                default => 'gray',
                            }),
                        Components\TextEntry::make('created_at')
                            ->label('Tanggal Pengajuan')
                            ->dateTime()
                            ->getStateUsing(fn($record) => $record->created_at->format('d M Y H:i:s')),

                        Components\TextEntry::make('logUpdateStatus')
                            ->label('History Update Status')
                            ->getStateUsing(
                                fn($record) =>
                                $record->logUpdateStatusPengajuans()
                                    ->orderBy('created_at', 'desc')
                                    ->get()
                                    ->map(fn($log) => "{$log->status_baru} - " . $log->created_at->format('d M Y H:i:s'))
                                    ->implode('<br>')
                            )
                            ->html(),
                        Components\TextEntry::make('Status atasan')
                            ->label('Status di Atasan')
                            ->visible(fn($record) => $record->bos_joulmer !== null)
                            ->getStateUsing(function ($record) {
                                $status = match ($record->bos_joulmer->is_approved) {
                                    'pending' => 'Menunggu Approval',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    default => 'Tidak Diketahui',
                                };

                                if ($record->bos_joulmer->approved_at) {
                                    $approvedTime = $record->bos_joulmer->approved_at->format('d M Y H:i:s');
                                    return "{$status} - {$approvedTime}";
                                }

                                return $status;
                            })
                            ->badge()
                            ->color(fn(string $state) => match (true) {
                                str_contains($state, 'Menunggu Approval') => 'info',
                                str_contains($state, 'Disetujui') => 'success',
                                str_contains($state, 'Ditolak') => 'danger',
                                default => 'gray',
                            }),
                        Components\TextEntry::make('bos_joulmer.note')
                            ->label('Catatan Atasan')
                            ->getStateUsing(fn($record) => $record->bos_joulmer?->note ?: '-')
                            ->columnSpanFull()
                            ->visible(fn($record) => in_array($record->bos_joulmer?->is_approved, ['approved', 'rejected'], true)),
                    ])
                    ->columns(2),
                Section::make('Pembayaran')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('payment_1')
                                    ->label('Nama Rekening')
                                    ->getStateUsing(fn($record) => strtoupper($record->payment_1)),
                                Components\TextEntry::make('bank_1')
                                    ->label('Nama Bank')
                                    ->getStateUsing(fn($record) => strtoupper($record->bank_1)),
                                Components\TextEntry::make('norek_1')
                                    ->label('Nomor Rekening')
                                    ->getStateUsing(fn($record) => strtoupper($record->norek_1)),
                            ]),
                    ]),
                Section::make('Detail Kendaraan')
                    ->schema([
                        ViewEntry::make('service_unit.pengajuan_id')
                            ->label('Detail Kendaraan')
                            ->view('filament.resources.pages.pengajuan.detail-kendaraan')
                            ->columnSpanFull(),
                    ]),
                Section::make('Informasi Complete')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Group::make([
                                    Components\TextEntry::make('complete.kode')
                                        ->label('Kode')
                                        ->visible(fn($record) => !empty($record->complete?->kode))
                                        ->badge()
                                        ->formatStateUsing(fn($state) => strtoupper($state)),
                                ]),
                                Components\TextEntry::make('complete.bengkel_invoice')
                                    ->label('Bengkel Invoice')
                                    ->visible(fn($record) => !empty($record->complete?->bengkel_invoice))
                                    ->columnSpan(2),

                            ]),
                        Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('complete.bengkel_estimasi')
                                    ->label('Bengkel Estimasi'),
                                Components\TextEntry::make('complete.no_telp_bengkel')
                                    ->label('No. Telp Bengkel'),
                                Components\TextEntry::make('complete.nominal_estimasi')
                                    ->label('Nominal Estimasi')
                                    ->formatStateUsing(fn($state) => $state !== null ? 'Rp ' . number_format($state, 0, ',', '.') : '-'),
                            ]),
                        Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('complete.tanggal_masuk_finance')
                                    ->label('Tanggal Masuk Finance')
                                    ->date(),
                                Components\TextEntry::make('complete.tanggal_tf_finance')
                                    ->label('Tanggal Transfer Finance')
                                    ->date(),
                                Components\TextEntry::make('complete.nominal_tf_finance')
                                    ->label('Nominal Transfer Finance')
                                    ->formatStateUsing(fn($state) => $state !== null ? 'Rp ' . number_format($state, 0, ',', '.') : '-'),
                            ]),
                        Components\TextEntry::make('complete.tanggal_input_bank')
                            ->label('Tanggal Input Bank')
                            ->date(),
                        Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('complete.bank_2')
                                    ->label('Bank'),
                                Components\TextEntry::make('complete.payment_2')
                                    ->label('Nama Rekening'),
                                Components\TextEntry::make('complete.norek_2')
                                    ->label('No. Rekening'),
                            ]),
                        Grid::make(1)
                            ->schema([
                                Components\TextEntry::make('complete.status_finance')
                                    ->label('Status Finance')
                                    ->getStateUsing(function ($record) {
                                        return match ($record->complete?->status_finance) {
                                            'paid' => 'PAID',
                                            'unpaid' => 'UNPAID',
                                            default => 'Tidak Diketahui',
                                        };
                                    })
                                    ->color(fn(string $state) => match ($state) {
                                        'PAID' => 'success',
                                        'UNPAID' => 'danger',
                                        default => 'gray',
                                    }),
                            ]),
                    ])
                    ->visible(fn($record) => !empty($record->complete)), // Only show when complete data is filled
                Section::make('Informasi Bengkel')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('complete.bank_bengkel')
                                    ->label('Bank Bengkel')
                                    ->getStateUsing(fn($record) => strtoupper($record->complete?->bank_bengkel)),
                                Components\TextEntry::make('complete.nama_rek_bengkel')
                                    ->label('Nama Rekening Bengkel')
                                    ->getStateUsing(fn($record) => strtoupper($record->complete?->nama_rek_bengkel)),
                                Components\TextEntry::make('complete.rek_bengkel')
                                    ->label('No. Rekening Bengkel'),
                                Components\TextEntry::make('complete.nominal_tf_bengkel')
                                    ->label('Nominal Transfer Bengkel')
                                    ->formatStateUsing(fn($state) => $state !== null ? 'Rp ' . number_format($state, 0, ',', '.') : '-'),
                                Components\TextEntry::make('complete.selisih_tf')
                                    ->label('Selisih Transfer')
                                    ->formatStateUsing(fn($state) => $state !== null ? 'Rp ' . number_format($state, 0, ',', '.') : '-'),
                                Components\TextEntry::make('complete.tanggal_tf_bengkel')
                                    ->label('Tanggal Transfer Bengkel')
                                    ->date(),
                                Components\TextEntry::make('complete.tanggal_pengerjaan')
                                    ->label('Tanggal Pengerjaan')
                                    ->date(),
                            ]),
                    ])
                    ->visible(fn($record) => !empty($record->complete)),
                Section::make('Dokumentasi Complete')
                    ->schema([
                        ViewEntry::make('complete.foto_nota')
                            ->label('Foto Nota')
                            ->view('filament.components.foto-nota')
                            ->columnSpan([
                                'default' => 4,
                                'md' => 3,
                            ]),
                        ViewEntry::make('finance.bukti_transaksi')
                            ->label('Bukti Transaksi')
                            ->view('filament.components.bukti_transaksi'),
                    ])
                    ->columns([
                        'default' => 4,
                        'md' => 3,
                    ])
                    ->visible(fn($record) => !empty($record->complete)),
            ])
            ->columns(1);
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
            'index' => Pages\ListPengajuans::route('/'),
            // 'create' => Pages\CreatePengajuan::route('/create'),
            'view' => Pages\ViewPengajuan::route('/{record}'),
            // 'edit' => Pages\EditPengajuan::route('/{record}/edit'),
            'proses' => Pages\ProsesPengajuan::route('/{record}/proses'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Pengajuan Finance'; // singular
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengajuan Finance'; // tetap singular
    }

    public static function getNavigationBadge(): ?string
    {
        $modelClass = static::$model;

        // Ensure $modelClass is a valid class string
        if (!is_string($modelClass) || !class_exists($modelClass)) {
            return '0'; // Return '0' if the model is not set or invalid
        }

        return (string) $modelClass::where('keterangan_proses', 'pengajuan finance')->count();
    }


    public static function canCreate(): bool
    {
        return false; // Menghilangkan tombol create (newPengajuan)
    }
}
