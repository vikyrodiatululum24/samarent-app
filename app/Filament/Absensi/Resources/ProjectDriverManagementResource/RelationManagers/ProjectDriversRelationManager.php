<?php

namespace App\Filament\Absensi\Resources\ProjectDriverManagementResource\RelationManagers;

use App\Filament\Absensi\Resources\DriverResource;
use Filament\Actions;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectDriversRelationManager extends RelationManager
{
    protected static string $relationship = 'drivers';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nik')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Driver')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sim')
                    ->label('SIM')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_wa')
                    ->label('No. WhatsApp')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\Action::make('viewDriver')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => DriverResource::getUrl('view', ['record' => $record])),
                Actions\Action::make('editDriver')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn ($record) => DriverResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
