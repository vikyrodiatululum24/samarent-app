<?php

namespace App\Filament\Manager\Resources;

use App\Filament\Manager\Resources\DriverAttendenceResource\Pages;
use App\Models\DriverAttendence;
use App\Services\GeocodingService;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;


class DriverAttendenceResource extends Resource
{
    protected static ?string $model = DriverAttendence::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Absensi Driver';

    protected static ?string $modelLabel = 'Absensi Driver';

    protected static ?string $pluralModelLabel = 'Absensi Driver';

    protected static string | \UnitEnum | null $navigationGroup = 'Driver Management';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            //
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->paginated([10, 25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('driver.user.name')
                    ->label('Nama Driver')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project'),
                Tables\Columns\TextColumn::make('time_in')
                    ->label('Waktu Masuk'),
                Tables\Columns\TextColumn::make('time_out')
                    ->label('Waktu Keluar'),
                Tables\Columns\BooleanColumn::make('is_complete')
                    ->label('Approved'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Umum')
                    ->schema([
                        TextEntry::make('date')->label('Tanggal')->date(),
                        TextEntry::make('driver.user.name')->label('Nama Driver'),
                        TextEntry::make('project.name')->label('Project'),
                        Group::make([
                            TextEntry::make('endUser.name')->label('Start User'),
                            TextEntry::make('endUserOut.name')
                                ->label('End User'),
                        ])
                            ->columns(2),
                        TextEntry::make('unit.type')->label('Unit'),
                        TextEntry::make('note')->label('Catatan')->columnSpanFull(),
                        Group::make([
                            Group::make([
                                TextEntry::make('endUserOut.email')->label('Email End User')->copyable(),
                                TextEntry::make('logMails.latest.status')
                                    ->label('Status Pengiriman')
                                    ->getStateUsing(function ($record) {
                                        $logMail = $record->logMails()->latest()->first();

                                        return $logMail ? ucfirst($logMail->status) : 'Belum Dikirim';
                                    }),
                                TextEntry::make('logMails.latest.error_message')
                                    ->label('Error')
                                    ->getStateUsing(function ($record) {
                                        $logMail = $record->logMails()->latest()->first();

                                        return ($logMail && $logMail->status === 'failed') ? $logMail->error_message : '-';
                                    })
                                    ->color('danger')
                                    ->visible(fn ($record) => $record->logMails()->latest()->first()?->status === 'failed'),
                            ])->columns(3),
                            \Filament\Infolists\Components\IconEntry::make('approved')
                                ->label('Status Approved')
                                ->getStateUsing(function ($record) {
                                    if ($record->confirmation()->where('status', 'approved')->exists()) {
                                        return 'heroicon-o-check-circle';
                                    }

                                    if ($record->confirmation()->where('status', 'rejected')->exists()) {
                                        return 'heroicon-o-x-circle';
                                    }

                                    return 'heroicon-o-clock';
                                })
                                ->icon(fn ($state) => $state)
                                ->visible(fn ($record) => $record->confirmation()->exists()),
                        ]),
                    ])
                    ->columns(3),

                Section::make('Absensi Masuk')
                    ->schema([
                        TextEntry::make('time_in')->label('Waktu Masuk'),
                        TextEntry::make('start_km')->label('KM Awal'),
                        TextEntry::make('location_in')
                            ->label('Lokasi Masuk')
                            ->formatStateUsing(function ($state) {
                                if (! $state) {
                                    return '-';
                                }

                                [$lat, $lng] = explode(',', $state);

                                return app(GeocodingService::class)->getAddressFromCoordinates($lat, $lng);
                            })
                            ->columnSpanFull(),
                        ImageEntry::make('photo_in')
                            ->label('Foto Masuk')
                            ->disk('public')
                            ->getStateUsing(fn ($record) => str_replace('storage/', '', $record->photo_in))
                            ->size(300)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Absensi Check')
                    ->schema([
                        ViewEntry::make('checks.attendance_id')
                            ->label('Absensi Check')
                            ->view('filament.check-attendences'),
                    ])
                    ->columns(2),

                Section::make('Absensi Keluar')
                    ->schema([
                        TextEntry::make('time_out')->label('Waktu Keluar'),
                        TextEntry::make('end_km')->label('KM Akhir'),
                        TextEntry::make('location_out')
                            ->label('Lokasi Keluar')
                            ->formatStateUsing(function ($state) {
                                if (! $state) {
                                    return '-';
                                }

                                [$lat, $lng] = explode(',', $state);

                                return app(GeocodingService::class)->getAddressFromCoordinates($lat, $lng);
                            })
                            ->columnSpanFull(),
                        ImageEntry::make('photo_out')
                            ->label('Foto Keluar')
                            ->disk('public')
                            ->getStateUsing(fn ($record) => str_replace('storage/', '', $record->photo_out))
                            ->size(300)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => ! empty($record->time_out)),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDriverAttendences::route('/'),
            'view' => Pages\ViewDriverAttendence::route('/{record}'),
        ];
    }
}
