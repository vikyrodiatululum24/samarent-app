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
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UnitJualResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UnitJualResource\RelationManagers;

class UnitJualResource extends Resource
{
    protected static ?string $model = UnitJual::class;
    protected static ?string $navigationGroup = 'Unit';
    protected static ?string $navigationLabel = 'Unit Jual';
    protected static ?string $pluralLabel = 'Unit Jual';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_id')
                    ->required()
                    ->label('Unit')
                    ->relationship('unit', 'nopol')
                    ->getOptionLabelFromRecordUsing(function (Unit $unit) {
                        return "{$unit->type} - {$unit->nopol}";
                    })
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('harga_jual')
                    ->prefix('Rp') // Tambahkan Rp di depan input
                    ->numeric() // hanya angka
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('harga_netto')
                    ->prefix('Rp') // Tambahkan Rp di depan input
                    ->numeric() // hanya angka
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->required(),
                Forms\Components\TextArea::make('keterangan')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\FileUpload::make('foto_depan')
                    ->image()
                    ->disk('public')
                    ->directory('unit_jual/foto_depan')
                    ->maxSize(1024)
                    ->resize(50)
                    ->label('Foto Depan')
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_belakang')
                    ->image()
                    ->disk('public')
                    ->directory('unit_jual/foto_belakang')
                    ->maxSize(1024)
                    ->resize(50)
                    ->label('Foto Depan')
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_kiri')
                    ->image()
                    ->disk('public')
                    ->directory('unit_jual/foto_kiri')
                    ->maxSize(1024)
                    ->resize(50)
                    ->label('Foto Depan')
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_kanan')
                    ->image()
                    ->disk('public')
                    ->directory('unit_jual/foto_kanan')
                    ->maxSize(1024)
                    ->resize(50)
                    ->label('Foto Depan')
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_interior')
                    ->image()
                    ->disk('public')
                    ->directory('unit_jual/foto_interior')
                    ->maxSize(1024)
                    ->resize(50)
                    ->label('Foto Depan')
                    ->nullable(),
                Forms\Components\FileUpload::make('foto_odometer')
                    ->image()
                    ->disk('public')
                    ->directory('unit_jual/foto_odometer')
                    ->maxSize(1024)
                    ->resize(50)
                    ->label('Foto Depan')
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
            'edit' => Pages\EditUnitJual::route('/{record}/edit'),
        ];
    }
}
