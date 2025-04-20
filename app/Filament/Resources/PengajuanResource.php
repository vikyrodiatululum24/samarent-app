<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengajuanResource\Pages;
use App\Filament\Resources\PengajuanResource\RelationManagers;
use App\Models\Pengajuan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Infolists\Components\ViewEntry;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;

class PengajuanResource extends Resource
{
    protected static ?string $model = Pengajuan::class;

    protected static ?string $slug = 'pengajuan';

    protected static ?string $recordTitleAttribute = 'no_pengajuan';

    protected static ?string $navigationLabel = 'Pengajuan';

    protected static ?int $navigationSort = 0;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                                Forms\Components\TextInput::make('no_pengajuan')
                                    ->label('No. Pengajuan')
                                    ->required() // Ensure the field is required
                                    ->default(fn() => 'SPK/' . str_pad(\App\Models\Pengajuan::max('id') + 1, 4, '0', STR_PAD_LEFT) . '/' . now()->format('m') . '/' . now()->format('Y'))
                                    ->readOnly()
                                    ->unique(ignoreRecord: true), // Ensure the field value is unique, but ignore the current record when editing
                                Forms\Components\TextInput::make('nama')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('no_wa')
                                    ->required()
                                    ->numeric()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\TextInput::make('jenis')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('type')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('nopol')
                                    ->required()
                                    ->maxLength(255),
                            ])
                    ])
                    ->columns(2)
                    ->columnSpan('full')
                    ->extraAttributes(['class' => 'mb-4']),

                Forms\Components\Fieldset::make('Detail Kendaraan')
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\TextInput::make('odometer')
                                    ->required()
                                    ->numeric()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('service')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('project')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Group::make([

                            Forms\Components\Select::make('up')
                                ->required()
                                ->options([
                                    'UP 1' => 'UP 1',
                                    'UP 2' => 'UP 2',
                                    'UP 3' => 'UP 3',
                                    'UP 5' => 'UP 5',
                                    'UP 7' => 'UP 7',
                                    'manual' => 'Lainnya',
                                ])
                                ->reactive()
                                ->afterStateUpdated(fn(callable $set, $state) => $set('up_lainnya', $state === 'manual' ? '' : null)),
                            Forms\Components\TextInput::make('up_lainnya')
                                ->label('UP Lainnya')
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
                            ->maxLength(255)
                            ->disabled(fn(callable $get) => $get('keterangan') !== 'Reimburse' && \Illuminate\Support\Facades\Auth::user()->role === 'user'),
                        Forms\Components\Select::make('bank_1')
                            ->nullable()
                            ->options([
                                'bca' => 'BCA',
                                'mandiri' => 'Mandiri',
                                'bri' => 'BRI',
                                'bni' => 'BNI',
                                'permata' => 'Permata',
                            ])
                            ->label('Bank'),
                        Forms\Components\TextInput::make('norek_1')
                            ->nullable()
                            ->numeric()
                            ->maxLength(255)
                            ->disabled(fn(callable $get) => $get('keterangan') !== 'Reimburse' && \Illuminate\Support\Facades\Auth::user()->role === 'user'),
                    ])
                    ->columns(2)
                    ->columnSpan('full')
                    ->extraAttributes(['class' => 'mb-4']),

                Forms\Components\Fieldset::make('Dokumentasi')
                    ->schema([
                        Forms\Components\FileUpload::make('foto_unit')
                            ->image()
                            ->required()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                            ->disk('public')
                            ->directory('foto_unit') // akan simpan di storage/app/public/foto_unit
                            ->visibility('public')
                            ->afterStateUpdated(function ($state, callable $get, callable $set, $livewire) {
                                $record = $livewire->record ?? null; // Ensure $record is available
                                if ($record && $record->foto_unit && $record->foto_unit !== $state) {
                                    Storage::disk('public')->delete($record->foto_unit);
                                }
                            }),
                        Forms\Components\FileUpload::make('foto_odometer')
                            ->image()
                            ->required()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                            ->disk('public')
                            ->directory('foto_odometer') // simpan di storage/app/public/foto_odometer
                            ->visibility('public')
                            ->afterStateUpdated(function ($state, callable $get, callable $set, $livewire) {
                                $record = $livewire->record ?? null; // Ensure $record is available
                                if ($record && $record->foto_odometer && $record->foto_odometer !== $state) {
                                    Storage::disk('public')->delete($record->foto_odometer);
                                }
                            }),
                        Forms\Components\FileUpload::make('foto_kondisi')
                            ->required()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                            ->disk('public')
                            ->directory('foto_kondisi') // simpan di storage/app/public/foto_kondisi
                            ->image()
                            ->multiple()
                            ->maxFiles(3) // Add validation to limit the number of files to 3
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire) {
                                if (count($state) > 3) {
                                    $set('foto_kondisi', array_slice($state, 0, 3)); // Limit to 3 files
                                }
                                $record = $livewire->record ?? null; // Ensure $record is available
                                if ($record && is_array($record->foto_kondisi)) {
                                    $lama = collect($record->foto_kondisi);
                                    $baru = collect($state);
                                    $yangDihapus = $lama->diff($baru);
                                    foreach ($yangDihapus as $path) {
                                        Storage::disk('public')->delete($path);
                                    }
                                }
                            }),
                    ])
                    ->columns(3)
                    ->columnSpan('full')
                    ->extraAttributes(['class' => 'mb-4']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_pengajuan')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('nama')->sortable()->searchable(),
                // Tables\Columns\TextColumn::make('no_wa')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('jenis')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('type')->sortable()->searchable(),
                // Tables\Columns\TextColumn::make('nopol')->sortable()->searchable(),
                // Tables\Columns\TextColumn::make('odometer')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('service')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('project')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('up')->sortable()->searchable(),
                // Tables\Columns\TextColumn::make('provinsi')->sortable()->searchable(),
                // Tables\Columns\TextColumn::make('kota')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('keterangan')->sortable()->searchable(),
                // Tables\Columns\TextColumn::make('payment_1')->sortable()->searchable(),
                // Tables\Columns\TextColumn::make('bank_1')->sortable()->searchable(),
                // Tables\Columns\TextColumn::make('norek_1')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('keterangan_proses')
                    ->label('Status Proses')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return match ($record->keterangan_proses) {
                            'cs' => 'Customer Service',
                            'finance' => 'Finance',
                            'done' => 'Selesai',
                            default => 'Tidak Diketahui',
                        };
                    })
                    ->color(fn(string $state) => match ($state) {
                        'Customer Service' => 'gray',
                        'Finance' => 'warning',
                        'Selesai' => 'success',
                        default => 'gray',
                    }),
                // Tables\Columns\ImageColumn::make('foto_unit')
                //     ->label('Foto Unit')
                //     ->getStateUsing(fn($record) => asset('storage/' . $record->foto_unit))
                //     ->height(50),
                // Tables\Columns\ImageColumn::make('foto_odometer')
                //     ->label('Foto Odometer')
                //     ->getStateUsing(fn($record) => asset('storage/' . $record->foto_odometer))
                //     ->height(50),
                // Tables\Columns\TextColumn::make('foto_kondisi')
                //     ->label('Semua Foto Kondisi')
                //     ->html()
                //     ->getStateUsing(function ($record) {
                //         return '<div style="display:flex; gap:6px;">' . collect($record->foto_kondisi)
                //             ->map(fn($file) => '<img src="' . asset('storage/' . $file) . '" width="50"/>')
                //             ->implode('') . '</div>';
                //     }),
            ])
            ->filters([
                SelectFilter::make('keterangan_proses')
                    ->label('Status Proses')
                    ->options([
                        'cs' => 'Customer Service',
                        'finance' => 'Finance',
                        'done' => 'Selesai',
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detail'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Pengajuan $record) {
                        $record->delete();
                        Notification::make()
                            ->title('Data pengajuan berhasil dihapus.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('Proses')
                    ->label('Proses')
                    ->icon('heroicon-o-pencil')
                    ->form([
                        Forms\Components\Fieldset::make('Informasi Bengkel')
                            ->schema([
                                Hidden::make('user_id')
                                    ->default(\Illuminate\Support\Facades\Auth::user()->id),
                                Forms\Components\TextInput::make('bengkel_estimasi')
                                    ->label('Bengkel Estimasi')
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
                                    ])
                                    ->required()
                                    ->default(fn($record) => $record->complete?->kode),
                            ])
                            ->columns(1),
                        Forms\Components\Fieldset::make('Informasi Finance')
                            ->schema([
                                Forms\Components\DatePicker::make('tanggal_masuk_finance')
                                    ->label('Tanggal Masuk Finance')
                                    ->required()
                                    ->default(fn($record) => $record->complete?->tanggal_masuk_finance),
                                Forms\Components\DatePicker::make('tanggal_tf_finance')
                                    ->label('Tanggal Transfer Finance')
                                    // ->required()
                                    ->default(fn($record) => $record->complete?->tanggal_tf_finance),
                                Forms\Components\TextInput::make('nominal_tf_finance')
                                    ->label('Nominal Transfer Finance')
                                    ->numeric()
                                    // ->required()
                                    ->default(fn($record) => $record->complete?->nominal_tf_finance),
                                Forms\Components\TextInput::make('payment_2')
                                    ->label('Metode Pembayaran')
                                    // ->required()
                                    ->default(fn($record) => $record->complete?->payment_2),
                                Forms\Components\TextInput::make('bank_2')
                                    ->label('Bank')
                                    // ->required()
                                    ->default(fn($record) => $record->complete?->bank_2),
                                Forms\Components\TextInput::make('norek_2')
                                    ->label('No. Rekening')
                                    // ->required()
                                    ->default(fn($record) => $record->complete?->norek_2),
                                Forms\Components\Select::make('status_finance')
                                    ->label('Status Finance')
                                    ->options([
                                        'paid' => 'Paid',
                                        'unpaid' => 'Unpaid',
                                    ])
                                    ->required()
                                    ->default(fn($record) => $record->complete?->status_finance ?? 'unpaid'),
                            ])
                            ->columns(2),
                        Forms\Components\Fieldset::make('Transfer Bengkel')
                            ->schema([
                                Forms\Components\TextInput::make('nominal_tf_bengkel')
                                    ->label('Nominal Transfer Bengkel')
                                    ->numeric()
                                    ->nullable()
                                    ->default(fn($record) => $record->complete?->nominal_tf_bengkel),
                                Forms\Components\TextInput::make('selisih_tf')
                                    ->label('Selisih Transfer')
                                    ->numeric()
                                    ->default(fn($record) => $record->complete?->selisih_tf ?? 0)
                                    ->reactive()
                                    ->afterStateUpdated(fn(callable $set, $state, callable $get) => $set('selisih_tf', $get('nominal_tf_finance') - $get('nominal_tf_bengkel'))),
                                Forms\Components\DatePicker::make('tanggal_tf_bengkel')
                                    ->label('Tanggal Transfer Bengkel')
                                    ->nullable()
                                    ->default(fn($record) => $record->complete?->tanggal_tf_bengkel),
                                Forms\Components\DatePicker::make('tanggal_pengerjaan')
                                    ->label('Tanggal Pengerjaan')
                                    ->nullable()
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
                        $record->complete()->updateOrCreate([], $data); // Update or create related data
                        $statusFinance = $data['status_finance'] ?? 'unpaid';
                        $record->update(['keterangan_proses' => $statusFinance === 'paid' ? 'done' : 'finance']);
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
            ])
            ->defaultSort('id', 'desc') // Optional: Add default sorting
            ->recordUrl(null); // Disable row hover linking
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
                                                'finance' => 'Finance',
                                                'done' => 'Selesai',
                                                default => 'Tidak Diketahui',
                                            };
                                        })
                                        ->color(fn(string $state) => match ($state) {
                                            'Customer Service' => 'primary',
                                            'Finance' => 'warning',
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
                Components\Section::make('Informasi Admin')
                    ->schema([
                        Components\Grid::make(1)
                            ->schema([
                                Components\Group::make([
                                    Components\TextEntry::make('admin.kode')
                                        ->label('Kode'),
                                ]),
                            ]),
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('admin.bengkel_estimasi')
                                    ->label('Bengkel Estimasi'),
                                Components\TextEntry::make('admin.no_telp_bengkel')
                                    ->label('No. Telp Bengkel'),
                                Components\TextEntry::make('admin.nominal_estimasi')
                                    ->label('Nominal Estimasi'),
                            ])
                            ->label('Informasi Bengkel'),
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('admin.tanggal_masuk_finance')
                                    ->label('Tanggal Masuk Finance')
                                    ->dateTime(),
                                Components\TextEntry::make('admin.tanggal_tf_finance')
                                    ->label('Tanggal Transfer Finance')
                                    ->dateTime(),
                                Components\TextEntry::make('admin.nominal_tf_finance')
                                    ->label('Nominal Transfer Finance'),
                            ]),
                        Components\Grid::make(1)
                            ->schema([
                                Components\Group::make([
                                    Components\TextEntry::make('admin.payment_2')
                                        ->label('Metode Pembayaran'),
                                ]),
                            ]),
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('admin.bank_2')
                                    ->label('Bank'),
                                Components\TextEntry::make('admin.norek_2')
                                    ->label('No. Rekening'),
                                Components\TextEntry::make('admin.status_finance')
                                    ->label('Status Finance'),
                            ]),
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('admin.nominal_tf_bengkel')
                                    ->label('Nominal Transfer Bengkel'),
                                Components\TextEntry::make('admin.selisih_tf')
                                    ->label('Selisih Transfer'),
                                Components\TextEntry::make('admin.tanggal_tf_bengkel')
                                    ->label('Tanggal Transfer Bengkel')
                                    ->dateTime(),
                                Components\TextEntry::make('admin.tanggal_pengerjaan')
                                    ->label('Tanggal Pengerjaan')
                                    ->dateTime(),
                            ]),
                    ])
                    ->visible(fn($record) => !empty($record->admin)), // Only show when admin data is filled
                Components\Section::make('Dokumentasi Admin')
                    ->schema([
                        ViewEntry::make('admin.foto_nota')
                            ->label('Foto Nota')
                            ->view('filament.components.foto-nota')
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                            ]),
                        ViewEntry::make('admin.foto_pengerjaan_bengkel')
                            ->label('Foto Pengerjaan Bengkel')
                            ->view('filament.components.foto-pengerjaan-bengkel')
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                            ]),
                        ViewEntry::make('admin.foto_tambahan')
                            ->label('Foto Tambahan')
                            ->view('filament.components.foto-tambahan')
                            ->columnSpan([
                                'default' => 4,
                                'md' => 3,
                            ]),
                    ])
                    ->columns([
                        'default' => 4,
                        'md' => 5,
                    ])
                    ->visible(fn($record)=> !empty($record->admin)),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewPengajuan::class,
            Pages\EditPengajuan::class,
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
            'index' => Pages\ListPengajuans::route('/'),
            'create' => Pages\CreatePengajuan::route('/create'),
            'edit' => Pages\EditPengajuan::route('/{record}/edit'),
            'view' => Pages\ViewPengajuan::route('/{record}'),
        ];
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
}
