<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PraPengajuanResource\Pages;
use App\Models\PraPengajuan;
use App\Models\Project;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PraPengajuanResource extends Resource
{
    protected static ?string $model = PraPengajuan::class;
    protected static ?string $navigationGroup = 'Pengajuan';
    protected static ?string $navigationLabel = 'Pra Pengajuan';
    protected static ?string $pluralLabel = 'Pra Pengajuan';

    private const SERVICE_LABELS = [
        'service_ganti_oli' => 'Service Ganti Oli',
        'rem_depan' => 'Rem Depan',
        'rem_belakang' => 'Rem Belakang',
        'lampu_depan' => 'Lampu Depan',
        'lampu_belakang' => 'Lampu Belakang',
        'ban_depan' => 'Ban Depan',
        'ban_belakang' => 'Ban Belakang',
        'gear_set' => 'Gear Set',
        'kampas_kopling' => 'Kampas Kopling',
        'fikter_udara' => 'Fikter Udara',
        'filter_oli' => 'Filter Oli',
        'busi' => 'Busi',
        'ban_dalam' => 'Ban Dalam',
        'spion' => 'Spion',
        'lampu_stop' => 'Lampu Stop',
        'lampu_sein_depan' => 'Lampu Sein depan',
        'lampu_sein_belakang' => 'Lampu Sein Belakang',
        'bearing_depan' => 'Bearing Depan',
        'bearung_belakang' => 'Bearung Belakang',
        'accu' => 'Accu',
        'lainnya' => 'Lainnya',
    ];

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
                                            Forms\Components\TextInput::make('nama_pic')
                                                ->label('Nama PIC')
                                                ->required()
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
                            ]),
                    Step::make('Data Service')
                        ->schema([
                            Forms\Components\Repeater::make('service_units')
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
                                        ])
                                ])
                        ])

                ])
                ->columnSpan('full')

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_pic')
                    ->label('Nama PIC')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_wa')
                    ->label('No. WhatsApp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('up')
                    ->label('UP')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis Kendaraan')
                    ->getStateUsing(function ($record) {
                        // Ambil semua service yang berelasi dengan pengajuan ini
                        $services = $record->service_units()->with('unit')->get();
                        // Format: [nama_service (nopol)], dipisah baris baru
                        return $services->map(function ($service) {
                            $jenis = $service->unit?->jenis ?? '-';
                            return "{$jenis}";
                        })->implode('<br>');
                    })
                    ->html()
                    ->searchable(query: function (Builder $query, string $search) {
                        // Join ke tabel service_unit dan unit, lalu filter berdasarkan nama service atau nopol
                        $query->whereHas('service_units.unit', function ($q) use ($search) {
                            $q->where('jenis', 'like', "%{$search}%");
                        });
                    })
                    ->width('120px')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('service_units')
                    ->label('Service')
                    ->getStateUsing(function ($record) {
                        // Ambil semua service yang berelasi dengan pengajuan ini
                        $services = $record->service_units()->with('unit')->get();
                        // Format: [nama_service (nopol)], dipisah baris baru
                        return $services->map(function ($service) {
                            return "{$service->service}";
                        })->implode('<br>');
                    })
                    ->html()
                    ->searchable(query: function (Builder $query, string $search) {
                        // Join ke tabel service_unit dan unit, lalu filter berdasarkan nama service atau nopol
                        $query->whereHas('service_units.unit', function ($q) use ($search) {
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
                        $services = $record->service_units()->with('unit')->get();
                        // Format: [nama_service (nopol)], dipisah baris baru
                        return $services->map(function ($service) {
                            $nopol = $service->unit?->nopol ?? '-';
                            return "{$nopol}";
                        })->implode('<br>');
                    })
                    ->html()
                    ->searchable(query: function (Builder $query, string $search) {
                        // Join ke tabel service_unit dan unit, lalu filter berdasarkan nama service atau nopol
                        $query->whereHas('service_units.unit', function ($q) use ($search) {
                            $q->where('nopol', 'like', "%{$search}%");
                        });
                    })
                    ->width('120px')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('up')
                    ->label('Filter UP')
                    ->options(function () {
                        return PraPengajuan::query()
                            ->select('up')
                            ->whereNotNull('up')
                            ->distinct()
                            ->orderBy('up')
                            ->pluck('up', 'up')
                            ->toArray();
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('ajukan')
                        ->label('Ajukan')
                        ->icon('heroicon-o-arrow-up')
                        ->action(function ($records) {
                            $params = $records->pluck('id')->toArray();
                            redirect()->route('ajukan-multiple-pra-pengajuan', ['ids' => implode(',', $params)]);
                            Notification::make()
                                ->title('Pengajuan Pra Pengajuan')
                                ->body('Pra Pengajuan berhasil diproses ke Pengajuan.')
                                ->success()
                                ->send();
                        })
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Ajukan Pra Pengajuan')
                        ->modalSubheading('Apakah Anda yakin ingin mengajukan Pra Pengajuan yang dipilih?')
                        ->modalButton('Ya, Ajukan')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Informasi Pra Pengajuan')
                    ->schema([
                        Components\TextEntry::make('nama_pic')->label('Nama PIC'),
                        Components\TextEntry::make('no_wa')->label('No. WhatsApp'),
                        Components\TextEntry::make('project')->label('Project'),
                        Components\TextEntry::make('up')->label('UP'),
                        Components\TextEntry::make('up_lainnya')->label('UP Lainnya')->placeholder('-'),
                        Components\TextEntry::make('provinsi')->label('Provinsi'),
                        Components\TextEntry::make('kota')->label('Kota'),
                        Components\TextEntry::make('tanggal')->label('Tanggal')->date('d/m/Y'),
                        Components\TextEntry::make('status')->label('Status')->badge(),
                        Components\Section::make('Detail Kendaraan')
                            ->schema([
                                ViewEntry::make('service_units.pra_pengajuan_id')
                                    ->label('Detail Kendaraan')
                                    ->view('filament.resources.pages.pengajuan.detail-kendaraan-praPengajuan')
                                    ->columnSpanFull(),
                            ]),

                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPraPengajuans::route('/'),
            'create' => Pages\CreatePraPengajuan::route('/create'),
            'view' => Pages\ViewPraPengajuan::route('/{record}'),
            'edit' => Pages\EditPraPengajuan::route('/{record}/edit'),
        ];
    }

    public static function formatServices(?string $state): string
    {
        if (blank($state)) {
            return '-';
        }

        $items = collect(explode(',', (string) $state))
            ->map(fn(string $value) => trim($value))
            ->filter()
            ->map(function (string $value) {
                if (str_starts_with($value, 'custom::')) {
                    return ucwords(str_replace('_', ' ', substr($value, 8)));
                }

                return self::SERVICE_LABELS[$value] ?? $value;
            })
            ->values()
            ->all();

        return empty($items) ? '-' : implode(', ', $items);
    }
}
