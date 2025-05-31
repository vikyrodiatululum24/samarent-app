<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Pengajuan;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Infolists\Components;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\ViewEntry;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Resources\PengajuanResource\Pages;
use App\Filament\Resources\PengajuanResource\RelationManagers\ServiceUnitRelationManager;
use App\Filament\Exports\PengajuanExporter; // Ensure the PengajuanExporter class is imported

class PengajuanResource extends Resource
{
    protected static ?string $model = Pengajuan::class;

    protected static ?string $slug = 'pengajuan';

    protected static ?string $recordTitleAttribute = 'no_pengajuan';

    protected static ?int $navigationSort = 0;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Informasi Umum')
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Hidden::make('user_id')
                                    ->default(\Illuminate\Support\Facades\Auth::user()->id),
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama PIC')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('no_wa')
                                    ->label('No. WhatsApp')
                                    ->required()
                                    ->numeric()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('project')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Select::make('up')
                                ->required()
                                ->label('Unit Pelaksana')
                                ->options([
                                    'UP 1' => 'UP 1',
                                    'UP 2' => 'UP 2',
                                    'UP 3' => 'UP 3',
                                    'UP 5' => 'UP 5',
                                    'UP 7' => 'UP 7',
                                    'CUST JEPANG' => 'CUST JEPANG',
                                    'manual' => 'Lainnya',
                                ])
                                ->reactive()
                                ->afterStateUpdated(fn(callable $set, $state) => $set('up_lainnya', $state === 'manual' ? '' : null)),
                            Forms\Components\TextInput::make('up_lainnya')
                                ->label('Unit Pelaksana Lainnya')
                                ->required(fn(callable $get) => $get('up') === 'manual')
                                ->visible(fn(callable $get) => $get('up') === 'manual'),
                            Forms\Components\TextInput::make('provinsi')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('kota')
                                ->required()
                                ->maxLength(255),
                        ])
                    ])
                    ->columns(2)
                    ->columnSpan('full')
                    ->extraAttributes(['class' => 'mb-4']),
                Forms\Components\Fieldset::make('Pembayaran')
                    ->schema([
                        Forms\Components\Select::make('keterangan')
                            ->required()
                            ->options([
                                'Reimburse' => 'Reimburse',
                                'cash advance' => 'Cash Advance',
                                'invoice' => 'Invoice',
                                'free' => 'Free',
                            ])
                            ->reactive(),
                        Forms\Components\TextInput::make('payment_1')
                            ->nullable()
                            ->label('Nama Rekening')
                            ->maxLength(255),
                        Forms\Components\Select::make('bank_1')
                            ->nullable()
                            ->label('Bank')
                            ->options([
                                'bca' => 'BCA',
                                'mandiri' => 'MANDIRI',
                                'bri' => 'BRI',
                                'bni' => 'BNI',
                                'permata' => 'PERMATA',
                            ]),
                        Forms\Components\TextInput::make('norek_1')
                            ->nullable()
                            ->label('No. Rekening')
                            ->numeric()
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->columnSpan('full')
                    ->extraAttributes(['class' => 'mb-4']),
            ]);
    }

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
                Tables\Columns\TextColumn::make('nopol')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('No. Polisi'),
                Tables\Columns\TextColumn::make('jenis')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Jenis Kendaraan'),
                Tables\Columns\TextColumn::make('type')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Type Unit'),
                Tables\Columns\TextColumn::make('service')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Permintaan Service'),
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
                        str_contains($state, 'Customer Service') => 'gray',
                        str_contains($state, 'Pengajuan Finance') => 'primary',
                        str_contains($state, 'Input Finance') => 'warning',
                        str_contains($state, 'Otorisasi') => 'warning',
                        str_contains($state, 'Selesai') => 'success',
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
                Tables\Actions\Action::make('Edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->form([
                        Forms\Components\Fieldset::make('Informasi Bengkel')
                            ->schema([
                                Hidden::make('user_id')
                                    ->default(\Illuminate\Support\Facades\Auth::user()->id),
                                Forms\Components\TextInput::make('bengkel_estimasi')
                                    ->label('Nama Bengkel Estimasi')
                                    ->required()
                                    ->default(fn($record) => $record->complete?->bengkel_estimasi),
                                Forms\Components\TextInput::make('no_telp_bengkel')
                                    ->label('No. Telp Bengkel')
                                    ->required()
                                    ->default(fn($record) => $record->complete?->no_telp_bengkel),
                                Forms\Components\TextInput::make('nominal_estimasi')
                                    ->label('Nominal Estimasi')
                                    ->numeric()
                                    ->required()
                                    ->default(fn($record) => $record->complete?->nominal_estimasi),
                            ])
                            ->columns(2),
                        Forms\Components\Fieldset::make('Informasi Pengajuan')
                            ->schema([
                                Forms\Components\Select::make('kode')
                                    ->label('Kode')
                                    ->options([
                                        'op' => 'OP',
                                        'sc' => 'SC',
                                        'sp' => 'SP',
                                        'stnk' => 'STNK',
                                    ])
                                    ->required()
                                    ->default(fn($record) => $record->complete?->kode),
                                Forms\Components\DatePicker::make('tanggal_masuk_finance')
                                    ->label('Tanggal Masuk Finance')
                                    ->required()
                                    ->default(fn($record) => $record->complete?->tanggal_masuk_finance),
                            ])
                            ->columns(1),
                        Forms\Components\Fieldset::make('Informasi Finance')
                            ->schema([
                                Forms\Components\DatePicker::make('tanggal_tf_finance')
                                    ->label('Tanggal Transfer Finance')
                                    // ->required()
                                    ->readOnly()
                                    ->default(fn($record) => $record->complete?->tanggal_tf_finance),
                                Forms\Components\TextInput::make('nominal_tf_finance')
                                    ->label('Nominal Transfer Finance')
                                    ->numeric()
                                    // ->required()
                                    ->readOnly()
                                    ->default(fn($record) => $record->complete?->nominal_tf_finance),
                                Forms\Components\TextInput::make('payment_2')
                                    ->label('Rekening Atas Nama')
                                    // ->required()
                                    ->readOnly()
                                    ->default(fn($record) => $record->complete?->payment_2),
                                Forms\Components\TextInput::make('bank_2')
                                    ->label('Bank')
                                    // ->required()
                                    ->readOnly()
                                    ->default(fn($record) => $record->complete?->bank_2),
                                Forms\Components\TextInput::make('norek_2')
                                    ->label('No. Rekening')
                                    // ->required()
                                    ->readOnly()
                                    ->default(fn($record) => $record->complete?->norek_2),
                                Forms\Components\TextInput::make('status_finance')
                                    ->label('Status Finance')
                                    ->required()
                                    ->readOnly()
                                    ->default(fn($record) => $record->complete?->status_finance ?? 'unpaid'),
                            ])
                            ->columns(2),
                        Forms\Components\Fieldset::make('Transfer Bengkel')
                            ->schema([
                                Forms\Components\TextInput::make('nominal_tf_bengkel')
                                    ->label('Nominal Transfer Bengkel')
                                    ->numeric()
                                    ->reactive()
                                    ->required(fn($record) => $record->complete?->status_finance === 'paid')
                                    ->nullable()
                                    ->default(fn($record) => $record->complete?->nominal_tf_bengkel),
                                Forms\Components\TextInput::make('selisih_tf')
                                    ->label('Selisih Transfer')
                                    ->numeric()
                                    ->required(fn($record) => $record->complete?->status_finance === 'paid')
                                    ->default(fn($record) => $record->complete?->selisih_tf)
                                    ->reactive()
                                    ->afterStateUpdated(fn(callable $set, $state, callable $get) => $set(
                                        'selisih_tf',
                                        // Ambil nilai nominal_tf_finance dari database berdasarkan record
                                        $get('nominal_tf_finance') - $get('nominal_tf_bengkel')
                                    )),
                                Forms\Components\DatePicker::make('tanggal_tf_bengkel')
                                    ->label('Tanggal Transfer Bengkel')
                                    ->nullable()
                                    ->required(fn(callable $get) => !empty($get('nominal_tf_bengkel')))
                                    ->default(fn($record) => $record->complete?->tanggal_tf_bengkel),
                                Forms\Components\DatePicker::make('tanggal_pengerjaan')
                                    ->label('Tanggal Pengerjaan')
                                    ->nullable()
                                    ->required(fn(callable $get) => !empty($get('nominal_tf_bengkel')))
                                    ->default(fn($record) => $record->complete?->tanggal_pengerjaan),
                            ])
                            ->columns(2),
                        Forms\Components\Fieldset::make('Dokumentasi')
                            ->schema([
                                Forms\Components\FileUpload::make('foto_nota')
                                    ->label('Foto Nota')
                                    ->disk('public')
                                    ->directory('foto_nota')
                                    ->nullable()
                                    ->default(fn($record) => $record->complete?->foto_nota),
                                Forms\Components\FileUpload::make('foto_pengerjaan_bengkel')
                                    ->label('Foto Pengerjaan Bengkel')
                                    ->disk('public')
                                    ->directory('foto_pengerjaan_bengkel')
                                    ->nullable()
                                    ->default(fn($record) => $record->complete?->foto_pengerjaan_bengkel),
                                Forms\Components\FileUpload::make('foto_tambahan')
                                    ->label('Foto Tambahan')
                                    ->disk('public')
                                    ->directory('foto_tambahan')
                                    ->multiple()
                                    ->maxFiles(3)
                                    ->nullable()
                                    ->default(fn($record) => $record->complete?->foto_tambahan)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire) {
                                        if (count($state) > 3) {
                                            $set('foto_tambahan', array_slice($state, 0, 3));
                                        }
                                        $record = $livewire->record ?? null;
                                        if ($record && is_array($record->complete->foto_tambahan)) {
                                            $lama = collect($record->complete->foto_tambahan);
                                            $baru = collect($state);
                                            $yangDihapus = $lama->diff($baru);
                                            foreach ($yangDihapus as $path) {
                                                Storage::disk('public')->delete($path);
                                            }
                                        }
                                    }),
                            ])
                            ->columns(2),
                    ])
                    ->action(function (array $data, Pengajuan $record) {
                        $record->complete()->updateOrCreate([], $data);
                        Notification::make()
                            ->title('Data pengajuan berhasil di edit.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('Proses')
                    ->label('Proses')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->form([
                        Forms\Components\Fieldset::make('Informasi Bengkel')
                            ->schema([
                                Hidden::make('user_id')
                                    ->default(\Illuminate\Support\Facades\Auth::user()->id),
                                Forms\Components\TextInput::make('bengkel_estimasi')
                                    ->label('Nama Bengkel Estimasi')
                                    ->required()
                                    ->default(fn($record) => $record->complete?->bengkel_estimasi),
                                Forms\Components\TextInput::make('no_telp_bengkel')
                                    ->label('No. Telp Bengkel')
                                    ->required()
                                    ->default(fn($record) => $record->complete?->no_telp_bengkel),
                                Forms\Components\TextInput::make('nominal_estimasi')
                                    ->label('Nominal Estimasi')
                                    ->numeric()
                                    ->required()
                                    ->default(fn($record) => $record->complete?->nominal_estimasi),
                            ])
                            ->columns(2),
                        Forms\Components\Fieldset::make('Informasi Pengajuan')
                            ->schema([
                                Forms\Components\Select::make('kode')
                                    ->label('Kode')
                                    ->options([
                                        'op' => 'OP',
                                        'sc' => 'SC',
                                        'sp' => 'SP',
                                        'stnk' => 'STNK',
                                    ])
                                    ->required()
                                    ->default(fn($record) => $record->complete?->kode),
                                Forms\Components\DatePicker::make('tanggal_masuk_finance')
                                    ->label('Tanggal Masuk Finance')
                                    ->required()
                                    ->default(fn($record) => $record->complete?->tanggal_masuk_finance),
                            ])
                            ->columns(1),
                        Forms\Components\Fieldset::make('Informasi Finance')
                            ->schema([
                                Forms\Components\DatePicker::make('tanggal_tf_finance')
                                    ->label('Tanggal Transfer Finance')
                                    // ->required()
                                    ->readOnly()
                                    ->default(fn($record) => $record->complete?->tanggal_tf_finance),
                                Forms\Components\TextInput::make('nominal_tf_finance')
                                    ->label('Nominal Transfer Finance')
                                    ->numeric()
                                    // ->required()
                                    ->readOnly()
                                    ->default(fn($record) => $record->complete?->nominal_tf_finance),
                                Forms\Components\TextInput::make('payment_2')
                                    ->label('Rekening Atas Nama')
                                    // ->required()
                                    ->readOnly()
                                    ->default(fn($record) => $record->complete?->payment_2),
                                Forms\Components\TextInput::make('bank_2')
                                    ->label('Bank')
                                    // ->required()
                                    ->readOnly()
                                    ->default(fn($record) => $record->complete?->bank_2),
                                Forms\Components\TextInput::make('norek_2')
                                    ->label('No. Rekening')
                                    // ->required()
                                    ->readOnly()
                                    ->default(fn($record) => $record->complete?->norek_2),
                                Forms\Components\TextInput::make('status_finance')
                                    ->label('Status Finance')
                                    ->required()
                                    ->readOnly()
                                    ->default(fn($record) => $record->complete?->status_finance ?? 'unpaid'),
                            ])
                            ->columns(2),
                        Forms\Components\Fieldset::make('Transfer Bengkel')
                            ->schema([
                                Forms\Components\TextInput::make('nominal_tf_bengkel')
                                    ->label('Nominal Transfer Bengkel')
                                    ->numeric()
                                    ->reactive()
                                    ->required(fn($record) => $record->complete?->status_finance === 'paid')
                                    ->nullable()
                                    ->default(fn($record) => $record->complete?->nominal_tf_bengkel),
                                Forms\Components\TextInput::make('selisih_tf')
                                    ->label('Selisih Transfer')
                                    ->numeric()
                                    ->required(fn($record) => $record->complete?->status_finance === 'paid')
                                    ->default(fn($record) => $record->complete?->selisih_tf)
                                    ->reactive()
                                    ->afterStateUpdated(fn(callable $set, $state, callable $get) => $set(
                                        'selisih_tf',
                                        // Ambil nilai nominal_tf_finance dari database berdasarkan record
                                        $get('nominal_tf_finance') - $get('nominal_tf_bengkel')
                                    )),
                                Forms\Components\DatePicker::make('tanggal_tf_bengkel')
                                    ->label('Tanggal Transfer Bengkel')
                                    ->nullable()
                                    ->required(fn(callable $get) => !empty($get('nominal_tf_bengkel')))
                                    ->default(fn($record) => $record->complete?->tanggal_tf_bengkel),
                                Forms\Components\DatePicker::make('tanggal_pengerjaan')
                                    ->label('Tanggal Pengerjaan')
                                    ->nullable()
                                    ->required(fn(callable $get) => !empty($get('nominal_tf_bengkel')))
                                    ->default(fn($record) => $record->complete?->tanggal_pengerjaan),
                            ])
                            ->columns(2),
                        Forms\Components\Fieldset::make('Dokumentasi')
                            ->schema([
                                Forms\Components\FileUpload::make('foto_nota')
                                    ->label('Foto Nota')
                                    ->disk('public')
                                    ->directory('foto_nota')
                                    ->nullable()
                                    ->default(fn($record) => $record->complete?->foto_nota),
                                Forms\Components\FileUpload::make('foto_pengerjaan_bengkel')
                                    ->label('Foto Pengerjaan Bengkel')
                                    ->disk('public')
                                    ->directory('foto_pengerjaan_bengkel')
                                    ->nullable()
                                    ->default(fn($record) => $record->complete?->foto_pengerjaan_bengkel),
                                Forms\Components\FileUpload::make('foto_tambahan')
                                    ->label('Foto Tambahan')
                                    ->disk('public')
                                    ->directory('foto_tambahan')
                                    ->multiple()
                                    ->maxFiles(3)
                                    ->nullable()
                                    ->default(fn($record) => $record->complete?->foto_tambahan)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire) {
                                        if (count($state) > 3) {
                                            $set('foto_tambahan', array_slice($state, 0, 3));
                                        }
                                        $record = $livewire->record ?? null;
                                        if ($record && is_array($record->complete->foto_tambahan)) {
                                            $lama = collect($record->complete->foto_tambahan);
                                            $baru = collect($state);
                                            $yangDihapus = $lama->diff($baru);
                                            foreach ($yangDihapus as $path) {
                                                Storage::disk('public')->delete($path);
                                            }
                                        }
                                    }),
                            ])
                            ->columns(2),
                    ])
                    ->action(function (array $data, Pengajuan $record) {
                        $record->complete()->updateOrCreate([], $data);
                        $nominalTfBengkel = $data['nominal_tf_bengkel'] ?? null;
                        $record->update([
                            'keterangan_proses' => !empty($nominalTfBengkel) ? 'done' : 'pengajuan finance'
                        ]);
                        Notification::make()
                            ->title('Data pengajuan berhasil diproses.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exporter(PengajuanExporter::class)
                ]),
            ])
            ->headerActions([
                ExportAction::make()->exporter(PengajuanExporter::class),
            ])
            ->defaultSort('id', 'desc'); // Optional: Add default sorting
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
                                                'finance' => 'Finance',
                                                'otorisasi' => 'Otorisasi',
                                                'done' => 'Selesai',
                                                default => 'Tidak Diketahui',
                                            };
                                        })
                                        ->color(fn(string $state) => match ($state) {
                                            'Customer Service' => 'gray',
                                            'Pengajuan Finance' => 'primary',
                                            'Finance' => 'warning',
                                            'Otorisasi' => 'warning',
                                            'Selesai' => 'success',
                                            default => 'gray',
                                        }),
                                    Components\TextEntry::make('created_at')
                                        ->label('Tanggal Pengajuan')
                                        ->dateTime()
                                        ->getStateUsing(fn($record) => $record->created_at->format('d M Y H:i:s')),
                                ]),
                                Components\Group::make([
                                    Components\TextEntry::make('project'),
                                    Components\TextEntry::make('keterangan'),
                                    Components\TextEntry::make('up'),
                                    Components\TextEntry::make('provinsi'),
                                    Components\TextEntry::make('kota'),
                                ]),
                            ])
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
                Components\Section::make('Detail Kendaraan')
                    ->schema([
                        ViewEntry::make('service_unit.pengajuan_id')
                            ->label('Detail Kendaraan')
                            ->view('filament.resources.pages.pengajuan.detail-kendaraan')
                            ->columnSpanFull(),
                    ]),
                Components\Section::make('Informasi Complete')
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
                Components\Section::make('Dokumentasi Complete')
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

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewPengajuan::class,
            Pages\EditPengajuan::class,
            // Pages\ProsesPengajuans::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            ServiceUnitRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengajuans::route('/'),
            'create' => Pages\CreatePengajuan::route('/create'),
            'edit' => Pages\EditPengajuan::route('/{record}/edit'),
            'view' => Pages\ViewPengajuan::route('/{record}'),
        ];
    }

    public static function getGlobalSearchResultUrl(\Illuminate\Database\Eloquent\Model $record): string
    {
        return PengajuanResource::getUrl('view', ['record' => $record]);
    }

    public static function getModelLabel(): string
    {
        return 'Pengajuan'; // singular
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengajuan'; // tetap singular
    }


    public static function getNavigationBadge(): ?string
    {
        $modelClass = static::$model;

        // Ensure $modelClass is a valid class string
        if (!is_string($modelClass) || !class_exists($modelClass)) {
            return '0'; // Return '0' if the model is not set or invalid
        }

        return (string) $modelClass::where('keterangan_proses', 'cs')->count();
    }

    public static function getCreateLabel(): string
    {
        return 'Tambah Pengajuan Baru';
    }
}
