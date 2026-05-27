<?php

namespace App\Filament\Absensi\Resources\KehadiranDriverResource\RelationManagers;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;

class OvertimePayRelationManager extends RelationManager
{
    protected static string $relationship = 'overtimePay';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
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
                Tables\Columns\TextColumn::make('worked_hours')->label('Jam Kerja')->sortable(),
                Tables\Columns\TextColumn::make('normal_hours')->label('Jam Normal')->sortable(),
                Tables\Columns\TextColumn::make('calculated_ot_hours')->label('Total Jam OT')->sortable(),
                Tables\Columns\TextColumn::make('amount_per_hour')->label('Amount/Hour')->money('idr', true)->sortable(),
                Tables\Columns\TextColumn::make('ot_amount')->label('Jumlah OT')->money('idr', true)->sortable(),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                // Action::make('edit')
                //     ->label('Edit')
                //     ->icon('heroicon-o-pencil')
                //     ->form([
                //         TextInput::make('monthly_allowance')->label('Tunjangan Bulanan')->numeric()->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(',')->minValue(0),
                //         TextInput::make('out_of_town')->label('Dinas Luar')->numeric()->minValue(0)->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(','),
                //         TextInput::make('overnight')->label('Menginap')->numeric()->minValue(0)->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(','),
                //         Textarea::make('remarks')->label('Keterangan')->columnSpanFull()->maxLength(65535),
                //     ])
                //     ->fillForm(fn($record) => $record->toArray())
                //     ->action(function ($record, array $data) {
                //         $record->update($data);
                //         Notification::make()
                //             ->title('Overtime Pay updated successfully')
                //             ->success()
                //             ->send();
                //     }),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
