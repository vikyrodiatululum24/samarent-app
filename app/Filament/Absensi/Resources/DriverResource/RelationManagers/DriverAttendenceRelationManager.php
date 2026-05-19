<?php

namespace App\Filament\Absensi\Resources\DriverResource\RelationManagers;

use App\Helpers\PayrollHelpers;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class DriverAttendenceRelationManager extends RelationManager
{
    protected static string $relationship = 'driverAttendences';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal')
                    ->required(),

                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->label('Project')
                    ->required(),

                Forms\Components\Select::make('end_user_id')
                    ->relationship('endUser', 'name')
                    ->searchable()
                    ->label('End User')
                    ->required(),

                Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'type')
                    ->searchable()
                    ->label('Unit')
                    ->required(),

                Forms\Components\TextInput::make('start_km')
                    ->label('Start KM')
                    ->inputMode('numeric')
                    ->rules(['regex:/^[0-9]+$/'])
                    ->validationMessages([
                        'regex' => 'Start KM harus berupa angka.',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('location_in')
                    ->label('Lokasi Masuk')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TimePicker::make('time_in')
                    ->label('Waktu Masuk')
                    ->required(),

                Forms\Components\FileUpload::make('photo_in')
                    ->label('Foto Masuk')
                    ->image()
                    ->disk('public')
                    ->directory('photos/attendances')
                    ->maxFiles(1),
                Forms\Components\TimePicker::make('time_check')
                    ->label('Waktu Cek'),
                Forms\Components\TextInput::make('location_check')
                    ->label('Lokasi Cek')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('photo_check')
                    ->label('Foto Cek')
                    ->image()
                    ->disk('public')
                    ->directory('photos/attendances')
                    ->maxFiles(1),
                Forms\Components\TextInput::make('end_km')
                    ->label('End KM')
                    ->inputMode('numeric')
                    ->rules(['regex:/^[0-9]+$/'])
                    ->validationMessages([
                        'regex' => 'End KM harus berupa angka.',
                    ]),
                Forms\Components\TimePicker::make('time_out')
                    ->label('Waktu Keluar'),
                Forms\Components\TextInput::make('location_out')
                    ->label('Lokasi Keluar')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('photo_out')
                    ->label('Foto Keluar')
                    ->image()
                    ->disk('public')
                    ->directory('photos/attendances')
                    ->maxFiles(1),
                Forms\Components\Textarea::make('note')
                    ->label('Catatan')
                    ->rows(3)
                    ->maxLength(65535),
                Forms\Components\Toggle::make('is_complete')
                    ->label('Approved'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                ->toggleable(isToggledHiddenByDefault: false)
                ->label('Tanggal'),
                Tables\Columns\TextColumn::make('shift')
                    ->label('Shift')
                    ->color(function ($state) {
                        return $state === 'Holiday' ? 'danger' : 'success';
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('project.name')->label('Project')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('endUser.name')->label('Start User')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('endUserOut.name')->label('End User')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('unit.type')->label('Unit')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('start_km')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('location_in')->label('Lokasi Masuk')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('time_in')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\ImageColumn::make('photo_in')
                    ->disk('public')
                    ->square()
                    ->label('Foto Masuk')
                    ->getStateUsing(fn($record) => str_replace('storage/', '', $record->photo_in))
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\ImageColumn::make('photo_in')
                    ->disk('public')
                    ->square()
                    ->label('Foto Masuk')
                    ->getStateUsing(fn($record) => str_replace('storage/', '', $record->photo_in))
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('time_chek')
                    ->label('Jam Cek')
                    ->getStateUsing(function ($record) {
                        $check = $record->checks()->latest()->first();
                        return $check ? $check->created_at->format('H:i:s') : '-';
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('location_chek')
                    ->label('Lokasi Cek')
                    ->getStateUsing(function ($record) {
                        $check = $record->checks()->latest()->first();
                        return $check ? $check->location : '-';
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\ImageColumn::make('photo_chek')
                    ->label('Foto Cek')
                    ->getStateUsing(function ($record) {
                        $check = $record->checks()->latest()->first();
                        return $check ? str_replace('storage/', '', $check->photo) : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('end_km')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('time_out')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('location_out')->label('Lokasi Keluar')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\ImageColumn::make('photo_out')
                    ->disk('public')
                    ->square()
                    ->label('Foto Keluar')
                    ->getStateUsing(fn($record) => str_replace('storage/', '', $record->photo_out))
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('note')
                    ->limit(50)
                    ->wrap()
                    ->label('Catatan'),
                Tables\Columns\BooleanColumn::make('is_complete')->label('Approved'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('month')
                    ->options(function () {
                        $months = [];
                        for ($i = 0; $i < 12; $i++) {
                            $date = Carbon::now()->subMonths($i);
                            $months[$date->format('Y-m')] = $date->translatedFormat('F Y');
                        }
                        return array_reverse($months, true); // urut dari lama ke baru
                    })
                    ->default(date('Y-m'))
                    ->query(function (Builder $query, array $data): Builder {
                        // Get the selected value
                        $selectedMonth = $data['value'] ?? date('Y-m');

                        return $selectedMonth ? $query->whereMonth('date', substr($selectedMonth, 5, 2))
                            ->whereYear('date', substr($selectedMonth, 0, 4)) : $query;
                    })
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                Actions\ActionGroup::make([
                    Actions\Action::make('priviewPdf')
                        ->label('Preview PDF')
                        ->icon('heroicon-o-eye')
                        ->action(function ($livewire) {
                            $filters = $livewire->tableFilters;
                            $month = $filters['month']['value'] ?? null;

                            if (!$month) {
                                Notification::make()
                                    ->title('Filter belum dipilih')
                                    ->body('Silakan pilih bulan terlebih dahulu sebelum melihat pratinjau PDF.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $driverId = $this->ownerRecord->id;
                            $url = route('preview-laporan-absensi', [
                                'driver_id' => $driverId,
                                'month' => $month,
                            ]);

                            $this->js("window.open('{$url}', '_blank')");
                        }),
                    Actions\Action::make('printPdf')
                        ->label('Print PDF')
                        ->icon('heroicon-o-printer')
                        ->badge(function ($livewire) {
                            $filters = $livewire->tableFilters;
                            $cetak = \App\Models\Cetak::where('driver_id', $this->ownerRecord->id)->where('periode', $filters['month']['value'] ?? null)->first();
                            return $cetak ? 'sudah di print' : null;
                        })
                        ->action(function ($livewire) {
                            $filters = $livewire->tableFilters;
                            $month = $filters['month']['value'] ?? null;

                            if (!$month) {
                                Notification::make()
                                    ->title('Filter belum dipilih')
                                    ->body('Silakan pilih bulan terlebih dahulu sebelum mencetak PDF.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $driverId = $this->ownerRecord->id;
                            $url = route('laporan-absensi', [
                                'driver_id' => $driverId,
                                'month' => $month,
                            ]);

                            $this->js("window.open('{$url}', '_blank')");
                        }),

                    Actions\Action::make('exportExcel')
                        ->label('Export to Excel')
                        ->icon('heroicon-o-document-plus')
                        ->action(function ($livewire) {
                            $filters = $livewire->tableFilters;
                            $month = $filters['month']['value'] ?? null;

                            if (!$month) {
                                Notification::make()
                                    ->title('Filter belum dipilih')
                                    ->body('Silakan pilih bulan terlebih dahulu sebelum mengekspor ke Excel.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $driverId = $this->ownerRecord->id;
                            $url = route('export-absensi-excel', [
                                'driver_id' => $driverId,
                                'month' => $month,
                            ]);

                            $this->js("window.open('{$url}', '_blank')");
                        }),
                ])
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->actions([
                Actions\Action::make('addNote')
                    ->label('Catatan Admin')
                    ->icon('heroicon-o-pencil-square')
                    ->form([
                        Textarea::make('note_admin')
                            ->label('Catatan Admin')
                            ->rows(4)
                            ->maxLength(65535),
                    ])
                    ->fillForm(fn($record) => ['note_admin' => $record->note_admin])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'note_admin' => $data['note_admin'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Catatan admin disimpan')
                            ->success()
                            ->send();
                    }),

                Actions\Action::make('edit')
                    ->label('Edit Absensi')
                    ->icon('heroicon-o-pencil')
                    ->form([
                        Section::make('Informasi Absensi')
                            ->schema([
                                DateTimePicker::make('time_in')->label('Waktu Masuk'),
                                DateTimePicker::make('time_out')->label('Waktu Keluar'),
                                Select::make('project_id')
                                    ->relationship('project', 'name')
                                    ->searchable()
                                    ->columnSpan(2)
                                    ->label('Project'),
                                Select::make('enduser_id')
                                    ->relationship('endUser', 'name')
                                    ->searchable()
                                    ->label('Start User'),
                                Select::make('endUserOut_id')
                                    ->relationship('endUserOut', 'name')
                                    ->searchable()
                                    ->label('End User'),
                                Select::make('shift')
                                    ->label('Shift')
                                    ->options([
                                        'Weekday' => 'Weekday',
                                        'Holiday' => 'Holiday',
                                    ]),
                                Toggle::make('is_complete')->label('Selesai'),
                            ])
                            ->columns(2),
                    ])
                    ->fillForm(fn($record) => [
                        'time_in' => $record->time_in,
                        'time_out' => $record->time_out,
                        'is_complete' => (bool) $record->is_complete,
                        'shift' => $record->shift,
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'time_in' => $data['time_in'] ?? $record->time_in,
                            'time_out' => $data['time_out'] ?? $record->time_out,
                            'is_complete' => $data['is_complete'] ?? 0,
                            'shift' => $data['shift'] ?? $record->shift,
                        ]);

                        if ($record->is_complete) {
                            PayrollHelpers::calculateOvertimePay($record);
                        }

                        Notification::make()
                            ->title('Absensi diperbarui')
                            ->success()
                            ->send();
                    }),

                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
