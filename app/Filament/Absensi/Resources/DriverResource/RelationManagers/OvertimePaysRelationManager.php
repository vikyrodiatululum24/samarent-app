<?php

namespace App\Filament\Absensi\Resources\DriverResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OvertimePaysRelationManager extends RelationManager
{
    protected static string $relationship = 'overtimePays';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('driver')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('driver')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->label('Tanggal')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hari')
                    ->label('Hari')
                    ->sortable(),
                Tables\Columns\TextColumn::make('shift')
                    ->label('Shift')
                    ->sortable(),
                Tables\Columns\TextColumn::make('from_time')
                    ->label('Dari Jam')
                    ->sortable(),
                Tables\Columns\TextColumn::make('to_time')
                    ->label('Sampai Jam')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ot_hours_time')
                    ->label('Jam OT')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ot_1x')
                    ->label('OT 1x')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ot_2x')
                    ->label('OT 2x')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ot_3x')
                    ->label('OT 3x')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ot_4x')
                    ->label('OT 4x')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ot_amount')
                    ->label('Jumlah OT')
                    ->money('idr', true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('transport')
                    ->label('Transport')
                    ->money('idr', true)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
