<?php

namespace App\Filament\Manager\Resources\DriverResource\RelationManagers;

use App\Filament\Manager\Resources\DriverAttendenceResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class DriverAttendenceRelationManager extends RelationManager
{
    protected static string $relationship = 'driverAttendences';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('date'),
                Tables\Columns\TextColumn::make('project.name')->label('Project'),
                Tables\Columns\TextColumn::make('endUser.name')->label('End User'),
                Tables\Columns\TextColumn::make('unit.type')->label('Unit'),
                Tables\Columns\TextColumn::make('start_km'),
                Tables\Columns\TextColumn::make('location_in')->label('Lokasi Masuk'),
                Tables\Columns\TextColumn::make('time_in'),
                Tables\Columns\ImageColumn::make('photo_in')
                    ->disk('public')
                    ->square()
                    ->label('Foto Masuk')
                    ->getStateUsing(fn ($record) => str_replace('storage/', '', $record->photo_in)),
                Tables\Columns\TextColumn::make('time_chek')
                    ->label('Jam Cek')
                    ->getStateUsing(function ($record) {
                        $check = $record->checks()->latest()->first();
                        return $check ? $check->created_at->format('H:i:s') : '-';
                    }),
                Tables\Columns\TextColumn::make('location_chek')
                    ->label('Lokasi Cek')
                    ->getStateUsing(function ($record) {
                        $check = $record->checks()->latest()->first();
                        return $check ? $check->location : '-';
                    }),
                Tables\Columns\ImageColumn::make('photo_chek')
                    ->label('Foto Cek')
                    ->getStateUsing(function ($record) {
                        $check = $record->checks()->latest()->first();
                        return $check ? str_replace('storage/', '', $check->photo) : null;
                    }),
                Tables\Columns\TextColumn::make('end_km'),
                Tables\Columns\TextColumn::make('time_out'),
                Tables\Columns\TextColumn::make('location_out')->label('Lokasi Keluar'),
                Tables\Columns\ImageColumn::make('photo_out')
                    ->disk('public')
                    ->square()
                    ->label('Foto Keluar')
                    ->getStateUsing(fn ($record) => str_replace('storage/', '', $record->photo_out)),
                Tables\Columns\TextColumn::make('note')
                    ->limit(50)
                    ->wrap()
                    ->label('Catatan'),
                Tables\Columns\BooleanColumn::make('is_complete')->label('Approved'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('month')
                    ->options(function () {
                            $months = [];
                            for ($i = 0; $i < 12; $i++) {
                                $date = Carbon::now()->subMonths($i);
                                $months[$date->format('Y-m')] = $date->translatedFormat('F Y');
                            }
                            return array_reverse($months, true); // urut dari lama ke baru
                        })
                        ->default(date('Y-m'))
                        ->query(function (Builder $query, array $data): Builder {
                            // Get the selected value
                            $selectedMonth = $data['value'] ?? date('Y-m');

                            return $selectedMonth ? $query->whereMonth('date', substr($selectedMonth, 5, 2))
                                ->whereYear('date', substr($selectedMonth, 0, 4)) : $query;
                        })
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                ActionGroup::make([
                    Action::make('priviewPdf')
                        ->label('Preview PDF')
                        ->icon('heroicon-o-eye')
                        ->action(function ($livewire) {
                            $filters = $livewire->tableFilters;
                            $month = $filters['month']['value'] ?? null;

                            if (!$month) {
                                Notification::make()
                                    ->title('Filter belum dipilih')
                                    ->body('Silakan pilih bulan terlebih dahulu sebelum melihat pratinjau PDF.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $driverId = $this->ownerRecord->id;
                            $url = route('preview-laporan-absensi', [
                                'driver_id' => $driverId,
                                'month' => $month,
                            ]);

                            $this->js("window.open('{$url}', '_blank')");
                        }),
                    Action::make('printPdf')
                        ->label('Print PDF')
                        ->icon('heroicon-o-printer')
                        ->badge(function($livewire) {
                            $filters = $livewire->tableFilters;
                            $cetak = \App\Models\Cetak::where('driver_id', $this->ownerRecord->id)->where('periode', $filters['month']['value'] ?? null)->first();
                            return $cetak ? 'sudah di print' : null;
                        })
                        ->action(function ($livewire) {
                            $filters = $livewire->tableFilters;
                            $month = $filters['month']['value'] ?? null;

                            if (!$month) {
                                Notification::make()
                                    ->title('Filter belum dipilih')
                                    ->body('Silakan pilih bulan terlebih dahulu sebelum mencetak PDF.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $driverId = $this->ownerRecord->id;
                            $url = route('laporan-absensi', [
                                'driver_id' => $driverId,
                                'month' => $month,
                            ]);

                            $this->js("window.open('{$url}', '_blank')");
                        }),

                    Action::make('exportExcel')
                        ->label('Export to Excel')
                        ->icon('heroicon-o-document-plus')
                        ->action(function ($livewire) {
                            $filters = $livewire->tableFilters;
                            $month = $filters['month']['value'] ?? null;

                            if (!$month) {
                                Notification::make()
                                    ->title('Filter belum dipilih')
                                    ->body('Silakan pilih bulan terlebih dahulu sebelum mengekspor ke Excel.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $driverId = $this->ownerRecord->id;
                            $url = route('export-absensi-excel', [
                                'driver_id' => $driverId,
                                'month' => $month,
                            ]);

                            $this->js("window.open('{$url}', '_blank')");
                        }),
                ])
                ->label('Export')
                ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->actions([
                Action::make('view')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => DriverAttendenceResource::getUrl('view', ['record' => $record])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
