<?php

namespace App\Filament\Resources\BengkelResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class KontakBengkelsRelationManager extends RelationManager
{
    protected static string $relationship = 'kontakBengkels';

    protected static ?string $title = 'Kontak Bengkel';

    protected static ?string $modelLabel = 'Kontak';

    protected static ?string $pluralModelLabel = 'Kontak';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->label('Nama Kontak')
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_telp')
                    ->label('Nomor Telepon')
                    ->tel()
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Kontak')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_telp')
                    ->label('Nomor Telepon')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-phone'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
