<?php

namespace App\Filament\Penjualan\Resources;

use App\Filament\Penjualan\Resources\OpenHouseResource\Pages;
use App\Filament\Penjualan\Resources\OpenHouseResource\RelationManagers;
use App\Models\OpenHouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OpenHouseResource extends Resource
{
    protected static ?string $model = OpenHouse::class;

    protected static ?string $pluralLabel = 'Open House Events';

    protected static ?string $navigationLabel = 'Open House Events';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_event')
                    ->label('Nama Event')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: Open House Akhir Tahun'),

                Forms\Components\DatePicker::make('tanggal_event')
                    ->label('Tanggal Event')
                    ->required(),

                Forms\Components\Textarea::make('lokasi_event')
                    ->label('Lokasi Event')
                    ->required()
                    ->placeholder('Contoh: Jl. Merdeka No. 123, Jakarta')
                    ->maxLength(255),

                Forms\Components\Textarea::make('deskripsi_event')
                    ->label('Deskripsi Event')
                    ->placeholder('Deskripsi singkat mengenai event')
                    ->maxLength(65535),

                Forms\Components\TimePicker::make('waktu_mulai')
                    ->label('Waktu Mulai')
                    ->required(),

                Forms\Components\TimePicker::make('waktu_selesai')
                    ->label('Waktu Selesai')
                    ->required(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktifkan Event')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_event')
                    ->label('Nama Event')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_event')
                    ->label('Tanggal Event')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('lokasi_event')
                    ->label('Lokasi Event')
                    ->limit(50)
                    ->tooltip(fn (OpenHouse $record): string => $record->lokasi_event)
                    ->sortable(),

                Tables\Columns\TextColumn::make('waktu_mulai')
                    ->label('Waktu Mulai')
                    ->time()
                    ->sortable(),

                Tables\Columns\TextColumn::make('waktu_selesai')
                    ->label('Waktu Selesai')
                    ->time()
                    ->sortable(),

                Tables\Columns\BooleanColumn::make('is_active')
                    ->label('Status')
                    ->sortable(),
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
            'index' => Pages\ListOpenHouses::route('/'),
            'create' => Pages\CreateOpenHouse::route('/create'),
            'edit' => Pages\EditOpenHouse::route('/{record}/edit'),
        ];
    }
}
