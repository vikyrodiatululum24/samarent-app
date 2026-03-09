<?php

namespace App\Filament\Manager\Resources\DriverResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class OvertimePaysRelationManager extends RelationManager
{
    protected static string $relationship = 'overtimePay';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('driver')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')->date()->label('Tanggal')->sortable(),
                Tables\Columns\TextColumn::make('hari')->label('Hari')->sortable(),
                Tables\Columns\TextColumn::make('shift')->label('Shift')->sortable(),
                Tables\Columns\TextColumn::make('from_time')->label('Dari Jam')->sortable(),
                Tables\Columns\TextColumn::make('to_time')->label('Sampai Jam')->sortable(),
                Tables\Columns\TextColumn::make('ot_hours_time')->label('Jam OT')->sortable(),
                Tables\Columns\TextColumn::make('ot_1x')->label('OT 1.5x')->sortable(),
                Tables\Columns\TextColumn::make('ot_2x')->label('OT 2x')->sortable(),
                Tables\Columns\TextColumn::make('ot_3x')->label('OT 3x')->sortable(),
                Tables\Columns\TextColumn::make('ot_4x')->label('OT 4x')->sortable(),
                Tables\Columns\TextColumn::make('calculated_ot_hours')->label('Total Jam OT')->sortable(),
                Tables\Columns\TextColumn::make('amount_per_hour')->label('Amount/Hour')->money('idr', true)->sortable(),
                Tables\Columns\TextColumn::make('ot_amount')->label('Jumlah OT')->money('idr', true)->sortable(),
                Tables\Columns\TextColumn::make('transport')->label('Transport')->money('idr', true)->sortable(),
                Tables\Columns\TextColumn::make('monthly_allowance')->label('Tunjangan Bulanan')->money('idr', true)->sortable(),
                Tables\Columns\TextColumn::make('out_of_town')->label('Dinas Luar')->money('idr', true)->sortable(),
                Tables\Columns\TextColumn::make('overnight')->label('Menginap')->money('idr', true)->sortable(),
                Tables\Columns\TextColumn::make('remarks')->label('Keterangan')->sortable(),
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

                        return $selectedMonth ? $query->whereMonth('tanggal', substr($selectedMonth, 5, 2))->whereYear('tanggal', substr($selectedMonth, 0, 4)) : $query;
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-plus')
                    ->action(function ($livewire) {
                        $driverId = $this->ownerRecord->id;
                        $filters = $livewire->tableFilters;
                        $month = $filters['month']['value'] ?? null;
                        if (!$month) {
                            Notification::make()
                                ->title('Silakan pilih bulan terlebih dahulu sebelum mengekspor data.')
                                ->danger()
                                ->send();
                            return null;
                        }

                        $url = route('export-overtime-excel', ['driver_id' => $driverId, 'month' => $month]);

                        $this->js("window.open('{$url}', '_blank')");
                    })
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
