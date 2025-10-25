<?php

namespace App\Filament\Absensi\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\DriverAttendence;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Absensi\Resources\KehadiranDriverResource\Pages;


class KehadiranDriverResource extends Resource
{
    protected static ?string $model = DriverAttendence::class;

    protected static ?string $pluralModelLabel = 'Kehadiran Driver';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal')
                    ->required(),
                Forms\Components\TimePicker::make('time_in')
                    ->label('Waktu Masuk')
                    ->required(),
                Forms\Components\TimePicker::make('time_check')
                    ->label('Waktu Check')
                    ->required(),
                Forms\Components\TimePicker::make('time_out')
                    ->label('Waktu Keluar')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Driver')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('project.name')->label('Project')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('endUser.name')->label('End User')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('unit.nopol')->label('Unit')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('date')->date()->label('Tanggal')->sortable(),
                Tables\Columns\TextColumn::make('time_in')->label('Waktu Masuk')->sortable(),
                Tables\Columns\TextColumn::make('time_check')->label('Waktu Check')->sortable(),
                Tables\Columns\TextColumn::make('time_out')->label('Waktu Keluar')->sortable(),
                Tables\Columns\BooleanColumn::make('is_complete')->label('Selesai')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detail Kehadiran Driver')
                    ->columns(2)
                    ->schema([
                        Group::make()
                            ->schema([
                                TextEntry::make('user.name')->label('Driver'),
                                TextEntry::make('unit.type')->label('Unit'),
                                TextEntry::make('unit.nopol')->label('Nopol'),
                                TextEntry::make('date')->label('Tanggal'),
                            ]),
                        Group::make()
                            ->schema([
                                TextEntry::make('project.name')->label('Project'),
                                TextEntry::make('endUser.name')->label('End User'),
                                TextEntry::make('is_complete')
                                    ->label('Approval')
                                    ->badge(fn($state) => $state ? 'success' : 'warning')
                                    ->formatStateUsing(fn($state) => $state ? 'Selesai' : 'Belum Selesai'),
                            ]),
                    ]),
                Section::make('Absen Masuk')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('time_in')
                            ->label('Waktu Masuk')
                            ->icon('heroicon-o-clock')
                            ->dateTime('H:i'),
                        TextEntry::make('start_km')->label('Start KM'),
                        TextEntry::make('location_in')->label('Lokasi Masuk')
                            ->formatStateUsing(fn($state, $record) => $state
                                ? "{$state} <br><a href='https://www.google.com/maps?q={$record->location_out}' target='_blank' class='text-primary-600 underline'>Lihat di Maps</a>"
                                : 'Alamat tidak tersedia')
                            ->html(),
                        TextEntry::make('photo_in')
                            ->label('Foto Masuk')
                            ->formatStateUsing(fn(string $state): string => $state ? '<a href="' . asset($state) . '" target="_blank"><img src="' . asset($state) . '" alt="Photo In" style="max-width: 200px; max-height: 200px;"/></a>' : 'No Photo')
                            ->html(),
                    ]),
                Section::make('Absen Check')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('time_check')
                            ->label('Waktu Check')
                            ->icon('heroicon-o-clock')
                            ->dateTime('H:i'),
                        TextEntry::make('location_check')->label('Lokasi Check')
                            ->formatStateUsing(fn($state, $record) => $state
                                ? "{$state} <br><a href='https://www.google.com/maps?q={$record->location_check}' target='_blank' class='text-primary-600 underline'>Lihat di Maps</a>"
                                : 'Alamat tidak tersedia')
                            ->html(),
                        TextEntry::make('photo_check')
                            ->label('Foto Check')
                            ->formatStateUsing(fn(string $state): string => $state ? '<a href="' . asset($state) . '" target="_blank"><img src="' . asset($state) . '" alt="Photo Check" style="max-width: 200px; max-height: 200px;"/></a>' : 'No Photo')
                            ->html(),
                    ]),
                Section::make('Absen Keluar')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('time_out')
                            ->label('Waktu Keluar')
                            ->icon('heroicon-o-clock')
                            ->dateTime('H:i'),
                        TextEntry::make('end_km')->label('End KM'),
                        TextEntry::make('location_out')
                            ->label('Lokasi Keluar')
                            ->formatStateUsing(fn($state, $record) => $state
                                ? "{$state} <br><a href='https://www.google.com/maps?q={$record->location_out}' target='_blank' class='text-primary-600 underline'>Lihat di Maps</a>"
                                : 'Alamat tidak tersedia')
                            ->html(),
                        TextEntry::make('photo_out')
                            ->label('Foto Keluar')
                            ->formatStateUsing(fn(string $state): string => $state ? '<a href="' . asset($state) . '" target="_blank"><img src="' . asset($state)
                                . '" alt="Photo Out" style="max-width: 200px; max-height: 200px;"/></a>' : 'No Photo')
                            ->html(),
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
            // 'create' => Pages\CreateKehadiranDriver::route('/create'),
            // 'edit' => Pages\EditKehadiranDriver::route('/{record}/edit'),
            'view' => Pages\ViewKehadiranDriver::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return true;
    }
}
