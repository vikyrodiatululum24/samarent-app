<?php

namespace App\Filament\Manager\Resources;

use App\Filament\Manager\Resources\DriverAttendenceResource\Pages;
use App\Models\DriverAttendence;
use App\Services\GeocodingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

class DriverAttendenceResource extends Resource
{
    protected static ?string $model = DriverAttendence::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Absensi Driver';

    protected static ?string $modelLabel = 'Absensi Driver';

    protected static ?string $pluralModelLabel = 'Absensi Driver';

    protected static ?string $navigationGroup = 'Driver Management';

    protected static bool $shouldRegisterNavigation = false;

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
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Umum')
                    ->schema([
                        Infolists\Components\TextEntry::make('date')
                            ->label('Tanggal')
                            ->date(),
                        Infolists\Components\TextEntry::make('driver.user.name')
                            ->label('Nama Driver'),
                        Infolists\Components\TextEntry::make('project.name')
                            ->label('Project'),
                        Infolists\Components\Group::make([
                        Infolists\Components\TextEntry::make('endUser.name')
                            ->label('End User'),
                            Infolists\Components\TextEntry::make('endUserOut.name')
                                ->label('End User Keluar')
                                ->visible(fn($record) => !empty($record->endUserOut)),
                        ])
                        ->label('End User')
                        ->columns(2),
                        Infolists\Components\TextEntry::make('unit.type')
                            ->label('Unit'),
                        Infolists\Components\TextEntry::make('note')
                            ->label('Catatan')
                            ->columnSpanFull(),
                        Infolists\Components\Group::make([
                            Infolists\Components\Group::make([
                                Infolists\Components\TextEntry::make('endUserOut.email')
                                    ->label('Email End User')
                                    ->copyable(),
                                Infolists\Components\TextEntry::make('logMails.latest.status')
                                    ->label('Status Pengiriman')
                                    ->getStateUsing(function ($record) {
                                        $logMail = $record->logMails()->latest()->first();
                                        return $logMail ? ucfirst($logMail->status) : 'Belum Dikirim';
                                    }),
                                Infolists\Components\TextEntry::make('logMails.latest.error_message')
                                    ->label('Error')
                                    ->getStateUsing(function ($record) {
                                        $logMail = $record->logMails()->latest()->first();
                                        return $logMail && $logMail->status === 'failed' ? $logMail->error_message : '-';
                                    })
                                    ->color('danger')
                                    ->visible(fn($record) => $record->logMails()->latest()->first()?->status === 'failed'),
                            ])
                            ->columns(3),
                        Infolists\Components\IconEntry::make('approved')
                            ->label('Status Approved')
                            ->getStateUsing(function ($record) {
                                if ($record->confirmation()->where('status', 'approved')->exists()) {
                                    return 'heroicon-o-check-circle';
                                } elseif ($record->confirmation()->where('status', 'rejected')->exists()) {
                                    return 'heroicon-o-x-circle';
                                }  else {
                                    return 'heroicon-o-clock';
                                }
                            })
                            ->icon(fn($state) => $state)
                            ->visible(fn($record) => $record->confirmation()->exists()),
                        ])
                        ->label('Status'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Absensi Masuk')
                    ->schema([
                        Infolists\Components\TextEntry::make('time_in')
                            ->label('Waktu Masuk'),
                        Infolists\Components\TextEntry::make('start_km')
                            ->label('KM Awal'),
                        Infolists\Components\TextEntry::make('location_in')
                            ->label('Lokasi Masuk')
                            ->formatStateUsing(function ($state) {

                                if (!$state) return '-';

                                [$lat, $lng] = explode(',', $state);

                                return app(GeocodingService::class)
                                    ->getAddressFromCoordinates($lat, $lng);
                            })
                            ->columnSpanFull(),
                        Infolists\Components\ImageEntry::make('photo_in')
                            ->label('Foto Masuk')
                            ->disk('public')
                            ->getStateUsing(fn($record) => str_replace('storage/', '', $record->photo_in))
                            ->size(300)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Absensi Check')
                    ->schema([
                        ViewEntry::make('checks.attendance_id')
                            ->label('Absensi Check')
                            ->view('filament.check-attendences'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Absensi Keluar')
                    ->schema([
                        Infolists\Components\TextEntry::make('time_out')
                            ->label('Waktu Keluar'),
                        Infolists\Components\TextEntry::make('end_km')
                            ->label('KM Akhir'),
                        Infolists\Components\TextEntry::make('location_out')
                            ->label('Lokasi Keluar')
                            ->formatStateUsing(function ($state) {

                                if (!$state) return '-';

                                [$lat, $lng] = explode(',', $state);

                                return app(GeocodingService::class)
                                    ->getAddressFromCoordinates($lat, $lng);
                            })
                            ->columnSpanFull(),
                        Infolists\Components\ImageEntry::make('photo_out')
                            ->label('Foto Keluar')
                            ->disk('public')
                            ->getStateUsing(fn($record) => str_replace('storage/', '', $record->photo_out))
                            ->size(300)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(fn($record) => !empty($record->time_out)),
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
            'index' => Pages\ListDriverAttendences::route('/'),
            'view' => Pages\ViewDriverAttendence::route('/{record}'),
        ];
    }
}
