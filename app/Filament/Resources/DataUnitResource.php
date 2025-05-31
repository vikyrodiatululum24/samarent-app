<?php

namespace App\Filament\Resources;


use Filament\Forms;
use App\Models\Unit;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DataUnitResource\Pages;
use App\Filament\Resources\DataUnitResource\RelationManagers;

class DataUnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    // protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    // protected static ?string $navigationGroup = 'Unit';
    protected static ?string $navigationLabel = 'Data Unit';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('jenis')
                    ->label('Jenis Kendaraan')
                    ->required(),
                Forms\Components\TextInput::make('merk')
                    ->label('Merk Kendaraan')
                    ->required(),
                Forms\Components\TextInput::make('type')
                    ->label('Type Kendaraan')
                    ->required(),
                Forms\Components\TextInput::make('nopol')
                    ->label('No. Polisi')
                    ->rules('required', 'unique:data_units,nopol', 'max:10'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis Kendaraan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('merk')
                    ->label('Merk Kendaraan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type Kendaraan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nopol')
                    ->label('No. Polisi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_pengajuan')
                    ->label('Total Pengajuan')
                    ->getStateUsing(function ($record) {
                        return $record->serviceUnit
                            ->filter(fn($su) => $su->pengajuan !== null)
                            ->count();
                    }),

                Tables\Columns\IconColumn::make('pengajuan_bulan_ini')
                    ->label('Pengajuan Bulan Ini')
                    ->getStateUsing(function ($record) {
                        $startOfMonth = request()->get('start_date') ? Carbon::parse(request()->get('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
                        $endOfMonth = request()->get('end_date') ? Carbon::parse(request()->get('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

                        return $record->serviceUnit
                            ->filter(function ($su) use ($startOfMonth, $endOfMonth) {
                                return $su->pengajuan &&
                                    $su->pengajuan->created_at->between($startOfMonth, $endOfMonth);
                            })
                            ->count() > 0;
                    })
                    ->boolean(),
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
            'index' => Pages\ListDataUnits::route('/'),
            'create' => Pages\CreateDataUnit::route('/create'),
            'edit' => Pages\EditDataUnit::route('/{record}/edit'),
        ];
    }
}
