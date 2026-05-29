<?php

namespace App\Filament\Absensi\Resources;

use App\Filament\Absensi\Resources\KehadiranDriverResource\Pages;
use App\Filament\Absensi\Resources\KehadiranDriverResource\RelationManagers\OvertimePayRelationManager;
use App\Models\DriverAttendence;
use App\Models\EndUser;
use App\Services\GeocodingService;
use App\Services\Overtime\OvertimeCalculatorService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class KehadiranDriverResource extends Resource
{
    protected static ?string $model = DriverAttendence::class;

    protected static ?string $pluralModelLabel = 'Kehadiran Driver';

    protected static function normalizeStoragePath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return str_replace('storage/', '', $path);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi Kehadiran Driver')
                ->schema([
                    Forms\Components\Hidden::make('driver_id'),

                    Forms\Components\DatePicker::make('date')->label('Tanggal')->required(),

                    Forms\Components\Select::make('unit_id')
                        ->label('Unit')
                        ->relationship('unit', 'nopol')
                        ->required()
                        ->searchable(),
                    Forms\Components\Select::make('project_id')
                        ->label('Project')
                        ->relationship('project', 'name')
                        ->required()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(function (Set $set) {
                            $set('end_user_id', null);
                            $set('end_user_out', null);
                            $set('user_id', null);
                        }),
                    Forms\Components\Select::make('user_id')
                        ->label('Driver')
                        ->relationship(
                            name: 'user',
                            titleAttribute: 'name',
                            modifyQueryUsing: function (Builder $query, Get $get) {
                                if ($get('project_id')) {
                                    $query->whereHas('driver.project', function (Builder $query) use ($get) {
                                        $query->where('id', $get('project_id'));
                                    });
                                }
                            }
                        )
                        ->disabled(fn(Get $get) => blank($get('project_id')))
                        ->live()
                        ->required()
                        ->searchable(),
                    Forms\Components\Select::make('shift')
                        ->label('Shift')
                        ->options([
                            'Weekday' => 'Weekday',
                            'Holiday' => 'Holiday',
                        ]),
                ])
                ->columns(2),
            Section::make('Absensi Masuk')
                ->schema([
                    Forms\Components\DateTimePicker::make('time_in')->label('Waktu Masuk')->required(),
                    Forms\Components\Select::make('end_user_id')
                        ->label('Start User')
                        ->options(fn(Get $get) => EndUser::query()
                            ->when($get('project_id'), fn($query, $projectId) => $query->where('project_id', $projectId))
                            ->orderBy('name')
                            ->pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->disabled(fn(Get $get) => blank($get('project_id')))
                        ->live(),
                    Forms\Components\TextInput::make('start_km')->label('KM Awal')
                    ->rules(['regex:/^[0-9]+$/'])
                    ->validationMessages(['regex' => 'KM Awal harus berupa angka'])
                    ->minValue(0)
                    ->required(),
                    Forms\Components\TextInput::make('location_in')->label('Lokasi Masuk')->maxLength(255),
                    Forms\Components\FileUpload::make('photo_in')
                        ->label('Foto Masuk')
                        ->image()
                        ->resize(50)
                        ->maxWidth(1024)
                        ->maxSize(2048) // Maksimal 2MB
                        ->disk('public')
                        ->directory('absen/photo_in')
                        ->afterStateHydrated(function ($component, $state) {
                            $component->state(self::normalizeStoragePath($state));
                        })
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Absensi Check')
                ->schema([
                    Forms\Components\Repeater::make('checks')
                        ->label('Absensi Check')
                        ->relationship('checks')
                        ->schema([
                            Forms\Components\TextInput::make('location')
                                ->label('Lokasi Check'),
                            Forms\Components\DateTimePicker::make('created_at')
                                ->label('Waktu Check'),
                            Forms\Components\FileUpload::make('photo')
                                ->label('Foto Check')
                                ->image()
                                ->resize(50)
                                ->maxWidth(1024)
                                ->maxSize(2048) // Maksimal 2MB
                                ->disk('public')
                                ->directory('absen/photo_check')
                                ->afterStateHydrated(function ($component, $state) {
                                    $component->state(self::normalizeStoragePath($state));
                                })
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                ])
                ->columns(1),
            Section::make('Absensi Keluar')
                ->schema([
                    Forms\Components\Select::make('end_user_out')
                        ->label('End User')
                        ->options(fn(Get $get) => EndUser::query()
                            ->when($get('project_id'), fn($query, $projectId) => $query->where('project_id', $projectId))
                            ->orderBy('name')
                            ->pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->disabled(fn(Get $get) => blank($get('project_id'))),
                    Forms\Components\DateTimePicker::make('time_out')->label('Waktu Keluar')->required(),
                    Forms\Components\TextInput::make('end_km')->label('KM Akhir')->numeric()->minValue(0),
                    Forms\Components\TextInput::make('location_out')->label('Lokasi Keluar')->maxLength(255),
                    Forms\Components\FileUpload::make('photo_out')
                        ->label('Foto Keluar')
                        ->image()
                        ->resize(50)
                        ->maxWidth(1024)
                        ->maxSize(2048) // Maksimal 2MB
                        ->disk('public')
                        ->directory('absen/photo_out')
                        ->afterStateHydrated(function ($component, $state) {
                            $component->state(self::normalizeStoragePath($state));
                        })
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Informasi Lainnya')
                ->schema([
                    Forms\Components\Textarea::make('note')->label('Catatan')->maxLength(65535)->columnSpanFull(),
                    Forms\Components\Textarea::make('note_admin')->label('Catatan Admin')->maxLength(65535)->columnSpanFull(),
                    Forms\Components\Checkbox::make('is_complete')->label('Selesai')->default(false)
                ])
                ->columns(2),
        ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Driver')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('endUser.name')
                    ->label('User In')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('endUserOut.name')
                    ->label('User Out')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit.nopol')
                    ->label('Unit')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->label('Tanggal')
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_in')
                    ->label('Waktu Masuk')
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_check')
                    ->label('Waktu Check')
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_out')
                    ->label('Waktu Keluar')
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('is_complete')
                    ->label('Selesai')
                    ->sortable()
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('project_id')
                    ->label('Filter Project')
                    ->relationship('project', 'name'),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Filter Driver')
                    ->relationship('user', 'name')
                    ->query(function (Builder $query, array $data, \Filament\Tables\Contracts\HasTable $livewire): Builder {
                        $userId = $data['value'] ?? null;
                        $projectId = $livewire->getTableFilterState('project_id')['value'] ?? null;

                        return $query
                            ->when($userId, fn (Builder $query, $userId) => $query->where('user_id', $userId))
                            ->when($projectId, function (Builder $query, $projectId): Builder {
                                return $query->whereHas('user.driver', function (Builder $query) use ($projectId): Builder {
                                    return $query->where('project_id', $projectId);
                                });
                            });
                    }),
                Tables\Filters\SelectFilter::make('is_complete')
                    ->label('Filter Status Selesai')
                    ->options([
                        1 => 'Selesai',
                        0 => 'Belum Selesai',
                    ]),
                Tables\Filters\SelectFilter::make('month')
                    ->label('Filter Bulan')
                    ->options(function () {
                        $months = [];
                        $attendances = DriverAttendence::selectRaw('DISTINCT YEAR(date) as year, MONTH(date) as month')
                            ->orderBy('year', 'desc')
                            ->orderBy('month', 'desc')
                            ->get();

                        foreach ($attendances as $attendance) {
                            $key = $attendance->year . '-' . str_pad($attendance->month, 2, '0', STR_PAD_LEFT);
                            $label = \Carbon\Carbon::createFromDate($attendance->year, $attendance->month, 1)->format('F Y');
                            $months[$key] = $label;
                        }

                        return $months;
                    })
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            [$year, $month] = explode('-', $data['value']);
                            $query->whereYear('date', $year)
                                ->whereMonth('date', $month);
                        }
                    }),

            ])
            ->actions([EditAction::make(), ViewAction::make()])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('complete_and_recalculate_overtime')
                        ->label('Tandai Selesai & Hitung Ulang OT')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Hitung Ulang Overtime')
                        ->modalSubheading('Semua absensi yang dipilih akan ditandai selesai lalu overtime dihitung ulang.')
                        ->modalButton('Ya, Proses')
                        ->action(function (Collection $records): void {
                            $calculator = app(OvertimeCalculatorService::class);
                            $successCount = 0;
                            $failedCount = 0;

                            foreach ($records as $record) {
                                try {
                                    $calculator->calculateAndPersist($record);
                                    $record->forceFill(['is_complete' => true])->save();
                                    $successCount++;
                                } catch (\Throwable $e) {
                                    report($e);
                                    $failedCount++;
                                }
                            }

                            if ($successCount > 0) {
                                Notification::make()
                                    ->title("{$successCount} absensi berhasil ditandai selesai dan overtime dihitung ulang.")
                                    ->success()
                                    ->send();
                            }

                            if ($failedCount > 0) {
                                Notification::make()
                                    ->title("{$failedCount} absensi gagal diproses.")
                                    ->warning()
                                    ->body('Periksa data tanggal, jam masuk/keluar, dan policy overtime pada record yang gagal.')
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Detail Kehadiran Driver')
                    ->columns(2)
                    ->schema([
                        Group::make()
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Driver'),
                                TextEntry::make('unit.type')
                                    ->label('Unit'),
                                TextEntry::make('unit.nopol')
                                    ->label('Nopol'),
                                TextEntry::make('date')
                                    ->label('Tanggal'),
                                TextEntry::make('note')
                                    ->label('Catatan'),
                            ]),
                        Group::make()
                            ->schema([
                                TextEntry::make('project.name')
                                    ->label('Project'),
                                TextEntry::make('endUser.name')
                                    ->label('End User 1'),
                                TextEntry::make('endUserOut.name')
                                    ->label('End User 2'),
                                TextEntry::make('is_complete')
                                    ->label('Approval')
                                    ->badge(fn($state) => $state ? 'success' : 'warning')
                                    ->formatStateUsing(fn($state) => $state ? 'Selesai' : 'Belum Selesai')
                            ])
                    ]),
                Section::make('Absensi Masuk')
                    ->schema([
                        TextEntry::make('time_in')
                            ->label('Waktu Masuk'),
                        TextEntry::make('start_km')
                            ->label('KM Awal'),
                        TextEntry::make('location_in')
                            ->label('Lokasi Masuk')
                            ->formatStateUsing(function ($state) {

                                if (blank($state) || !str_contains($state, ',')) return '-';

                                [$lat, $lng] = explode(',', $state);

                                return app(GeocodingService::class)
                                    ->getAddressFromCoordinates($lat, $lng);
                            })
                            ->columnSpanFull(),
                        ImageEntry::make('photo_in')
                            ->label('Foto Masuk')
                            ->disk('public')
                            ->getStateUsing(fn($record) => self::normalizeStoragePath($record->photo_in))
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

                                if (blank($state) || !str_contains($state, ',')) return '-';

                                [$lat, $lng] = explode(',', $state);

                                return app(GeocodingService::class)
                                    ->getAddressFromCoordinates($lat, $lng);
                            })
                            ->columnSpanFull(),
                        ImageEntry::make('photo_out')
                            ->label('Foto Keluar')
                            ->disk('public')
                            ->getStateUsing(fn($record) => self::normalizeStoragePath($record->photo_out))
                            ->size(300)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(fn($record) => !empty($record->time_out)),
            ])
            ->columns(1);
    }

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
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
