<?php

namespace App\Filament\User\Resources;

use Filament\Forms;
use App\Models\Unit;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Form;
use App\Models\Pengajuan;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Infolists\Components;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Wizard;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\ViewEntry;
use App\Filament\User\Resources\PengajuanResource\Pages;

class PengajuanResource extends Resource
{
    protected static ?string $model = Pengajuan::class;

    protected static ?string $slug = 'pengajuan';

    protected static ?string $navigationLabel = 'Pengajuan';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Pengajuan')
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
                                                ->afterStateUpdated(fn($component, $state) => $component->state(strtoupper($state)))
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('no_wa')
                                                ->label('No. WhatsApp')
                                                ->required()
                                                ->numeric()
                                                ->maxLength(255),
                                        ]),
                                    Forms\Components\Group::make()
                                        ->schema([
                                            Forms\Components\Select::make('project')
                                                ->label('Project')
                                                ->required()
                                                ->options(Project::pluck('name', 'name')->toArray()) // key dan value = name
                                                ->searchable()
                                                ->createOptionForm([
                                                    Forms\Components\TextInput::make('name')
                                                        ->label('Nama Project')
                                                        ->required()
                                                        ->maxLength(255),
                                                ])
                                                ->createOptionUsing(function (array $data) {
                                                    Project::create(['name' => $data['name']]);
                                                    return $data['name']; // ini yang akan dipakai sebagai value dari select
                                                })
                                                ->createOptionAction(function ($action) {
                                                    $action->modalHeading('Tambah Project Baru');
                                                }),
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
                                                ->visible(fn(callable $get) => $get('up') === 'manual')
                                                ->afterStateUpdated(fn($component, $state) => $component->state(strtoupper($state))),
                                        ]),
                                    Forms\Components\TextInput::make('provinsi')
                                        ->required()
                                        ->label('Provinsi')
                                        ->afterStateUpdated(fn($component, $state) => $component->state(strtoupper($state)))
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('kota')
                                        ->required()
                                        ->label('Kota/Kab')
                                        ->afterStateUpdated(fn($component, $state) => $component->state(strtoupper($state)))
                                        ->maxLength(255),


                                ])
                                ->columns(2)
                                ->columnSpan('full')
                                ->extraAttributes(['class' => 'mb-4']),
                            Forms\Components\Fieldset::make('Pembayaran')
                                ->schema([
                                    Forms\Components\Select::make('keterangan')
                                        ->required()
                                        ->label('Jenis Pengajuan')
                                        ->options([
                                            'reimburse' => 'REIMBURSE',
                                            'cash advance' => 'CASH ADVANCE',
                                            'invoice' => 'INVOICE',
                                            'free' => 'FREE',
                                        ])
                                        ->reactive(),
                                    Forms\Components\Select::make('payment_1')
                                        ->label('Nama Rekening')
                                        ->options(
                                            \App\Models\Norek::pluck('name', 'name')->toArray()
                                        )
                                        ->searchable()
                                        ->nullable()
                                        ->reactive()
                                        ->afterStateUpdated(function ($component, $state, callable $set) {
                                            $component->state(strtoupper($state));
                                            $norek = \App\Models\Norek::where('name', $state)->first();
                                            $set('norek_1', $norek?->norek);
                                            $set('bank_1', $norek?->bank);
                                        })
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->label('Nama Rekening')
                                                ->required()
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('norek')
                                                ->label('No. Rekening')
                                                ->required()
                                                ->numeric()
                                                ->maxLength(255),
                                            Forms\Components\Select::make('bank')
                                                ->label('Bank')
                                                ->required()
                                                ->options([
                                                    'BCA' => 'BCA',
                                                    'MANDIRI' => 'MANDIRI',
                                                    'BRI' => 'BRI',
                                                    'BNI' => 'BNI',
                                                    'PERMATA' => 'PERMATA',
                                                    'BTN' => 'BTN',
                                                ]),
                                        ])
                                        ->createOptionUsing(function (array $data) {
                                            // Cek duplikat berdasarkan name atau norek
                                            $exists = \App\Models\Norek::where('name', $data['name'])
                                                ->orWhere('norek', $data['norek'])
                                                ->exists();
                                            if ($exists) {
                                                \Filament\Notifications\Notification::make()
                                                    ->title('Gagal Menambah Rekening')
                                                    ->body('Nama rekening atau nomor rekening sudah terdaftar.')
                                                    ->danger()
                                                    ->send();
                                                return $data['name'];
                                            }
                                            \App\Models\Norek::create(['name' => $data['name'], 'norek' => $data['norek'], 'bank' => $data['bank']]);
                                            \Filament\Notifications\Notification::make()
                                                ->title('Berhasil Menambah Rekening')
                                                ->body('Nama rekening dan nomor rekening berhasil ditambahkan.')
                                                ->success()
                                                ->send();
                                            return $data['name'];
                                        })
                                        ->createOptionAction(function ($action) {
                                            $action->modalHeading('Tambah Nama Rekening Baru');
                                        }),
                                    Forms\Components\TextInput::make('bank_1')
                                        ->nullable()
                                        ->label('Bank')
                                        ->readOnly()
                                        ->default(function (callable $get) {
                                            $nama = $get('payment_1');
                                            if (!$nama) return null;
                                            $norek = \App\Models\Norek::where('name', $nama)->first();
                                            return $norek?->bank;
                                        })
                                        ->reactive()
                                        ->afterStateHydrated(function ($component, $state, callable $get) {
                                            $nama = $get('payment_1');
                                            if ($nama) {
                                                $norek = \App\Models\Norek::where('name', $nama)->first();
                                                $component->state($norek?->bank);
                                            }
                                        }),

                                    Forms\Components\TextInput::make('norek_1')
                                        ->nullable()
                                        ->label('No. Rekening')
                                        ->numeric()
                                        ->maxLength(255)
                                        ->readOnly()
                                        ->default(function (callable $get) {
                                            $nama = $get('payment_1');
                                            if (!$nama) return null;
                                            $norek = \App\Models\Norek::where('name', $nama)->first();
                                            return $norek?->norek;
                                        })
                                        ->reactive()
                                        ->afterStateHydrated(function ($component, $state, callable $get) {
                                            $nama = $get('payment_1');
                                            if ($nama) {
                                                $norek = \App\Models\Norek::where('name', $nama)->first();
                                                $component->state($norek?->norek);
                                            }
                                        }),
                                ])
                                ->columns(2)
                                ->columnSpan('full')
                                ->extraAttributes(['class' => 'mb-4']),
                        ]),
                    Step::make('Data Service')
                        ->schema([
                            Forms\Components\Repeater::make('service_unit')
                                ->relationship() // penting: ini untuk relasi hasMany
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Select::make('unit_id')
                                                ->label('Unit')
                                                ->relationship('unit', 'nopol')
                                                ->getOptionLabelFromRecordUsing(function (Unit $unit) {
                                                    return "{$unit->type} - {$unit->nopol}";
                                                })
                                                ->searchable()
                                                ->preload()
                                                ->required(),
                                            Forms\Components\TextInput::make('odometer')
                                                ->numeric()
                                                ->required(),
                                        ]),
                                    Forms\Components\TextInput::make('service')
                                        ->label('Jenis Permintaan Service')
                                        ->required(),
                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\FileUpload::make('foto_unit')
                                                ->label('Foto Unit')
                                                ->image()
                                                ->resize(50)
                                                ->maxWidth(1024)
                                                ->optimize('webp')
                                                ->maxSize(2048) // Maksimal 2MB
                                                ->disk('public')
                                                ->directory('foto_unit')
                                                ->nullable(),

                                            Forms\Components\FileUpload::make('foto_odometer')
                                                ->label('Foto Odometer')
                                                ->image()
                                                ->resize(50)
                                                ->maxWidth(1024)
                                                ->optimize('webp')
                                                ->maxSize(2048) // Maksimal 2MB
                                                ->disk('public')
                                                ->directory('foto_odometer')
                                                ->nullable(),

                                            Forms\Components\FileUpload::make('foto_kondisi')
                                                ->label('Foto Kondisi')
                                                ->image()
                                                ->resize(50)
                                                ->maxWidth(1024)
                                                ->optimize('webp')
                                                ->maxSize(2048) // Maksimal 2MB
                                                ->multiple()
                                                ->maxFiles(3)
                                                ->disk('public')
                                                ->directory('foto_kondisi')
                                                ->nullable(),
                                        ])
                                ])
                        ])

                ])
                    ->columnSpan('full') // Membuat wizard full width
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_pengajuan')->sortable()->searchable()->width('130px')->label('No. Pengajuan'),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->searchable()
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->width('120px')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('User')->width('150px')->wrap(),
                Tables\Columns\TextColumn::make('nama')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Nama PIC')->width('150px')->wrap(),
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis Kendaraan')
                    ->getStateUsing(function ($record) {
                        // Ambil semua service yang berelasi dengan pengajuan ini
                        $services = $record->service_unit()->with('unit')->get();
                        // Format: [nama_service (nopol)], dipisah baris baru
                        return $services->map(function ($service) {
                            $jenis = $service->unit?->jenis ?? '-';
                            return "{$jenis}";
                        })->implode('<br>');
                    })
                    ->html()
                    ->searchable(query: function (Builder $query, string $search) {
                        // Join ke tabel service_unit dan unit, lalu filter berdasarkan nama service atau nopol
                        $query->whereHas('service_unit.unit', function ($q) use ($search) {
                            $q->where('jenis', 'like', "%{$search}%");
                        });
                    })
                    ->width('120px')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('service_unit')
                    ->label('Service')
                    ->getStateUsing(function ($record) {
                        // Ambil semua service yang berelasi dengan pengajuan ini
                        $services = $record->service_unit()->with('unit')->get();
                        // Format: [nama_service (nopol)], dipisah baris baru
                        return $services->map(function ($service) {
                            return "{$service->service}";
                        })->implode('<br>');
                    })
                    ->html()
                    ->searchable(query: function (Builder $query, string $search) {
                        // Join ke tabel service_unit dan unit, lalu filter berdasarkan nama service atau nopol
                        $query->whereHas('service_unit.unit', function ($q) use ($search) {
                            $q->where('service', 'like', "%{$search}%");
                        });
                    })
                    ->width('200px')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nopol')
                    ->label('No. Polisi')
                    ->getStateUsing(function ($record) {
                        // Ambil semua service yang berelasi dengan pengajuan ini
                        $services = $record->service_unit()->with('unit')->get();
                        // Format: [nama_service (nopol)], dipisah baris baru
                        return $services->map(function ($service) {
                            $nopol = $service->unit?->nopol ?? '-';
                            return "{$nopol}";
                        })->implode('<br>');
                    })
                    ->html()
                    ->searchable(query: function (Builder $query, string $search) {
                        // Join ke tabel service_unit dan unit, lalu filter berdasarkan nama service atau nopol
                        $query->whereHas('service_unit.unit', function ($q) use ($search) {
                            $q->where('nopol', 'like', "%{$search}%");
                        });
                    })
                    ->width('120px')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('project')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Project')->width('150px')->wrap(),
                Tables\Columns\TextColumn::make('up')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Unit Pelaksana')->width('120px')->wrap(),
                Tables\Columns\TextColumn::make('complete.bengkel_estimasi')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Bengkel')
                    ->width('150px')
                    ->wrap(),
                Tables\Columns\TextColumn::make('complete.nominal_tf_bengkel')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Nominal Transfer')
                    ->width('150px')
                    ->wrap(),
                Tables\Columns\TextColumn::make('complete.nominal_estimasi')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Nominal Estimasi')
                    ->formatStateUsing(fn($state) => $state !== null ? 'Rp ' . number_format($state, 0, ',', '.') : '-')
                    ->width('150px')
                    ->wrap(),
                Tables\Columns\TextColumn::make('keterangan')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->width('150px')->wrap(),
                Tables\Columns\TextColumn::make('keterangan_proses')
                    ->label('Status Proses')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state) => match (true) {
                        str_contains(strtoupper($state), 'CUSTOMER SERVICE') => 'black',
                        str_contains(strtoupper($state), 'VERIFIKASI') => 'danger',
                        str_contains(strtoupper($state), 'PENGAJUAN FINANCE') => 'primary',
                        str_contains(strtoupper($state), 'INPUT FINANCE') => 'brown',
                        str_contains(strtoupper($state), 'OTORISASI') => 'yellow',
                        str_contains(strtoupper($state), 'SELESAI') => 'success',
                        default => 'gray',
                    })
                    ->getStateUsing(function ($record) {
                        return match ($record->keterangan_proses) {
                            'cs' => 'Customer Service',
                            'checker' => 'Verifikasi',
                            'pengajuan finance' => 'Pengajuan Finance',
                            'finance' => 'Input Finance',
                            'otorisasi' => 'Otorisasi',
                            'done' => 'Selesai',
                            default => 'Tidak Diketahui',
                        };
                    })
                    ->width('150px')
                    ->wrap(),
                Tables\Columns\TextColumn::make('complete.bengkel_invoice')
                    ->label('Bengkel Invoice')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->width('150px')
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('keterangan_proses')
                    ->label('Status Proses')
                    ->options([
                        'cs' => 'Customer Service',
                        'checker' => 'Verifikasi',
                        'pengajuan finance' => 'Pengajuan Finance',
                        'finance' => 'Input Finance',
                        'otorisasi' => 'Otorisasi',
                        'done' => 'Selesai',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
                            ->getStateUsing(fn($record) =>
                                $record->logUpdateStatusPengajuans()
                                    ->orderBy('created_at', 'desc')
                                    ->get()
                                    ->map(fn($log) => "{$log->status_baru} - " . $log->created_at->format('d M Y H:i:s'))
                                    ->implode('<br>')
                            )
                            ->html(),
                    ])
                    ->columns(2),
                Section::make('Pembayaran')
                    ->schema([
                        Components\Grid::make(3)
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
            //
        ];
    }

    public static function getGlobalSearchResultUrl(\Illuminate\Database\Eloquent\Model $record): string
    {
        return PengajuanResource::getUrl('view', ['record' => $record]);
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
        return null;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Filament::auth()->id());
    }
}
