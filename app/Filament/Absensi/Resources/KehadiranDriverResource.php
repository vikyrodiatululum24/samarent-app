<?php

namespace App\Filament\Absensi\Resources;

use App\Filament\Absensi\Resources\KehadiranDriverResource\Pages;
use App\Filament\Absensi\Resources\KehadiranDriverResource\RelationManagers;
use App\Models\DriverAttendence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KehadiranDriverResource extends Resource
{
    protected static ?string $model = DriverAttendence::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKehadiranDrivers::route('/'),
            'create' => Pages\CreateKehadiranDriver::route('/create'),
            'edit' => Pages\EditKehadiranDriver::route('/{record}/edit'),
            'view' => Pages\ViewKehadiranDriver::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

}
