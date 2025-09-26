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
    protected static ?string $pluralLabel = 'Unit Jual';
    protected static ?string $navigationLabel = 'Unit Jual';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_id')
                    ->required()
                    ->relationship('unit', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Select a Unit')
                    ->label('Pilih Unit')
                    ->getOptionLabelFromRecordUsing(function (Unit $unit) {
                        return "{$unit->type} - {$unit->nopol}";
                    }),
                Forms\Components\TextInput::make('harga_jual')
                    ->required()
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric(),
                Forms\Components\TextInput::make('harga_netto')
                    ->required()
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric(),
                Forms\Components\TextArea::make('keterangan')
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_depan')
                    ->label('Foto Depan')
                    ->image()
                    ->resize(50)
                    ->maxWidth(1024)
                    ->optimize('webp')
                    ->maxSize(2048) // Maksimal 2MB
                    ->disk('public')
                    ->directory('unit_jual/foto_depan')
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_belakang')
                    ->label('Foto Belakang')
                    ->image()
                    ->resize(50)
                    ->maxWidth(1024)
                    ->optimize('webp')
                    ->maxSize(2048) // Maksimal 2MB
                    ->disk('public')
                    ->directory('unit_jual/foto_belakang')
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_kiri')
                    ->label('Foto Kiri')
                    ->image()
                    ->resize(50)
                    ->maxWidth(1024)
                    ->optimize('webp')
                    ->maxSize(2048) // Maksimal 2MB
                    ->disk('public')
                    ->directory('unit_jual/foto_kiri')
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_kanan')
                    ->label('Foto Kanan')
                    ->image()
                    ->resize(50)
                    ->maxWidth(1024)
                    ->optimize('webp')
                    ->maxSize(2048) // Maksimal 2MB
                    ->disk('public')
                    ->directory('unit_jual/foto_kanan')
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_interior')
                    ->label('Foto Interior')
                    ->image()
                    ->resize(50)
                    ->maxWidth(1024)
                    ->optimize('webp')
                    ->maxSize(2048) // Maksimal 2MB
                    ->disk('public')
                    ->directory('unit_jual/foto_interior')
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_odometer')
                    ->label('Foto Odometer')
                    ->image()
                    ->resize(50)
                    ->maxWidth(1024)
                    ->optimize('webp')
                    ->maxSize(2048) // Maksimal 2MB
                    ->disk('public')
                    ->directory('unit_jual/foto_odometer')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_jual')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_netto')
                    ->numeric()
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
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Informasi Harga')
                    ->schema([
                        Infolists\Components\TextEntry::make('harga_jual')
                            ->label('Harga Jual')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('harga_netto')
                            ->label('Harga Netto')
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
            'index' => Pages\ListUnitJuals::route('/'),
            'create' => Pages\CreateUnitJual::route('/create'),
            'view' => Pages\ViewUnitJual::route('/{record}'),
            'edit' => Pages\EditUnitJual::route('/{record}/edit')
        ];
    }
}
