<?php

namespace App\Filament\Absensi\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Driver;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Absensi\Resources\DriverResource\Pages;
use App\Filament\Absensi\Resources\DriverResource\RelationManagers;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;
    protected static ?string $pluralModelLabel = 'Driver';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user.name')
                    ->label('Nama Driver')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('user.email')
                    ->email()
                    ->label('Email Driver')
                    ->required()
                    ->unique(
                        table: User::class,
                        column: 'email',
                        ignorable: fn($record) => $record?->user
                    )
                    ->validationMessages(['email' => 'Email sudah terdaftar.'])
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->label('Password Driver')
                    ->required()
                    ->revealable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nik')
                    ->label('NIK')
                    ->required()
                    ->numeric()
                    ->unique(Driver::class, 'nik', ignoreRecord: true)
                    ->maxLength(16)
                    ->minLength(16)
                    ->validationMessages([
                        'regex' => 'Nomor NIK harus terdiri dari 16 digit angka.',
                        'unique' => 'Nomor NIK sudah terdaftar.',
                    ])
                    ->rules(['regex:/^[0-9]{16}$/']),
                Forms\Components\TextInput::make('sim')
                    ->label('SIM')
                    ->required()
                    ->numeric()
                    ->unique(Driver::class, 'sim', ignoreRecord: true)
                    ->rules(['regex:/^[0-9]{12,14}$/'])
                    ->validationMessages([
                        'regex' => 'Nomor SIM harus terdiri dari 12 hingga 14 digit angka.',
                        'unique' => 'Nomor SIM sudah terdaftar.',
                    ])
                    ->maxLength(14)
                    ->minLength(12),
                Forms\Components\TextInput::make('alamat')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_wa')
                    ->required()
                    ->label('No. WhatsApp')
                    ->numeric()
                    ->rules(['regex:/^08[0-9]{8,11}$/'])
                    ->validationMessages([
                        'regex' => 'Nomor WhatsApp harus diawali dengan "08" dan terdiri dari 10 hingga 13 digit angka.',
                    ])
                    ->maxLength(13)
                    ->minLength(11),
                Forms\Components\TextInput::make('tempat')
                    ->maxLength(255)
                    ->label('Tempat Lahir')
                    ->default(null),
                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->default(null)
                    ->required()
                    ->label('Tanggal Lahir'),
                Forms\Components\Select::make('jenis_kelamin')
                    ->required()
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan',
                    ]),
                Forms\Components\TextInput::make('rt')
                    ->maxLength(3)
                    ->label('RT')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('rw')
                    ->maxLength(3)
                    ->label('RW')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('kelurahan')
                    ->maxLength(255)
                    ->label('Kelurahan/Desa')
                    ->default(null),
                Forms\Components\TextInput::make('kecamatan')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Select::make('agama')
                    ->options([
                        'Islam' => 'Islam',
                        'Kristen' => 'Kristen',
                        'Katolik' => 'Katolik',
                        'Hindu' => 'Hindu',
                        'Buddha' => 'Buddha',
                        'Konghucu' => 'Konghucu',
                    ])
                    ->default(null),
                Forms\Components\FileUpload::make('photo')
                    ->image()
                    ->label('Foto Driver')
                    ->maxSize(1024)
                    ->maxWidth('1080')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imagePreviewHeight('250')
                    ->directory('driver-photos')
                    ->visibility('public')
                    ->resize(50)
                    ->default(null)
                    ->helperText('Maksimal ukuran file 1MB. Disarankan ukuran foto 1:1 (persegi).'),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->label('Nama Driver')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sim')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_wa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tempat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin'),
                Tables\Columns\TextColumn::make('rt')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rw')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelurahan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kecamatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('agama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('photo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Akun')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')->label('Nama Driver'),
                        Infolists\Components\TextEntry::make('user.email')->label('Email'),
                        Infolists\Components\TextEntry::make('password')->label('Password'),
                    ])
                    ->columns(3),
                Infolists\Components\Section::make('Identitas')
                    ->columns(2)
                    ->inlineLabel()
                    ->schema([
                        Infolists\Components\TextEntry::make('no_wa')->label('No. WhatsApp')->placeholder('Untitled'),
                        Infolists\Components\TextEntry::make('nik')->label('NIK'),
                        Infolists\Components\TextEntry::make('sim')->label('SIM'),
                        Infolists\Components\TextEntry::make('jenis_kelamin')->label('Jenis Kelamin'),
                        Infolists\Components\TextEntry::make('tempat')->label('Tempat Lahir'),
                        Infolists\Components\TextEntry::make('tanggal_lahir')->label('Tanggal Lahir')->date(),
                        Infolists\Components\TextEntry::make('agama')->label('Agama'),
                        Infolists\Components\ImageEntry::make('photo')->label('Foto Driver')->hiddenLabel(),
                    ]),
                Infolists\Components\Section::make('Alamat')
                    ->schema([
                        Infolists\Components\TextEntry::make('alamat')->label('Alamat'),
                        Infolists\Components\TextEntry::make('rt')->label('RT'),
                        Infolists\Components\TextEntry::make('rw')->label('RW'),
                        Infolists\Components\TextEntry::make('kelurahan')->label('Kelurahan/Desa'),
                        Infolists\Components\TextEntry::make('kecamatan')->label('Kecamatan'),
                    ])
                    ->inlineLabel()
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DriverAttendenceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
            'view' => Pages\ViewDriver::route('/{record}'),
        ];
    }
}
