<?php

namespace App\Filament\Finance\Resources;

use App\Filament\Finance\Resources\PengajuanResource\Pages;
use App\Filament\Finance\Resources\PengajuanResource\RelationManagers;
use App\Models\Pengajuan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Infolists\Components\ViewEntry;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Filters\SelectFilter;

class PengajuanResource extends Resource
{
    protected static ?string $model = Pengajuan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
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
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Nominal Transfer'),
                Tables\Columns\TextColumn::make('complete.nominal_estimasi')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Nominal Estimasi')
                    ->formatStateUsing(fn($state) => $state !== null ? 'Rp ' . number_format($state, 0, ',', '.') : '-'),
                Tables\Columns\TextColumn::make('keterangan')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('keterangan_proses')
                    ->label('Status Proses')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state) => match (true) {
                        str_contains(strtoupper($state), 'CUSTOMER SERVICE') => 'gray',
                        str_contains(strtoupper($state), 'PENGAJUAN FINANCE') => 'primary',
                        str_contains(strtoupper($state), 'INPUT FINANCE') => 'warning',
                        str_contains(strtoupper($state), 'OTORISASI') => 'warning',
                        str_contains(strtoupper($state), 'SELESAI') => 'success',
                        default => 'gray',
                    })
                    ->getStateUsing(function ($record) {
                        return match ($record->keterangan_proses) {
                            'cs' => 'Customer Service',
                            'pengajuan finance' => 'Pengajuan Finance',
                            'finance' => 'Input Finance',
                            'otorisasi' => 'Otorisasi',
                            'done' => 'Selesai',
                            default => 'Tidak Diketahui',
                        };
                    }),
            ])
            ->filters([
                SelectFilter::make('keterangan_proses')
                    ->label('Status Proses')
                    ->options([
                        'cs' => 'Customer Service',
                        'pengajuan finance' => 'Pengajuan Finance',
                        'finance' => 'Input Finance',
                        'otorisasi' => 'Otorisasi',
                        'done' => 'Selesai',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('Proses')
                    ->label('Proses')
                    ->icon('heroicon-o-pencil')
                    ->form([
                        Forms\Components\Fieldset::make('Informasi Finance')
                            ->schema([
                                Hidden::make('finance.user_id')
                                    ->default(\Illuminate\Support\Facades\Auth::user()->id),
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('complete.payment_2')
                                            ->label('Rekening Atas Nama')
                                            ->required()
                                            ->default(fn($record) => $record->complete?->payment_2 ?? ''),
                                        Forms\Components\TextInput::make('complete.bank_2')
                                            ->label('Bank')
                                            ->required()
                                            ->default(fn($record) => $record->complete?->bank_2 ?? ''),
                                        Forms\Components\TextInput::make('complete.norek_2')
                                            ->label('No. Rekening')
                                            ->numeric()
                                            ->required()
                                            ->default(fn($record) => $record->complete?->norek_2 ?? ''),
                                    ]),
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\DatePicker::make('complete.tanggal_tf_finance')
                                            ->label('Tanggal Transfer')
                                            ->required(fn($get) => $get('complete.status_finance') === 'paid') // Kondisi required
                                            ->default(fn($record) => $record->complete?->tanggal_tf_finance),

                                        Forms\Components\TextInput::make('complete.nominal_tf_finance')
                                            ->label('Nominal Transfer')
                                            ->numeric()
                                            ->required(fn($get) => $get('complete.status_finance') === 'paid') // Kondisi required
                                            ->default(fn($record) => $record->complete?->nominal_tf_finance),

                                        Forms\Components\Select::make('complete.status_finance')
                                            ->label('Status')
                                            ->default(fn($record) => $record->complete?->status_finance ?? 'unpaid')
                                            ->options([
                                                'paid' => 'Paid',
                                                'unpaid' => 'Unpaid',
                                            ]),
                                    ]),
                                Forms\Components\FileUpload::make('finance.bukti_transaksi')
                                    ->label('Bukti Transaksi')
                                    ->helperText('Hanya dapat mengunggah file dengan tipe PDF atau gambar (image).')
                                    ->required(fn($get) => $get('complete.status_finance') === 'paid') // Kondisi required
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->disk('public')
                                    ->directory('bukti_transaksi')
                                    ->default(fn($record) => $record->finance?->bukti_transaksi),
                            ])
                            ->columns(1),
                    ])
                    ->action(function (array $data, Pengajuan $record) {
                        $data['finance']['user_id'] = \Illuminate\Support\Facades\Auth::user()->id;
                        $record->complete()->updateOrCreate([], $data['complete']); // Update or create related data
                        $record->finance()->updateOrCreate([], $data['finance']); // Update or create related data
                        $statusFinance = $data['complete']['status_finance'] ?? 'unpaid';
                        $record->update(['keterangan_proses' => $statusFinance === 'paid' ? 'otorisasi' : 'finance']);
                        Notification::make()
                            ->title('Data pengajuan berhasil diproses.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Informasi Umum')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\Group::make([
                                    Components\TextEntry::make('nama'),
                                    Components\TextEntry::make('no_wa'),
                                ]),
                                Components\Group::make([
                                    Components\TextEntry::make('keterangan_proses')
                                        ->label('Status Proses')
                                        ->badge()
                                        ->getStateUsing(function ($record) {
                                            return match ($record->keterangan_proses) {
                                                'cs' => 'Customer Service',
                                                'pengajuan finance' => 'Pengajuan Finance',
                                                'finance' => 'Input Finance',
                                                'otorisasi' => 'Otorisasi',
                                                'done' => 'Selesai',
                                                default => 'Tidak Diketahui',
                                            };
                                        })
                                        ->color(fn(string $state) => match ($state) {
                                            'Customer Service' => 'gray',
                                            'Pengajuan Finance' => 'primary',
                                            'Input Finance' => 'warning',
                                            'Otorisasi' => 'warning',
                                            'Selesai' => 'success',
                                            default => 'gray',
                                        }),
                                    Components\TextEntry::make('created_at')
                                        ->label('Tanggal Pengajuan')
                                        ->dateTime()
                                        ->getStateUsing(fn($record) => $record->created_at->format('d M Y H:i:s')),
                                ]),
                            ]),
                    ]),

                Components\Section::make('Detail Kendaraan')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\Group::make([
                                    Components\TextEntry::make('jenis'),
                                    Components\TextEntry::make('type'),
                                    Components\TextEntry::make('nopol'),
                                    Components\TextEntry::make('odometer'),
                                    Components\TextEntry::make('service'),
                                ]),
                                Components\Group::make([
                                    Components\TextEntry::make('project'),
                                    Components\TextEntry::make('keterangan'),
                                    Components\TextEntry::make('up'),
                                    Components\TextEntry::make('provinsi'),
                                    Components\TextEntry::make('kota'),
                                ]),
                            ]),
                    ]),

                Components\Section::make('Pembayaran')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('payment_1'),
                                Components\TextEntry::make('bank_1'),
                                Components\TextEntry::make('norek_1'),
                            ]),
                    ]),
                Components\Section::make('Dokumentasi')
                    ->schema([
                        ViewEntry::make('foto_unit')
                            ->label('Foto Unit')
                            ->view('filament.components.foto-unit')
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                            ]),

                        ViewEntry::make('foto_odometer')
                            ->label('Foto Odometer')
                            ->view('filament.components.foto-odometer')
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                            ]),

                        ViewEntry::make('foto_kondisi')
                            ->label('Foto Kondisi')
                            ->view('filament.components.foto-kondisi')
                            ->columnSpan([
                                'default' => 4,
                                'md' => 3,
                            ]),
                    ])
                    ->columns([
                        'default' => 4,
                        'md' => 5,
                    ]),
                Components\Section::make('Informasi Proses')
                    ->schema([
                        Components\Grid::make(1)
                            ->schema([
                                Components\Group::make([
                                    Components\TextEntry::make('complete.kode')
                                        ->label('Kode'),
                                ]),
                            ]),
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('complete.bengkel_estimasi')
                                    ->label('Bengkel Estimasi'),
                                Components\TextEntry::make('complete.no_telp_bengkel')
                                    ->label('No. Telp Bengkel'),
                                Components\TextEntry::make('complete.nominal_estimasi')
                                    ->label('Nominal Estimasi'),
                            ])
                            ->label('Informasi Bengkel'),
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('complete.tanggal_masuk_finance')
                                    ->label('Tanggal Masuk Finance')
                                    ->dateTime(),
                                Components\TextEntry::make('complete.tanggal_tf_finance')
                                    ->label('Tanggal Transfer Finance')
                                    ->dateTime(),
                                Components\TextEntry::make('complete.nominal_tf_finance')
                                    ->label('Nominal Transfer Finance'),
                            ]),
                        Components\Grid::make(1)
                            ->schema([
                                Components\Group::make([
                                    Components\TextEntry::make('complete.payment_2')
                                        ->label('Nama Rekening'),
                                ]),
                            ]),
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('complete.bank_2')
                                    ->label('Bank'),
                                Components\TextEntry::make('complete.norek_2')
                                    ->label('No. Rekening'),
                                Components\TextEntry::make('complete.status_finance')
                                    ->label('Status Finance'),
                            ]),
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('complete.nominal_tf_bengkel')
                                    ->label('Nominal Transfer Bengkel'),
                                Components\TextEntry::make('complete.selisih_tf')
                                    ->label('Selisih Transfer'),
                                Components\TextEntry::make('complete.tanggal_tf_bengkel')
                                    ->label('Tanggal Transfer Bengkel')
                                    ->dateTime(),
                                Components\TextEntry::make('complete.tanggal_pengerjaan')
                                    ->label('Tanggal Pengerjaan')
                                    ->dateTime(),
                            ]),
                    ])
                    ->visible(fn($record) => !empty($record->complete)), // Only show when complete data is filled
                Components\Section::make('Dokumentasi Proses')
                    ->schema([
                        ViewEntry::make('complete.foto_nota')
                            ->label('Foto Nota')
                            ->view('filament.components.foto-nota')
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                            ]),
                        ViewEntry::make('complete.foto_pengerjaan_bengkel')
                            ->label('Foto Pengerjaan Bengkel')
                            ->view('filament.components.foto-pengerjaan-bengkel')
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                            ]),
                        ViewEntry::make('complete.foto_tambahan')
                            ->label('Foto Tambahan')
                            ->view('filament.components.foto-tambahan')
                            ->columnSpan([
                                'default' => 4,
                                'md' => 3,
                            ]),
                        ViewEntry::make('finance.bukti_transaksi')
                            ->label('Bukti Transaksi')
                            ->view('filament.components.bukti_transaksi')
                            ->columnSpan([
                                'default' => 4,
                                'md' => 3,
                            ]),
                    ])
                    ->columns([
                        'default' => 4,
                        'md' => 5,
                    ])
                    ->visible(fn($record) => !empty($record->complete)),
            ]);
    }

    // public static function getRecordSubNavigation(Page $page): array
    // {
    //     return $page->generateNavigationItems([
    //         Pages\ViewPengajuan::class,
    //         // Pages\EditPengajuan::class,
    //         // Pages\ProsesPengajuans::class,
    //     ]);
    // }

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
