<?php

namespace App\Filament\Absensi\Resources;

use App\Filament\Absensi\Resources\KehadiranDriverResource\Pages;
use App\Filament\Absensi\Resources\KehadiranDriverResource\RelationManagers\OvertimePayRelationManager;
use App\Models\DriverAttendence;
use App\Services\GeocodingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KehadiranDriverResource extends Resource
{
    protected static ?string $model = DriverAttendence::class;

    protected static ?string $pluralModelLabel = 'Kehadiran Driver';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('date')->label('Tanggal')->required(),
            Forms\Components\DateTimePicker::make('time_in')->label('Waktu Masuk')->required(),
            Forms\Components\DateTimePicker::make('time_out')->label('Waktu Keluar')->required(),
            Forms\Components\Checkbox::make('is_complete')->label('Selesai')->default(false)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([Tables\Columns\TextColumn::make('user.name')->label('Driver')->searchable()->sortable(), Tables\Columns\TextColumn::make('project.name')->label('Project')->searchable()->sortable(), Tables\Columns\TextColumn::make('endUser.name')->label('End User')->searchable()->sortable(), Tables\Columns\TextColumn::make('unit.nopol')->label('Unit')->searchable()->sortable(), Tables\Columns\TextColumn::make('date')->date()->label('Tanggal')->sortable(), Tables\Columns\TextColumn::make('time_in')->label('Waktu Masuk')->sortable(), Tables\Columns\TextColumn::make('time_check')->label('Waktu Check')->sortable(), Tables\Columns\TextColumn::make('time_out')->label('Waktu Keluar')->sortable(), Tables\Columns\BooleanColumn::make('is_complete')->label('Selesai')->sortable()])
            ->filters([
                //
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\ViewAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Detail Kehadiran Driver')
                ->columns(2)
                ->schema([Group::make()->schema([TextEntry::make('user.name')->label('Driver'), TextEntry::make('unit.type')->label('Unit'), TextEntry::make('unit.nopol')->label('Nopol'), TextEntry::make('date')->label('Tanggal')]), Group::make()->schema([TextEntry::make('project.name')->label('Project'), TextEntry::make('endUser.name')->label('End User'), TextEntry::make('is_complete')->label('Approval')->badge(fn($state) => $state ? 'success' : 'warning')->formatStateUsing(fn($state) => $state ? 'Selesai' : 'Belum Selesai')])]),
                Section::make('Absensi Masuk')
                    ->schema([
                        TextEntry::make('time_in')
                            ->label('Waktu Masuk'),
                        TextEntry::make('start_km')
                            ->label('KM Awal'),
                        TextEntry::make('location_in')
                            ->label('Lokasi Masuk')
                            ->formatStateUsing(function ($state) {

                                if (!$state) return '-';

                                [$lat, $lng] = explode(',', $state);

                                return app(GeocodingService::class)
                                    ->getAddressFromCoordinates($lat, $lng);
                            })
                            ->columnSpanFull(),
                        ImageEntry::make('photo_in')
                            ->label('Foto Masuk')
                            ->disk('public')
                            ->getStateUsing(fn($record) => str_replace('storage/', '', $record->photo_in))
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
                        TextEntry::make('time_out')
                            ->label('Waktu Keluar'),
                        TextEntry::make('end_km')
                            ->label('KM Akhir'),
                        TextEntry::make('location_out')
                            ->label('Lokasi Keluar')
                            ->formatStateUsing(function ($state) {

                                if (!$state) return '-';

                                [$lat, $lng] = explode(',', $state);

                                return app(GeocodingService::class)
                                    ->getAddressFromCoordinates($lat, $lng);
                            })
                            ->columnSpanFull(),
                        ImageEntry::make('photo_out')
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

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([Pages\ViewKehadiranDriver::class, Pages\EditKehadiranDriver::class]);
    }

    public static function getRelations(): array
    {
        return [OvertimePayRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKehadiranDrivers::route('/'),
            // 'create' => Pages\CreateKehadiranDriver::route('/create'),
            'edit' => Pages\EditKehadiranDriver::route('/{record}/edit'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('created_at', 'desc');
    }
}
