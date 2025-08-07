<?php

namespace App\Filament\Asuransi\Resources;

use App\Filament\Asuransi\Resources\AsuransiResource\Pages;
use App\Filament\Asuransi\Resources\AsuransiResource\RelationManagers;
use App\Models\Asuransi;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AsuransiResource extends Resource
{
    protected static ?string $model = Asuransi::class;
    protected static ?string $navigationLabel = 'Data Asuransi';
    protected static ?string $label = 'Data Asuransi';
    protected static ?string $pluralLabel = 'Data Asuransi';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Umum')
                    ->schema([
                        Forms\Components\Select::make('unit_id')
                            ->label('Unit')
                            ->searchable()
                            ->required()
                            ->relationship('unit', 'nopol')
                            ->getOptionLabelFromRecordUsing(function (Unit $unit) {
                                return "{$unit->nopol} - {$unit->type}";
                            })
                            ->preload(),
                        Forms\Components\TextInput::make('nama_pic')
                            ->label('Nama PIC')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('tanggal_pengajuan')
                            ->label('Tanggal Pengajuan')
                            ->default(now()),

                        Forms\Components\Select::make('up')
                            ->label('Unit Pelaksana')
                            ->options([
                                'UP 1' => 'UP 1',
                                'UP 2' => 'UP 2',
                                'UP 3' => 'UP 3',
                                'UP 5' => 'UP 5',
                                'UP 7' => 'UP 7',
                                'CUST JEPANG' => 'CUST JEPANG',
                            ]),
                        Forms\Components\Textarea::make('lokasi')
                            ->label('Lokasi')
                            ->columnSpan([
                                'sm' => 2,
                            ])
                            ->maxLength(255),
                    ])
                    ->columns([
                        'sm' => 2,
                    ]),
                Forms\Components\Section::make('Detail Asuransi')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Asuransi')
                            ->maxLength(255),
                        Forms\Components\Select::make('jenis')
                            ->label('Jenis Asuransi')
                            ->options([
                                'TLO' => 'TLO (Total Lost Only)',
                                'All Risk' => 'All Risk',
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('periode_mulai')
                                    ->label('Periode Mulai'),
                                Forms\Components\DatePicker::make('periode_selesai')
                                    ->label('Periode Selesai')
                            ]),
                        Forms\Components\TextInput::make('nominal')
                            ->label('Nominal')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('kategori')
                            ->label('Kategori'),
                        Forms\Components\Textarea::make('status')
                            ->label('Status')
                            ->columnSpan([
                                'sm' => 2,
                            ])
                    ])
                    ->columns([
                        'sm' => 2,
                    ]),

                Forms\Components\Section::make('Keterangan')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_kejadian')
                            ->label('Tanggal Kejadian')
                            ->default(now()),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan Insiden')
                            ->rows(3),
                        Forms\Components\Textarea::make('tujuan_pengajuan')
                            ->label('Tujuan Pengajuan')
                            ->rows(3),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Dokumen Pendukung')
                    ->schema([
                        Forms\Components\FileUpload::make('foto_ktp')
                            ->label('Foto KTP')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('asuransi/ktp'),

                        Forms\Components\FileUpload::make('foto_sim')
                            ->label('Foto SIM')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('asuransi/sim'),

                        Forms\Components\FileUpload::make('foto_sntk')
                            ->label('Foto STNK')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('asuransi/stnk'),

                        Forms\Components\FileUpload::make('foto_bpkb')
                            ->label('Foto BPKB')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('asuransi/bpkb'),

                        Forms\Components\FileUpload::make('foto_polis_asuransi')
                            ->label('Foto Polis Asuransi')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('asuransi/polis'),

                        Forms\Components\FileUpload::make('foto_ba')
                            ->label('Foto BA')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('asuransi/ba'),

                        Forms\Components\FileUpload::make('foto_keterangan_bengkel')
                            ->label('Foto Keterangan Bengkel')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('asuransi/bengkel'),

                        Forms\Components\FileUpload::make('foto_npwp_pt')
                            ->label('Foto NPWP PT')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('asuransi/npwp'),

                        Forms\Components\FileUpload::make('foto_nota')
                            ->label('Foto Nota')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->multiple()
                            ->maxFiles(4)
                            ->disk('public')
                            ->directory('asuransi/nota'),

                        Forms\Components\FileUpload::make('foto_unit')
                            ->label('Foto Unit')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->multiple()
                            ->maxFiles(4)
                            ->disk('public')
                            ->directory('asuransi/unit'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_pengajuan')
                    ->label('Tanggal Pengajuan')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('unit.nopol')
                    ->label('No. Polisi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_pic')
                    ->label('Nama PIC')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Asuransi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'TLO' => 'warning',
                        'All Risk' => 'primary',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Diproses' => 'info',
                        'Selesai' => 'success',
                        'Ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('periode_mulai')
                    ->label('Periode Mulai')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('periode_selesai')
                    ->label('Periode Selesai')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('up')
                    ->label('UP')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tujuan_pengajuan')
                    ->label('Tujuan Pengajuan')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->label('Jenis Asuransi')
                    ->options([
                        'TLO' => 'TLO (Total Lost Only)',
                        'Comprehensive' => 'Comprehensive',
                        'Third Party' => 'Third Party',
                    ]),

                Tables\Filters\SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'Baru' => 'Baru',
                        'Perpanjangan' => 'Perpanjangan',
                        'Klaim' => 'Klaim',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Diproses' => 'Diproses',
                        'Selesai' => 'Selesai',
                        'Ditolak' => 'Ditolak',
                    ]),

                Tables\Filters\SelectFilter::make('up')
                    ->label('Unit Pelaksana')
                    ->options([
                        'UP 1' => 'UP 1',
                        'UP 2' => 'UP 2',
                        'UP 3' => 'UP 3',
                        'UP 5' => 'UP 5',
                        'UP 7' => 'UP 7',
                        'CUST JEPANG' => 'CUST JEPANG',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Umum')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('unit.nopol')
                                    ->label('No. Polisi Unit')
                                    ->badge()
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('unit.type')
                                    ->label('Tipe Unit')
                                    ->badge()
                                    ->color('info'),

                                Infolists\Components\TextEntry::make('nama_pic')
                                    ->label('Nama PIC')
                                    ->color('gray'),

                                Infolists\Components\TextEntry::make('tanggal_pengajuan')
                                    ->label('Tanggal Pengajuan')
                                    ->date('d M Y')
                                    ->color('gray'),

                                Infolists\Components\TextEntry::make('up')
                                    ->label('Unit Pelaksana')
                                    ->badge()
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('lokasi')
                                    ->label('Lokasi')
                                    ->color('gray')
                                    ->visible(fn($record) => !empty($record->lokasi)),
                            ]),
                    ]),

                Infolists\Components\Section::make('Detail Asuransi')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('nama')
                                    ->label('Nama Asuransi')
                                    ->color('gray')
                                    ->weight('bold'),

                                Infolists\Components\TextEntry::make('jenis')
                                    ->label('Jenis Asuransi')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'TLO' => 'warning',
                                        'All Risk' => 'primary',
                                        default => 'gray',
                                    }),


                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('periode_mulai')
                                        ->label('Periode Mulai')
                                        ->color('gray'),
                                    Infolists\Components\TextEntry::make('periode_selesai')
                                        ->label('Periode Selesai')
                                        ->color('gray'),
                                ])
                                    ->label('Periode Asuransi')
                                    ->columns(2),

                                Infolists\Components\TextEntry::make('nominal')
                                    ->label('Nominal')
                                    ->money('IDR')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('kategori')
                                    ->label('Kategori')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'Baru' => 'success',
                                        'Perpanjangan' => 'warning',
                                        'Klaim' => 'danger',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->size('lg')
                                    ->color(fn(string $state): string => match ($state) {
                                        'Pending' => 'warning',
                                        'Diproses' => 'info',
                                        'Selesai' => 'success',
                                        'Ditolak' => 'danger',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),

                Infolists\Components\Section::make('Keterangan')
                    ->schema([
                        Infolists\Components\TextEntry::make('keterangan')
                            ->label('Keterangan Insiden')
                            ->prose()
                            ->visible(fn($record) => !empty($record->keterangan)),

                        Infolists\Components\TextEntry::make('tujuan_pengajuan')
                            ->label('Tujuan Pengajuan')
                            ->prose()
                            ->visible(fn($record) => !empty($record->tujuan_pengajuan)),
                    ])
                    ->columns(1),

                Infolists\Components\Section::make('Dokumen Pendukung')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\ImageEntry::make('foto_ktp')
                                    ->label('Foto KTP')
                                    ->disk('public')
                                    ->height(200)
                                    ->visible(fn($record) => !empty($record->foto_ktp)),

                                Infolists\Components\ImageEntry::make('foto_sim')
                                    ->label('Foto SIM')
                                    ->disk('public')
                                    ->height(200)
                                    ->visible(fn($record) => !empty($record->foto_sim)),

                                Infolists\Components\ImageEntry::make('foto_sntk')
                                    ->label('Foto STNK')
                                    ->disk('public')
                                    ->height(200)
                                    ->visible(fn($record) => !empty($record->foto_sntk)),

                                Infolists\Components\ImageEntry::make('foto_bpkb')
                                    ->label('Foto BPKB')
                                    ->disk('public')
                                    ->height(200)
                                    ->visible(fn($record) => !empty($record->foto_bpkb)),

                                Infolists\Components\ImageEntry::make('foto_polis_asuransi')
                                    ->label('Foto Polis Asuransi')
                                    ->disk('public')
                                    ->height(200)
                                    ->visible(fn($record) => !empty($record->foto_polis_asuransi)),

                                Infolists\Components\ImageEntry::make('foto_ba')
                                    ->label('Foto BA')
                                    ->disk('public')
                                    ->height(200)
                                    ->visible(fn($record) => !empty($record->foto_ba)),

                                Infolists\Components\ImageEntry::make('foto_keterangan_bengkel')
                                    ->label('Foto Keterangan Bengkel')
                                    ->disk('public')
                                    ->height(200)
                                    ->visible(fn($record) => !empty($record->foto_keterangan_bengkel)),

                                Infolists\Components\ImageEntry::make('foto_npwp_pt')
                                    ->label('Foto NPWP PT')
                                    ->disk('public')
                                    ->height(200)
                                    ->visible(fn($record) => !empty($record->foto_npwp_pt)),
                                Infolists\Components\ImageEntry::make('foto_nota')
                                    ->label('Foto Nota')
                                    ->disk('public')
                                    ->square()
                                    ->visible(fn($record) => !empty($record->foto_nota) && is_array($record->foto_nota) && count($record->foto_nota) > 0),
                                Infolists\Components\ImageEntry::make('foto_unit')
                                    ->label('Foto Unit')
                                    ->disk('public')
                                    ->square()
                                    ->visible(fn($record) => !empty($record->foto_unit) && is_array($record->foto_unit) && count($record->foto_unit) > 0),
                            ]),
                    ]),

                // Infolists\Components\Section::make('Foto Unit')
                //     ->schema([
                //         Infolists\Components\ImageEntry::make('foto_unit')
                //             ->label('Foto Unit')
                //             ->disk('public')
                //             ->height(300)
                //             ->square()
                //             ->visible(fn($record) => !empty($record->foto_unit) && is_array($record->foto_unit) && count($record->foto_unit) > 0),
                //     ])
                //     ->columns(2)
                //     ->visible(fn($record) => !empty($record->foto_unit) && is_array($record->foto_unit) && count($record->foto_unit) > 0),
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
            'index' => Pages\ListAsuransis::route('/'),
            'create' => Pages\CreateAsuransi::route('/create'),
            'view' => Pages\ViewAsuransi::route('/{record}'),
            'edit' => Pages\EditAsuransi::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Asuransi';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Asuransi';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'Pending')->count();
    }
}
