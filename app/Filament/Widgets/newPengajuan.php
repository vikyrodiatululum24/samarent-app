<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PengajuanResource;
use App\Models\Pengajuan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class newPengajuan extends BaseWidget
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
                    ->date('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('User'),
                Tables\Columns\TextColumn::make('nama')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Nama PIC'),
                Tables\Columns\TextColumn::make('nopol')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('No. Polisi'),
                Tables\Columns\TextColumn::make('jenis')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Jenis Kendaraan'),
                Tables\Columns\TextColumn::make('type')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Type Unit'),
                Tables\Columns\TextColumn::make('service')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Permintaan Service'),
                Tables\Columns\TextColumn::make('project')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Project'),
                Tables\Columns\TextColumn::make('up')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Unit Pelaksana'),
                Tables\Columns\TextColumn::make('keterangan')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('keterangan_proses')
                        ->label('Status Proses')
                        ->sortable()
                        ->searchable()
                        ->badge()
                        ->getStateUsing(function ($record) {
                            return match ($record->keterangan_proses) {
                                'cs' => 'Customer Service',
                                'pengajuan finance' => 'Pengajuan Finance',
                                'finance' => 'Input Finance',
                                'otorisasi' => 'Otorisasi',
                                'done' => 'Selesai',
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
                Tables\Actions\Action::make('open')
                    ->url(fn (Pengajuan $record): string => PengajuanResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
