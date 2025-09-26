<?php

namespace App\Filament\Resources;


use Filament\Forms;
use App\Models\Unit;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Actions\ExportAction;
use App\Filament\Exports\UnitExporter;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Resources\DataUnitResource\Pages;
use App\Filament\Resources\DataUnitResource\RelationManagers;

class DataUnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    // protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?string $navigationGroup = 'Unit';
    protected static ?string $navigationLabel = 'Data Unit';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('no_rks')->label('No. RKS'),
                Forms\Components\TextInput::make('penyerahan_unit')->label('Penyerahan Unit'),
                Forms\Components\TextInput::make('jenis')->label('Jenis Kendaraan')->required(),
                Forms\Components\TextInput::make('merk')->label('Merk Kendaraan')->required(),
                Forms\Components\TextInput::make('type')->label('Type Kendaraan')->required(),
                Forms\Components\TextInput::make('nopol')->label('No. Polisi')->rules('required', 'unique:data_units,nopol', 'max:10')->required(),
                Forms\Components\TextInput::make('no_rangka')->label('No. Rangka'),
                Forms\Components\TextInput::make('no_mesin')->label('No. Mesin'),
                Forms\Components\TextInput::make('tgl_pajak')->label('Tanggal Pajak'),
                Forms\Components\TextInput::make('regional')->label('Regional'),
                Forms\Components\TextInput::make('warna')->label('Warna Kendaraan'),
                Forms\Components\TextInput::make('tahun')->label('Tahun Kendaraan'),
                Forms\Components\TextInput::make('bpkb')->label('BPKB'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_rks')
                    ->label('No. RKS')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('penyerahan_unit')
                    ->label('Penyerahan Unit')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->format('d-m-Y'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis Kendaraan')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('merk')
                    ->label('Merk Kendaraan')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type Kendaraan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nopol')
                    ->label('No. Polisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_rangka')
                    ->label('No. Rangka')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_mesin')
                    ->label('No. Mesin')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_pajak')
                    ->label('Tanggal Pajak')
                    ->dateTime()
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->format('d-m-Y'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('regional')
                    ->label('Regional')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

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
                    ExportBulkAction::make('export_units')
                        ->label('Export Unit')
                        ->exporter(UnitExporter::class),
                ]),

                // Removed headerActions ExportAction because it is not supported for tables
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Unit'; // singular
    }

    public static function getPluralModelLabel(): string
    {
        return 'Unit'; // tetap singular
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDataUnits::route('/'),
            'create' => Pages\CreateDataUnit::route('/create'),
            'edit' => Pages\EditDataUnit::route('/{record}/edit'),
        ];
    }
}
