<?php

namespace App\Filament\User\Resources\PengajuanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceUnitRelationManager extends RelationManager
{
    protected static string $relationship = 'service_unit';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('no_pengajuan')
                //     ->readOnly()
                //     ->maxLength(255),
                Forms\Components\TextInput::make('jenis')
                    ->required()
                    ->label('Jenis Kendaraan')
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->label('Tipe Unit')
                    ->maxLength(255),
                Forms\Components\TextInput::make('nopol')
                    ->required()
                    ->label('Nomor Polisi')
                    ->placeholder('Tanpa Spasi')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('no_pengajuan')
            ->columns([
                Tables\Columns\TextColumn::make('no_pengajuan'),
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
