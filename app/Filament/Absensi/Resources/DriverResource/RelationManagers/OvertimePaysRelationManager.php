<?php

namespace App\Filament\Absensi\Resources\DriverResource\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class OvertimePaysRelationManager extends RelationManager
{
    protected static string $relationship = 'overtimePay';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\DatePicker::make('tanggal')->label('Tanggal')->required(),
            Forms\Components\TextInput::make('hari')->label('Hari')->required(),
            Forms\Components\Select::make('shift')
                ->label('Shift')
                ->options([
                    'Weekday' => 'Weekday',
                    'Holiday' => 'Holiday',
                ]),
            Forms\Components\TimePicker::make('from_time')->label('Dari Jam')->required(),
            Forms\Components\TimePicker::make('to_time')->label('Sampai Jam')->required(),
            Forms\Components\TextInput::make('worked_hours')->label('Jam Kerja')->numeric()->minValue(0),
            Forms\Components\TextInput::make('normal_hours')->label('Jam Normal')->numeric()->minValue(0),
            Forms\Components\TextInput::make('calculated_ot_hours')->label('Total Jam OT')->numeric()->minValue(0),
            Forms\Components\TextInput::make('amount_per_hour')->label('Amount/Hour')->numeric()->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(','),
            Forms\Components\TextInput::make('ot_amount')->label('Jumlah OT')->numeric()->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(','),
            Forms\Components\TextInput::make('out_of_town')->label('Dinas Luar')->numeric()->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(','),
            Forms\Components\TextInput::make('overnight')->label('Menginap')->numeric()->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(','),
            Forms\Components\TextInput::make('transport')->label('Transport')->numeric()->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(','),
            Forms\Components\TextInput::make('monthly_allowance')->label('Tunjangan Bulanan')->numeric()->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(','),
            Forms\Components\Textarea::make('remarks')->label('Keterangan')->columnSpanFull()->maxLength(65535),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('driver')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')->date()->label('Tanggal')->sortable(),
                Tables\Columns\TextColumn::make('hari')->label('Hari')->sortable(),
                Tables\Columns\TextColumn::make('shift')
                    ->label('Shift')
                    ->color(function ($state) {
                        return $state === 'Holiday' ? 'danger' : 'success';
                    }),
                Tables\Columns\TextColumn::make('from_time')->label('Dari Jam')->sortable(),
                Tables\Columns\TextColumn::make('to_time')->label('Sampai Jam')->sortable(),
                Tables\Columns\TextColumn::make('worked_hours')->label('Jam Kerja')->sortable(),
                Tables\Columns\TextColumn::make('normal_hours')->label('Jam Normal')->sortable(),
                Tables\Columns\TextColumn::make('calculated_ot_hours')->label('Total Jam OT')->sortable(),
                Tables\Columns\TextColumn::make('amount_per_hour')->label('Amount/Hour')->money('idr', true)->sortable(),
                Tables\Columns\TextColumn::make('ot_amount')->label('Jumlah OT')->money('idr', true)->sortable(),
                // Tables\Columns\TextColumn::make('transport')->label('Transport')->money('idr', true)->sortable(),
                // Tables\Columns\TextColumn::make('monthly_allowance')->label('Tunjangan Bulanan')->money('idr', true)->sortable(),
                // Tables\Columns\TextColumn::make('out_of_town')->label('Dinas Luar')->money('idr', true)->sortable(),
                // Tables\Columns\TextColumn::make('overnight')->label('Menginap')->money('idr', true)->sortable(),
                // Tables\Columns\TextColumn::make('remarks')->label('Keterangan')->sortable(),
            ])
            ->defaultSort('tanggal', 'asc')
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

                        return $selectedMonth ? $query->whereMonth('tanggal', substr($selectedMonth, 5, 2))->whereYear('tanggal', substr($selectedMonth, 0, 4)) : $query;
                    }),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
                Actions\ActionGroup::make([
                    Actions\Action::make('previewPdf')
                        ->label('Preview PDF')
                        ->icon('heroicon-o-eye')
                        ->action(function ($livewire) {
                            $filters = $livewire->tableFilters;
                            $month = $filters['month']['value'] ?? null;

                            if (! $month) {
                                Notification::make()
                                    ->title('Filter belum dipilih')
                                    ->body('Silakan pilih bulan terlebih dahulu sebelum melihat pratinjau PDF.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $driverId = $this->ownerRecord->id;
                            $url = route('preview-laporan-overtime', [
                                'driver_id' => $driverId,
                                'month' => $month,
                            ]);

                            $this->js("window.open('{$url}', '_blank')");
                        }),
                    Actions\Action::make('exportExcel')
                        ->label('Export Excel')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function ($livewire) {
                            $filters = $livewire->tableFilters;
                            $month = $filters['month']['value'] ?? null;

                            if (! $month) {
                                Notification::make()
                                    ->title('Silakan pilih bulan terlebih dahulu sebelum mengekspor data.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $driverId = $this->ownerRecord->id;
                            $url = route('export-overtime-excel', [
                                'driver_id' => $driverId,
                                'month' => $month,
                            ]);

                            $this->js("window.open('{$url}', '_blank')");
                        }),
                ])
                    ->label('Laporan')
                    ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->actions([Actions\EditAction::make(), Actions\DeleteAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }
}
