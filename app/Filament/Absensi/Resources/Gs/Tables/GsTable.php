<?php

namespace App\Filament\Absensi\Resources\Gs\Tables;

use App\Models\Gs;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Filament\Exports\GsExporter;
use Filament\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;

class GsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('driver.user.name')
                    ->label('Driver')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('no_hp')
                    ->label('No. HP Driver')
                    ->searchable(),
                TextColumn::make('unit.nopol')
                    ->label('Unit')
                    ->searchable(),
                TextColumn::make('project')
                    ->label('Project')
                    ->searchable(),
                TextColumn::make('user')
                    ->label('User')
                    ->searchable(),
                TextColumn::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date()
                    ->sortable(),
                TextColumn::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->date()
                    ->sortable(),
                TextColumn::make('driver_pengganti')
                    ->label('Driver Pengganti')
                    ->searchable(),
                TextColumn::make('status_progres')
                    ->label('Status Progres')
                    ->badge()
                    ->color(fn($state) => $state === 'progres' ? 'warning' : ($state === 'selesai' ? 'success' : 'gray'))
                    ->action(
                        \Filament\Actions\Action::make('updateStatusProgres')
                            ->modalHeading('Update Status Progres')
                            ->modalWidth('sm')
                            ->form([
                                \Filament\Forms\Components\Select::make('status_progres')
                                    ->label('Status Progres')
                                    ->options([
                                        'progres' => 'Progres',
                                        'selesai' => 'Selesai',
                                    ])
                                    ->required(),
                            ])
                            ->action(function (Gs $record, array $data): void {
                                $record->update(['status_progres' => $data['status_progres']]);
                            })
                    ),
                TextColumn::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->color(fn($state) => $state === 'belum bayar' ? 'warning' : ($state === 'terbayar' ? 'success' : 'gray'))
                    ->action(
                        \Filament\Actions\Action::make('updateStatusPembayaran')
                            ->modalHeading('Update Status Pembayaran')
                            ->modalWidth('sm')
                            ->form([
                                \Filament\Forms\Components\Select::make('status_pembayaran')
                                    ->label('Status Pembayaran')
                                    ->options([
                                        'belum bayar' => 'Belum Dibayar',
                                        'terbayar' => 'Terbayar',
                                    ])
                                    ->required(),
                            ])
                            ->action(function (Gs $record, array $data): void {
                                $record->update(['status_pembayaran' => $data['status_pembayaran']]);
                            })
                    ),
            ])
            ->filters([
                SelectFilter::make('month')
                    ->label('Filter Bulan & Tahun')
                    ->options(function () {
                        $months = [];
                        $gss = Gs::selectRaw('DISTINCT YEAR(tanggal_mulai) as year, MONTH(tanggal_mulai) as month')
                            ->whereNotNull('tanggal_mulai')
                            ->orderBy('year', 'desc')
                            ->orderBy('month', 'desc')
                            ->get();

                        foreach ($gss as $gs) {
                            $key = $gs->year . '-' . str_pad($gs->month, 2, '0', STR_PAD_LEFT);
                            $label = Carbon::createFromDate($gs->year, $gs->month, 1)->format('F Y');
                            $months[$key] = $label;
                        }

                        return $months;
                    })
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            [$year, $month] = explode('-', $data['value']);
                            $query->whereYear('tanggal_mulai', $year)->whereMonth('tanggal_mulai', $month);
                        }
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()->exporter(GsExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

