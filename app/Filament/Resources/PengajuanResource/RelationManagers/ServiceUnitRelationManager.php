<?php

namespace App\Filament\Resources\PengajuanResource\RelationManagers;

use App\Models\DataUnit;
use App\Models\Unit;
use Filament\Forms;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceUnitRelationManager extends RelationManager
{
    protected static string $relationship = 'service_unit';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('unit_id')
                    ->label('Unit')
                    ->relationship('unit', 'nopol') // Relasi dari model ini ke model Unit
                    ->getOptionLabelFromRecordUsing(function (Unit $unit) {
                        return "{$unit->type} - {$unit->nopol}";
                    })
                    ->searchable()
                    ->preload() // Optional, preload semua data untuk menghindari query saat ketik
                    ->required(),
                Forms\Components\TextInput::make('odometer')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('service')
                    ->label('Jenis Permintaan Service')
                    ->required(),
                Forms\Components\FileUpload::make('foto_unit')
                    ->label('Foto Unit')
                    ->image()
                    ->disk('public')
                    ->directory('foto_unit')
                    ->nullable('required'),
                Forms\Components\FileUpload::make('foto_odometer')
                    ->label('Foto Odometer')
                    ->image()
                    ->disk('public')
                    ->directory('foto_odometer')
                    ->nullable('required'),
                Forms\Components\FileUpload::make('foto_kondisi')
                    ->label('Foto Kondisi')
                    ->image()
                    ->multiple()
                    ->maxFiles(3)
                    ->disk('public')
                    ->directory('foto_kondisi')
                    ->nullable('required'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('unit_nopol')
                    ->label('Unit')
                    ->getStateUsing(function ($record) {
                        if (!$record->unit) {
                            return 'Unit tidak ditemukan';
                        }
                        return "{$record->unit->type} - {$record->unit->nopol}";
                    }),
                Tables\Columns\TextColumn::make('odometer'),
                Tables\Columns\TextColumn::make('service')->label('Jenis Service'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
