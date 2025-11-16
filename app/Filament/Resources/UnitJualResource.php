<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Unit;
use Filament\Tables;
use App\Models\UnitJual;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UnitJualResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UnitJualResource\RelationManagers;

class UnitJualResource extends Resource
{
    protected static ?string $model = UnitJual::class;
    protected static ?string $navigationGroup = 'Unit';
    protected static ?string $pluralLabel = 'Jual Unit';
    protected static ?string $navigationLabel = 'Jual Unit';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_id')
                    ->required()
                    ->relationship('unit', 'nopol')
                    ->searchable()
                    ->preload()
                    ->placeholder('Select a Unit')
                    ->label('Pilih Unit')
                    ->getOptionLabelFromRecordUsing(function (Unit $unit) {
                        return "{$unit->type} - {$unit->nopol}";
                    }),
                    Forms\Components\TextInput::make('odometer')
                        ->label('Odometer (km)')
                        ->required()
                        ->suffix('km')
                        ->numeric(),
                Forms\Components\TextInput::make('harga_jual')
                    ->label('Open Price')
                    ->required()
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric(),
                Forms\Components\TextInput::make('harga_netto')
                    ->label('Harga Target')
                    ->required()
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric(),
                Forms\Components\Select::make('rateBody')
                    ->label('Rate Body')
                    ->options([
                        10 => '10%',
                        20 => '20%',
                        30 => '30%',
                        40 => '40%',
                        50 => '50%',
                        60 => '60%',
                        70 => '70%',
                        80 => '80%',
                        90 => '90%',
                        100 => '100%',
                    ])
                    ->required(),
                Forms\Components\Select::make('rateInterior')
                    ->label('Rate Interior')
                    ->options([
                        10 => '10%',
                        20 => '20%',
                        30 => '30%',
                        40 => '40%',
                        50 => '50%',
                        60 => '60%',
                        70 => '70%',
                        80 => '80%',
                        90 => '90%',
                        100 => '100%',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_depan')
                    ->label('Foto Depan')
                    ->image()
                    ->resize(50)
                    ->maxWidth(1024)
                    ->optimize('webp')
                    ->disk('public')
                    ->directory('unit_jual/foto_depan')
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_belakang')
                    ->label('Foto Belakang')
                    ->image()
                    ->resize(50)
                    ->maxWidth(1024)
                    ->optimize('webp')
                    ->disk('public')
                    ->directory('unit_jual/foto_belakang')
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_kiri')
                    ->label('Foto Kiri')
                    ->image()
                    ->resize(50)
                    ->maxWidth(1024)
                    ->optimize('webp')
                    ->disk('public')
                    ->directory('unit_jual/foto_kiri')
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_kanan')
                    ->label('Foto Kanan')
                    ->image()
                    ->resize(50)
                    ->maxWidth(1024)
                    ->optimize('webp')
                    ->disk('public')
                    ->directory('unit_jual/foto_kanan')
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_interior')
                    ->label('Foto Interior')
                    ->image()
                    ->resize(50)
                    ->maxWidth(1024)
                    ->optimize('webp')
                    ->disk('public')
                    ->directory('unit_jual/foto_interior')
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_odometer')
                    ->label('Foto Odometer')
                    ->image()
                    ->resize(50)
                    ->maxWidth(1024)
                    ->optimize('webp')
                    ->disk('public')
                    ->directory('unit_jual/foto_odometer')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit.type')
                    ->label('Unit')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        return $record->unit ? "{$record->unit->type}" : '-';
                    }),
                Tables\Columns\TextColumn::make('unit.nopol')
                    ->label('No. Polisi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_jual')
                    ->label('Open Price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_netto')
                    ->label('Harga Target')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rateBody')
                    ->label('Rate Body')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rateInterior')
                    ->label('Rate Interior')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordUrl(
                fn($record): string => static::getUrl('view', ['record' => $record]),
            )
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            ->components([
                Infolists\Components\Section::make('Detail Kendaraan')
                    ->schema([
                        Infolists\Components\TextEntry::make('unit.type')
                            ->label('Unit')
                            ->formatStateUsing(function ($record) {
                                return $record->unit ? "{$record->unit->type} - {$record->unit->nopol}" : '-';
                            }),
                        Infolists\Components\TextEntry::make('unit.no_rangka')
                            ->label('No Rangka'),
                        Infolists\Components\TextEntry::make('unit.no_mesin')
                            ->label('No Mesin'),
                        Infolists\Components\TextEntry::make('unit.tgl_pajak')
                            ->label('Tgl Pajak'),
                        Infolists\Components\TextEntry::make('unit.regional')
                            ->label('Regional'),
                        Infolists\Components\TextEntry::make('unit.merk')
                            ->label('Merk'),
                        Infolists\Components\TextEntry::make('warna')
                            ->label('Warna'),
                        Infolists\Components\TextEntry::make('tahun')
                            ->label('Tahun'),
                        Infolists\Components\TextEntry::make('bpkb')
                            ->label('BPKB'),
                        Infolists\Components\TextEntry::make('odometer')
                            ->label('Odometer')
                            ->suffix('km'),
                        Infolists\Components\TextEntry::make('rateBody')
                            ->label('Rate Body')
                            ->suffix('%'),
                        Infolists\Components\TextEntry::make('rateInterior')
                            ->label('Rate Interior')
                            ->suffix('%'),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Informasi Harga')
                    ->schema([
                        Infolists\Components\TextEntry::make('harga_jual')
                            ->label('Open Price')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('harga_netto')
                            ->label('Harga Target')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Infolists\Components\Grid::make(3)
                    ->schema([
                        Infolists\Components\ImageEntry::make('foto_depan')
                            ->label('Foto Depan')
                            ->disk('public')
                            ->height(200),
                        Infolists\Components\ImageEntry::make('foto_belakang')
                            ->label('Foto Belakang')
                            ->disk('public')
                            ->height(200),
                        Infolists\Components\ImageEntry::make('foto_kiri')
                            ->label('Foto Kiri')
                            ->disk('public')
                            ->height(200),
                        Infolists\Components\ImageEntry::make('foto_kanan')
                            ->label('Foto Kanan')
                            ->disk('public')
                            ->height(200),
                        Infolists\Components\ImageEntry::make('foto_interior')
                            ->label('Foto Interior')
                            ->disk('public')
                            ->height(200),
                        Infolists\Components\ImageEntry::make('foto_odometer')
                            ->label('Foto Odometer')
                            ->disk('public')
                            ->height(200),
                    ]),
                Infolists\Components\Section::make('Data Penawar')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('penawars')
                            ->label('')
                            ->schema([
                                Infolists\Components\Grid::make([
                                    'sm' => 2,
                                    'md' => 3,
                                    'lg' => 6
                                ])
                                    ->schema([
                                        Infolists\Components\TextEntry::make('nama')
                                            ->label('Nama Penawar')
                                            ->weight('bold'),
                                        Infolists\Components\TextEntry::make('no_wa')
                                            ->label('No. WhatsApp'),
                                        Infolists\Components\TextEntry::make('harga_penawaran')
                                            ->label('Harga Penawaran')
                                            ->money('IDR')
                                            ->color('success')
                                            ->weight('bold'),
                                        Infolists\Components\TextEntry::make('down_payment')
                                            ->label('Down Payment')
                                            ->money('IDR')
                                            ->color('warning')
                                            ->weight('bold'),
                                        Infolists\Components\TextEntry::make('catatan')
                                            ->label('Catatan'),
                                    ])
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->email === 'centralakun@samarent.com';
    }

    public static function canView($record): bool
    {
        return auth()->user()?->email === 'centralakun@samarent.com';
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->email === 'centralakun@samarent.com';
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->email === 'centralakun@samarent.com';
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->email === 'centralakun@samarent.com';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnitJuals::route('/'),
            'create' => Pages\CreateUnitJual::route('/create'),
            'view' => Pages\ViewUnitJual::route('/{record}'),
            'edit' => Pages\EditUnitJual::route('/{record}/edit')
        ];
    }
}
