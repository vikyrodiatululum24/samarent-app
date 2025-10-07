<?php

namespace App\Filament\Absensi\Resources\DriverResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DriverAttendenceRelationManager extends RelationManager
{
    protected static string $relationship = 'driverAttendences';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
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
                Tables\Columns\TextColumn::make('time_check'),
                Tables\Columns\TextColumn::make('location_check')->label('Lokasi Cek'),
                Tables\Columns\ImageColumn::make('photo_check')
                    ->disk('public')
                    ->square()
                    ->label('Foto Cek')
                    ->getStateUsing(fn ($record) => str_replace('storage/', '', $record->photo_check)),
                Tables\Columns\TextColumn::make('end_km'),
                Tables\Columns\TextColumn::make('time_out'),
                Tables\Columns\TextColumn::make('location_out')->label('Lokasi Keluar'),
                Tables\Columns\ImageColumn::make('photo_out')
                    ->disk('public')
                    ->square()
                    ->label('Foto Keluar')
                    ->getStateUsing(fn ($record) => str_replace('storage/', '', $record->photo_out)),
                Tables\Columns\BooleanColumn::make('is_complete')->label('Approved'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('month')
                    ->options([
                        '01' => 'January',
                        '02' => 'February',
                        '03' => 'March',
                        '04' => 'April',
                        '05' => 'May',
                        '06' => 'June',
                        '07' => 'July',
                        '08' => 'August',
                        '09' => 'September',
                        '10' => 'October',
                        '11' => 'November',
                        '12' => 'December',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // Get the selected value
                        $selectedMonth = $data['value'] ?? date('m');

                        return $selectedMonth ? $query->whereMonth('date', $selectedMonth) : $query;
                    })
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('printPdf')
                    ->label('Print Laporan')
                    ->icon('heroicon-o-printer')
                    ->url(fn($record) => route('laporan-absensi', ['driver_id' => $this->ownerRecord->id, 'month' => request()->get('tableFilters')['month'] ?? date('m')]))
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
