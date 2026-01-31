<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BengkelResource\Pages;
use App\Filament\Resources\BengkelResource\RelationManagers;
use App\Models\Bengkel;
use App\Models\Wilayah;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BengkelResource extends Resource
{
    protected static ?string $model = Bengkel::class;

    protected static ?string $navigationLabel = 'Bengkel';

    protected static ?string $modelLabel = 'Bengkel';

    protected static ?string $pluralModelLabel = 'Bengkel';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        // Hide dari manager panel
        return Filament::getCurrentPanel()?->getId() !== 'manager';
    }

    public static function canViewAny(): bool
    {
        // Prevent access dari manager panel
        if (Filament::getCurrentPanel()?->getId() === 'manager') {
            return false;
        }

        return parent::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Bengkel')
                    ->description('Data umum bengkel')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Bengkel')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->placeholder('Masukkan nama bengkel'),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Masukkan keterangan atau deskripsi bengkel'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Alamat Lengkap')
                    ->description('Pilih alamat secara bertahap dari provinsi hingga desa')
                    ->schema([
                        Forms\Components\Select::make('provinsi')
                            ->label('Provinsi')
                            ->searchable()
                            ->required()
                            ->options(function () {
                                return Wilayah::where('level', 'provinsi')
                                    ->orderBy('nama')
                                    ->pluck('nama', 'nama');
                            })
                            ->reactive()
                            ->afterStateUpdated(function (Set $set) {
                                $set('kab_kota', null);
                                $set('kecamatan', null);
                                $set('desa', null);
                            })
                            ->placeholder('Pilih provinsi'),

                        Forms\Components\Select::make('kab_kota')
                            ->label('Kabupaten/Kota')
                            ->searchable()
                            ->required()
                            ->options(function (Get $get) {
                                $provinsiNama = $get('provinsi');
                                if (!$provinsiNama) {
                                    return [];
                                }
                                $provinsi = Wilayah::where('level', 'provinsi')
                                    ->where('nama', $provinsiNama)
                                    ->first();

                                if (!$provinsi) {
                                    return [];
                                }

                                return Wilayah::where('level', 'kabupaten')
                                    ->where('parent_id', $provinsi->id)
                                    ->orderBy('nama')
                                    ->pluck('nama', 'nama');
                            })
                            ->reactive()
                            ->afterStateUpdated(function (Set $set) {
                                $set('kecamatan', null);
                                $set('desa', null);
                            })
                            ->disabled(fn (Get $get): bool => !$get('provinsi'))
                            ->placeholder('Pilih kabupaten/kota'),

                        Forms\Components\Select::make('kecamatan')
                            ->label('Kecamatan')
                            ->searchable()
                            ->required()
                            ->options(function (Get $get) {
                                $kabKotaNama = $get('kab_kota');
                                if (!$kabKotaNama) {
                                    return [];
                                }
                                $kabKota = Wilayah::where('level', 'kabupaten')
                                    ->where('nama', $kabKotaNama)
                                    ->first();

                                if (!$kabKota) {
                                    return [];
                                }

                                return Wilayah::where('level', 'kecamatan')
                                    ->where('parent_id', $kabKota->id)
                                    ->orderBy('nama')
                                    ->pluck('nama', 'nama');
                            })
                            ->reactive()
                            ->afterStateUpdated(function (Set $set) {
                                $set('desa', null);
                            })
                            ->disabled(fn (Get $get): bool => !$get('kab_kota'))
                            ->placeholder('Pilih kecamatan'),

                        Forms\Components\Select::make('desa')
                            ->label('Desa/Kelurahan')
                            ->searchable()
                            ->required()
                            ->options(function (Get $get) {
                                $kecamatanNama = $get('kecamatan');
                                if (!$kecamatanNama) {
                                    return [];
                                }
                                $kecamatan = Wilayah::where('level', 'kecamatan')
                                    ->where('nama', $kecamatanNama)
                                    ->first();

                                if (!$kecamatan) {
                                    return [];
                                }

                                return Wilayah::where('level', 'desa')
                                    ->where('parent_id', $kecamatan->id)
                                    ->orderBy('nama')
                                    ->pluck('nama', 'nama');
                            })
                            ->disabled(fn (Get $get): bool => !$get('kecamatan'))
                            ->placeholder('Pilih desa/kelurahan'),

                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat Detail')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull()
                            ->placeholder('Masukkan alamat lengkap (nama jalan, nomor, RT/RW, dll)'),

                        Forms\Components\TextInput::make('g_maps')
                            ->label('Link Google Maps')
                            ->url()
                            ->prefix('https://')
                            ->suffixIcon('heroicon-m-map-pin')
                            ->columnSpanFull()
                            ->placeholder('https://maps.google.com/...'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Kontak Bengkel')
                    ->description('Tambahkan kontak person bengkel')
                    ->schema([
                        Forms\Components\Repeater::make('kontakBengkels')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Kontak')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama kontak'),

                                Forms\Components\TextInput::make('no_telp')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('08xxxxxxxxxx'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Tambah Kontak')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['nama'] ?? null)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Bengkel')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('provinsi')
                    ->label('Provinsi')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('kab_kota')
                    ->label('Kab/Kota')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('alamat')
                    ->label('Alamat')
                    ->searchable()
                    ->limit(50)
                    ->toggleable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('kontak_bengkels_count')
                    ->label('Jumlah Kontak')
                    ->counts('kontakBengkels')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('g_maps')
                    ->label('Google Maps')
                    ->url(fn (Bengkel $record): ?string => $record->g_maps)
                    ->openUrlInNewTab()
                    ->placeholder('-')
                    ->limit(30)
                    ->tooltip(fn (Bengkel $record): ?string => $record->g_maps)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provinsi')
                    ->label('Provinsi')
                    ->options(function () {
                        return Wilayah::where('level', 'provinsi')
                            ->orderBy('nama')
                            ->pluck('nama', 'nama');
                    })
                    ->searchable(),

                Tables\Filters\SelectFilter::make('kab_kota')
                    ->label('Kabupaten/Kota')
                    ->options(function () {
                        return Bengkel::query()
                            ->whereNotNull('kab_kota')
                            ->distinct()
                            ->orderBy('kab_kota')
                            ->pluck('kab_kota', 'kab_kota');
                    })
                    ->searchable(),

                Tables\Filters\SelectFilter::make('kecamatan')
                    ->label('Kecamatan')
                    ->options(function () {
                        return Bengkel::query()
                            ->whereNotNull('kecamatan')
                            ->distinct()
                            ->orderBy('kecamatan')
                            ->pluck('kecamatan', 'kecamatan');
                    })
                    ->searchable(),

                Tables\Filters\SelectFilter::make('desa')
                    ->label('Desa/Kelurahan')
                    ->options(function () {
                        return Bengkel::query()
                            ->whereNotNull('desa')
                            ->distinct()
                            ->orderBy('desa')
                            ->pluck('desa', 'desa');
                    })
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('open_maps')
                    ->label('Buka Maps')
                    ->icon('heroicon-o-map-pin')
                    ->color('success')
                    ->url(fn (Bengkel $record): ?string => $record->g_maps)
                    ->openUrlInNewTab()
                    ->visible(fn (Bengkel $record): bool => !empty($record->g_maps)),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBengkels::route('/'),
            'create' => Pages\CreateBengkel::route('/create'),
            'view' => Pages\ViewBengkel::route('/{record}'),
            'edit' => Pages\EditBengkel::route('/{record}/edit'),
        ];
    }
}
