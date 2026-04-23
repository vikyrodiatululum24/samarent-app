<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Unit;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Form;
use App\Models\Pengajuan;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Infolists\Components;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Wizard;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;
use App\Filament\Imports\PengajuanImporter;
use Filament\Infolists\Components\ViewEntry;
use App\Models\BosJoulmer;
use App\Filament\Resources\PengajuanResource\Pages;


class PengajuanResource extends Resource
{
    protected static ?string $model = Pengajuan::class;

    protected static ?string $navigationGroup = 'Pengajuan';

    protected static ?string $slug = 'pengajuan';

    protected static ?string $recordTitleAttribute = 'no_pengajuan';

    protected static ?int $navigationSort = 0;

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
                                                ->default(Auth::user()->id),
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
                                                ->required(),
                                        ]),
                                    Forms\Components\TextInput::make('service')
                                        ->label('Jenis Permintaan Service')
                                        ->required(),
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Grid::make(3)
                                                ->schema([
                                                    Forms\Components\FileUpload::make('foto_unit')
                                                        ->label('Foto Unit')
                                                        ->image()
                                                        ->resize(50)
                                                        ->optimize('webp')
                                                        ->maxWidth(1024)
                                                        ->maxSize(2048) // Maksimal 2MB
                                                        ->disk('public')
                                                        ->directory('foto_unit')
                                                        ->nullable(),
                                                    Forms\Components\FileUpload::make('foto_odometer')
                                                        ->label('Foto Odometer')
                                                        ->image()
                                                        ->resize(50)
                                                        ->optimize('webp')
                                                        ->maxWidth(1024)
                                                        ->maxSize(2048) // Maksimal 2MB
                                                        ->disk('public')
                                                        ->directory('foto_odometer')
                                                        ->nullable(),
                                                    Forms\Components\FileUpload::make('foto_kondisi')
                                                        ->label('Foto Kondisi')
                                                        ->image()
                                                        ->resize(50)
                                                        ->optimize('webp')
                                                        ->maxWidth(1024)
                                                        ->maxSize(2048) // Maksimal 2MB
                                                        ->multiple()
                                                        ->maxFiles(3)
                                                        ->disk('public')
                                                        ->directory('foto_kondisi')
                                                        ->nullable(),
                                                ]),
                                            Forms\Components\FileUpload::make('foto_pengerjaan_bengkel')
                                                ->label('Foto Pengerjaan Bengkel')
                                                ->image()
                                                ->resize(50)
                                                ->optimize('webp')
                                                ->maxWidth(1024)
                                                ->maxSize(2048) // Maksimal 2MB
                                                ->disk('public')
                                                ->directory('foto_pengerjaan_bengkel')
                                                ->nullable()
                                                ->reactive()
                                                ->dehydrated(),
                                            Forms\Components\FileUpload::make('foto_tambahan')
                                                ->label('Foto Tambahan')
                                                ->image()
                                                ->resize(50)
                                                ->optimize('webp')
                                                ->maxWidth(1024)
                                                ->maxSize(2048) // Maksimal 2MB
                                                ->disk('public')
                                                ->directory('foto_tambahan')
                                                ->multiple()
                                                ->maxFiles(3)
                                                ->nullable(),
                                        ])
                                ])
                        ])

                ])
                    ->columnSpan('full') // Membuat wizard full width
            ])
            ->columns(1); // Pastikan form satu kolom agar full width
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
                Tables\Columns\TextColumn::make('up')
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->where(function (Builder $q) use ($search) {
                            $q->where('up', 'like', "%{$search}%")
                                ->orWhere('up_lainnya', 'like', "%{$search}%");
                        });
                    })
                    ->getStateUsing(fn($record) => $record->up === 'manual' ? ($record->up_lainnya ?: '-') : $record->up)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Unit Pelaksana')
                    ->width('120px')
                    ->wrap(),
                
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
                        str_contains(strtoupper($state), 'PENGAJUAN ATASAN') => 'info',
                        str_contains(strtoupper($state), 'PENGAJUAN FINANCE') => 'primary',
                        str_contains(strtoupper($state), 'INPUT FINANCE') => 'brown',
                        str_contains(strtoupper($state), 'OTORISASI') => 'yellow',
                        str_contains(strtoupper($state), 'SELESAI') => 'success',
                        default => 'gray',
                    })
                    ->getStateUsing(function ($record) {
                        $prosesPengajuan = $record->keterangan_proses ?? '';
                        $statusBos = $record->bos_joulmer?->is_approved;

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

                        if ($prosesPengajuan === 'pengajuan atasan') {
                            $bosText = match ($statusBos) {
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                'pending' => 'Pending',
                                default => 'Tidak Diketahui',
                            };
                            return "{$prosesText} - {$bosText}";
                        }

                        return $prosesText;
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
                        'pengajuan atasan' => 'Pengajuan Atasan',
                        'pengajuan finance' => 'Pengajuan Finance',
                        'finance' => 'Input Finance',
                        'otorisasi' => 'Otorisasi',
                        'done' => 'Selesai',
                    ]),
                SelectFilter::make('bos_status')
                    ->label('Status di Atasan')
                    ->options([
                        'pending' => 'Menunggu',
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
                Tables\Actions\Action::make('Edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->url(fn($record) => PengajuanResource::getUrl('edit-complete', ['record' => $record])),
                Tables\Actions\Action::make('Proses')
                    ->label('Proses')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->url(fn($record) => PengajuanResource::getUrl('proses-complete', ['record' => $record])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // ExportBulkAction::make()->exporter(ServiceUnitExporter::class),
                    Tables\Actions\BulkAction::make('check')
                        ->label('Verifikasi')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['keterangan_proses' => 'pengajuan finance']);
                            }
                            Notification::make()
                                ->title('Status pengajuan diubah menjadi Pengajuan Finance.')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->modalHeading('Konfirmasi Pengajuan Finance')
                        ->modalSubheading('Apakah Anda yakin ingin mengubah status semua pengajuan yang dipilih menjadi "Pengajuan Finance"?')
                        ->modalButton('Ya, Ubah Status'),
                    Tables\Actions\BulkAction::make('submit_to_bos')
                        ->label('Ajukan Keatasan')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('info')
                        ->action(function ($records) {
                            $submittedCount = 0;
                            $skippedCount = 0;

                            foreach ($records as $record) {
                                if ($record->keterangan_proses === 'checker' || $record->keterangan_proses === 'pengajuan atasan' && $record->bos_joulmer?->is_approved === 'rejected') {
                                    BosJoulmer::updateOrCreate(
                                        ['pengajuan_id' => $record->id],
                                        [
                                            'user_id' => Auth::id(),
                                            'is_approved' => 'pending',
                                        ]
                                    );
                                    $record->update(['keterangan_proses' => 'pengajuan atasan']);
                                    $submittedCount++;
                                } else {
                                    $skippedCount++;
                                }
                            }

                            if ($submittedCount > 0) {
                                Notification::make()
                                    ->title("{$submittedCount} pengajuan berhasil diajukan ke Atasan.")
                                    ->success()
                                    ->send();
                            }

                            if ($skippedCount > 0) {
                                Notification::make()
                                    ->title("{$skippedCount} pengajuan dilewati karena status bukan Verifikasi atau sudah diajukan ke Atasan.")
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->modalHeading('Konfirmasi Ajukan ke Atasan')
                        ->modalSubheading('Pengajuan yang dipilih akan dikirim ke Atasan untuk review dan status menjadi "Menunggu Atasan".')
                        ->modalButton('Ya, Ajukan')
                ]),
            ])
            ->headerActions([
                // ExportAction::make()->exporter(ServiceUnitExporter::class),
                ImportAction::make()->importer(PengajuanImporter::class)

            ])
            ->defaultSort('id', 'desc'); // Optional: Add default sorting
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Informasi Umum')
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
                            ->getStateUsing(fn($record) => match ($record->bos_joulmer?->is_approved) {
                                'pending' => 'Menunggu Approval',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                default => '-',
                            })
                            ->badge()
                            ->color(fn(string $state) => match ($state) {
                                'Menunggu Approval' => 'info',
                                'Disetujui' => 'success',
                                'Ditolak' => 'danger',
                            }),
                        Components\TextEntry::make('bos_joulmer.note')
                            ->label('Catatan Atasan')
                            ->getStateUsing(fn($record) => $record->bos_joulmer?->note ?: '-')
                            ->columnSpanFull()
                            ->visible(fn($record) => in_array($record->bos_joulmer?->is_approved, ['approved', 'rejected'], true)),
                    ])
                    ->columns(2),
                Components\Section::make('Pembayaran')
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
                Components\Section::make('Detail Kendaraan')
                    ->schema([
                        ViewEntry::make('service_unit.pengajuan_id')
                            ->label('Detail Kendaraan')
                            ->view('filament.resources.pages.pengajuan.detail-kendaraan')
                            ->columnSpanFull(),
                    ]),
                Components\Section::make('Informasi Complete')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\Group::make([
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
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('complete.bengkel_estimasi')
                                    ->label('Bengkel Estimasi'),
                                Components\TextEntry::make('complete.no_telp_bengkel')
                                    ->label('No. Telp Bengkel'),
                                Components\TextEntry::make('complete.nominal_estimasi')
                                    ->label('Nominal Estimasi')
                                    ->formatStateUsing(fn($state) => $state !== null ? 'Rp ' . number_format($state, 0, ',', '.') : '-'),
                            ])
                            ->label('Informasi Bengkel'),
                        Components\Grid::make(3)
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
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('complete.bank_2')
                                    ->label('Bank'),
                                Components\TextEntry::make('complete.payment_2')
                                    ->label('Nama Rekening'),
                                Components\TextEntry::make('complete.norek_2')
                                    ->label('No. Rekening'),
                            ]),
                        Components\Grid::make(1)
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
                Components\Section::make('Informasi Bengkel')
                    ->schema([
                        Components\Grid::make(3)
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
                Components\Section::make('Dokumentasi Complete')
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
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewPengajuan::class,
            Pages\EditPengajuan::class,
            Pages\ProsesCompletePengajuan::class,
            Pages\EditCompletePengajuan::class,
        ]);
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
            'edit-complete' => Pages\EditCompletePengajuan::route('/{record}/edit-complete'),
            'proses-complete' => Pages\ProsesCompletePengajuan::route('/{record}/proses-complete'),
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
