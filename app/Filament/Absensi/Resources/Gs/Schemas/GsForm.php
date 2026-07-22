<?php

namespace App\Filament\Absensi\Resources\Gs\Schemas;

use App\Models\Driver;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Driver Original')
                    ->schema([
                        Select::make('driver_id')
                            ->label('Driver')
                            ->options(fn () => Driver::query()
                                ->join('users', 'drivers.user_id', '=', 'users.id')
                                ->orderBy('users.name')
                                ->pluck('users.name', 'drivers.id')
                            )
                            ->searchable()
                            ->required(),
                        TextInput::make('no_hp')
                            ->label('No. HP Driver')
                            ->maxLength(15)
                            ->tel(),
                        Textarea::make('alasan')
                            ->label('Alasan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Data User')
                    ->schema([
                        Select::make('project')
                            ->label('Project')
                            ->options(fn () => Project::query()
                                ->orderBy('name')
                                ->pluck('name', 'name')
                            )
                            ->searchable()
                            ->required(),
                        TextInput::make('user')
                            ->label('User'),
                        TextInput::make('no_hp_user')
                            ->label('No. HP User')
                            ->maxLength(15)
                            ->tel(),
                    ])
                    ->columns(3),

                Section::make('Detail')
                    ->schema([
                        Textarea::make('lokasi')
                            ->label('Lokasi')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('unit_id')
                            ->label('Unit')
                            ->relationship('unit', 'nopol')
                            ->searchable()
                            ->required(),
                        TextInput::make('kunci_unit')
                            ->label('Kunci Unit'),
                        DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai'),
                        DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->afterOrEqual('tanggal_mulai'),
                        TimePicker::make('jam_standby_mulai')
                            ->label('Jam Standby Mulai')
                            ->required(),
                        TimePicker::make('jam_standby_selesai')
                            ->label('Jam Standby Selesai'),
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Driver Pengganti')
                    ->schema([
                        TextInput::make('driver_pengganti')
                            ->label('Nama Driver Pengganti')
                            ->required(),
                        TextInput::make('no_hp_pengganti')
                            ->label('No. HP Pengganti')
                            ->maxLength(15)
                            ->tel()
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }
}

