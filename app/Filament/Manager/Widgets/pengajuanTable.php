<?php

namespace App\Filament\Manager\Widgets;

use App\Filament\Manager\Resources\PengajuanResource;
use App\Models\Pengajuan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class pengajuanTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(PengajuanResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('no_pengajuan')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->searchable()
                    ->label('Tanggal Pengajuan')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('nama')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('up')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('keterangan')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('keterangan_proses')
                        ->label('Status Proses')
                        ->sortable()
                        ->searchable()
                        ->badge()
                        ->getStateUsing(function ($record) {
                            return match ($record->keterangan_proses) {
                                'CS' => 'Customer Service',
                                'PENGAJUAN FINANCE' => 'Pengajuan Finance',
                                'FINANCE' => 'Input Finance',
                                'OTORISASI' => 'Otorisasi',
                                'DONE' => 'Selesai',
                                default => 'Tidak Diketahui',
                            };
                        })
                        ->color(fn(string $state) => match (true) {
                            str_contains($state, 'Customer Service') => 'gray',
                            str_contains($state, 'Pengajuan Finance') => 'primary',
                            str_contains($state, 'Input Finance') => 'warning',
                            str_contains($state, 'Otorisasi') => 'warning',
                            str_contains($state, 'Selesai') => 'success',
                            default => 'gray',
                        }),
                ])
            ->actions([
                // Tables\Actions\Action::make('open')
                //     ->url(fn (Pengajuan $record): string => PengajuanResource::getUrl('view', ['record' => $record])),
            ]);
    }

}
