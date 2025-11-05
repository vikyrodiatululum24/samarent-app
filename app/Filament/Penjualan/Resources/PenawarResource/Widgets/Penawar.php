<?php

namespace App\Filament\Penjualan\Resources\PenawarResource\Widgets;

use App\Models\Penawar as PenawarModel;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;

class Penawar extends BaseWidget
{
    protected static ?string $heading = 'Daftar Penawar';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PenawarModel::query()->with(['unitJual.unit'])->latest()
            )
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Penawar')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('no_wa')
                    ->label('No. WhatsApp')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone'),

                TextColumn::make('unitJual.unit.nopol')
                    ->label('No. Polisi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->default('-'),

                TextColumn::make('unitJual.unit.merk')
                    ->label('Merk')
                    ->searchable()
                    ->default('-'),

                TextColumn::make('unitJual.unit.type')
                    ->label('Type')
                    ->searchable()
                    ->default('-'),

                TextColumn::make('unitJual.unit.tahun')
                    ->label('Tahun')
                    ->sortable()
                    ->default('-'),

                TextColumn::make('unitJual.harga_jual')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('harga_penawaran')
                    ->label('Harga Penawaran')
                    ->money('IDR')
                    ->sortable()
                    ->color('warning'),

                TextColumn::make('down_payment')
                    ->label('Down Payment')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('catatan')
                    ->label('Catatan')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->default('-'),

                TextColumn::make('created_at')
                    ->label('Tanggal Penawaran')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('unitJual.unit.nopol')
                    ->label('No. Polisi')
                    ->relationship('unitJual.unit', 'nopol')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
