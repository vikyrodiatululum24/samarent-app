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
                    ->sortable()
                    ->formatStateUsing(fn (?string $state): string => strtoupper($state ?? '')),
                Tables\Columns\TextColumn::make('no_kunci')
                    ->label('No. Kunci')
                    ->searchable()
                    ->formatStateUsing(fn (?string $state): string => strtoupper($state ?? '')),
                Tables\Columns\TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->searchable()
                    ->formatStateUsing(fn (?string $state): string => strtoupper($state ?? '')),
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
                    ->searchable()
                    ->formatStateUsing(fn (?string $state): string => ucwords(strtolower($state ?? ''))),
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
                Action::make('import_excel_custom')
                    ->label('Import Excel')
                    ->icon('heroicon-o-document-arrow-up')
                    ->color('primary')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('file')
                            ->label('Pilih File Excel/CSV')
                            ->disk('local')
                            ->directory('imports')
                            ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $filePath = \Illuminate\Support\Facades\Storage::disk('local')->path($data['file']);
                        $import = new \App\Imports\KunciSerepImport();
                        
                        \Maatwebsite\Excel\Facades\Excel::import($import, $filePath);

                        $successCount = $import->successCount;
                        $failuresCount = count($import->failures());

                        $body = "Import selesai. {$successCount} data berhasil diimpor.";
                        if ($failuresCount > 0) {
                            $body .= " ⚠️ Terdapat {$failuresCount} baris gagal diimpor (Nopol tidak ditemukan/format salah).";
                            \Filament\Notifications\Notification::make()
                                ->warning()
                                ->title('Import Selesai dengan Peringatan')
                                ->body($body)
                                ->persistent()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Import Berhasil')
                                ->body($body)
                                ->send();
                        }
                    })
                    ->extraModalFooterActions([
                        \Filament\Actions\Action::make('download_sample')
                            ->label('Download Sampel Format')
                            ->color('success')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->action(function () {
                                return \Maatwebsite\Excel\Facades\Excel::download(
                                    new \App\Exports\KunciSerepSampleExport(),
                                    'format_import_kunci_serep.xlsx'
                                );
                            }),
                    ]),
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
