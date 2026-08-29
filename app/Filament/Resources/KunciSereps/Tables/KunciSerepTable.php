<?php

namespace App\Filament\Resources\KunciSereps\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\KunciSereps\Schemas\KunciSerepForm;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;

class KunciSerepTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit.nopol')
                    ->label('Unit')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_kunci')
                    ->label('No. Kunci')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_kunci')
                    ->label('Status Kunci')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->color(fn (string $state): string => match ($state) {
                        'tersedia' => 'success',
                        'diambil' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->label('Tgl Masuk')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_keluar')
                    ->label('Tgl Keluar')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('diambil_oleh')
                    ->label('Diambil Oleh')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('lokasi')
                    ->label('Filter Lokasi')
                    ->options(KunciSerepForm::getLokasiOptions()),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($livewire) {
                        $query = $livewire->getFilteredTableQuery();
                        $ids = $query->pluck('id')->toArray();
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\KunciSerepExport($ids),
                            'kunci_serep.xlsx'
                        );
                    }),
            ]);
    }
}
