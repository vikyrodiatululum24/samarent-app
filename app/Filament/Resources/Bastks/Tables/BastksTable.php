<?php

namespace App\Filament\Resources\Bastks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BastksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode')
                    ->label('Kode BASTK')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('unit.nopol')
                    ->label('Unit')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->unit ? "{$record->unit->type} - {$record->unit->nopol}" : '-'),
                TextColumn::make('kepada')
                    ->label('Kepada')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tgl_serah')
                    ->label('Tgl Serah')
                    ->date()
                    ->sortable(),
                TextColumn::make('tgl_kembali')
                    ->label('Tgl Kembali')
                    ->date()
                    ->sortable(),
                TextColumn::make('nama_penyerah')
                    ->label('Penyerah')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_penerima')
                    ->label('Penerima')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
