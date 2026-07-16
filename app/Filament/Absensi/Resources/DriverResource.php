<?php

namespace App\Filament\Absensi\Resources;

use App\Filament\Absensi\Resources\DriverResource\Pages;
use App\Filament\Absensi\Resources\DriverResource\RelationManagers;
use App\Filament\Manager\Resources\DriverResource\RelationManagers\ReimbursementsRelationManager;
use App\Models\Branch;
use App\Models\Division;
use App\Models\Driver;
use App\Models\SetSalary;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Image;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Project Management';

    protected static ?string $pluralModelLabel = 'Driver';

    public static function form(Schema $schema): Schema
    {
        return $schema
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
                    ->unique(Driver::class, 'nik', ignoreRecord: true)
                    ->maxLength(16)
                    ->minLength(16)
                    ->rules(['regex:/^[0-9]{16}$/'])
                    ->validationMessages([
                        'regex' => 'Nomor NIK harus terdiri dari 16 digit angka.',
                        'unique' => 'Nomor NIK sudah terdaftar.',
                    ]),
                Forms\Components\TextInput::make('sim')
                    ->label('SIM')
                    ->unique(Driver::class, 'sim', ignoreRecord: true)
                    ->rules(['regex:/^[0-9]{12,14}$/'])
                    ->validationMessages([
                        'regex' => 'Nomor SIM harus terdiri dari 12 hingga 14 digit angka.',
                        'unique' => 'Nomor SIM sudah terdaftar.',
                    ])
                    ->maxLength(14)
                    ->minLength(12),
                Forms\Components\TextInput::make('no_wa')
                    ->required()
                    ->label('No. WhatsApp')
                    ->rules(['regex:/^08[0-9]{8,11}$/'])
                    ->validationMessages([
                        'regex' => 'Nomor WhatsApp harus diawali dengan "08" dan terdiri dari 10 hingga 13 digit angka.',
                    ])
                    ->maxLength(13)
                    ->minLength(11),
                Forms\Components\TextInput::make('alamat')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('rt')
                    ->maxLength(3)
                    ->label('RT')
                    ->rules(['regex:/^[0-9]{1,3}$/'])
                    ->validationMessages([
                        'regex' => 'RT harus berupa angka dan maksimal 3 digit.',
                    ])
                    ->default(null),
                Forms\Components\TextInput::make('rw')
                    ->maxLength(3)
                    ->label('RW')
                    ->rules(['regex:/^[0-9]{1,3}$/'])
                    ->validationMessages([
                        'regex' => 'RW harus berupa angka dan maksimal 3 digit.',
                    ])
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
                    ->default(null)
                    ->required(),
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
                    ->label('Jenis Kelamin')
                    ->options([
                        'laki-laki' => 'Laki-laki',
                        'perempuan' => 'Perempuan',
                    ])
                    ->default(null),
                Forms\Components\Select::make('project_id')
                    ->label('Penempatan')
                    ->relationship('project', 'name', fn (\Illuminate\Database\Eloquent\Builder $query) => $query->whereNotNull('name'))
                    ->searchable()
                    ->default(null)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('branch_id', null);
                        $set('division_id', null);
                    }),
                Forms\Components\Select::make('branch_id')
                    ->label('Branch')
                    ->options(fn(Get $get) => Branch::query()
                        ->whereNotNull('name')
                        ->when($get('project_id'), fn($query, $projectId) => $query->where('project_id', $projectId))
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->disabled(fn(Get $get) => blank($get('project_id')))
                    ->default(null)
                    ->live()
                    ->afterStateUpdated(fn(Set $set) => $set('division_id', null)),
                Forms\Components\Select::make('division_id')
                    ->label('Division')
                    ->options(fn(Get $get) => Division::query()
                        ->whereNotNull('name')
                        ->when($get('branch_id'), fn($query, $branchId) => $query->where('branch_id', $branchId))
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->disabled(fn(Get $get) => blank($get('branch_id')))
                    ->live()
                    ->afterStateUpdated(fn(Set $set) => $set('set_salary_id', null))
                    ->default(null),
                Forms\Components\Select::make('set_salary_id')
                    ->label('Set Salary')
                    ->options(fn(Get $get) => SetSalary::query()
                        ->whereNotNull('name')
                        ->when($get('division_id'), fn($query, $divisionId) => $query->where('division_id', $divisionId))
                        ->active()
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->default(null)
                    ->helperText('Jika dipilih, ini akan menjadi override SetSalary untuk driver ini.')
                    ->disabled(fn(Get $get) => blank($get('division_id'))),
                Forms\Components\TextInput::make('pic')
                    ->label('PIC')
                    ->required(),
                Forms\Components\FileUpload::make('photo')
                    ->image()
                    ->label('Foto Driver')
                    ->maxWidth('1080')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imagePreviewHeight('250')
                    ->directory('driver-photos')
                    ->visibility('public')
                    ->resize(50)
                    ->default(null)
                    ->helperText('Maksimal ukuran file 1MB. Disarankan ukuran foto 1:1 (persegi).'),
            ])
            ->columns(2);
    }


    public static function table(Table $table): Table
    {
        return $table
        ->paginated([10, 25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Driver')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nik')
                ->label('NIK')
                ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sim')
                    ->label('SIM')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->label('Alamat')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_wa')
                    ->label('No. WhatsApp')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tempat')
                    ->label('Tempat Lahir')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->date()
                    ->label('Tanggal Lahir')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('rt')
                    ->label('RT')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('rw')
                    ->label('RW')                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelurahan')
                    ->label('Kelurahan/Desa')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('kecamatan')
                    ->label('Kecamatan')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('agama')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Agama'),
                Tables\Columns\TextColumn::make('photo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Photo Driver'),
                Tables\Columns\TextColumn::make('project.name')
                    ->searchable()
                    ->label('Penempatan')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('branch.name')
                    ->searchable()
                    ->label('Branch')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('division.name')
                    ->searchable()
                    ->label('Divisi')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('pic')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Akun')
                    ->schema([
                        TextEntry::make('name_driver')
                            ->label('Nama Driver')
                            ->getStateUsing(fn($record) => $record?->user?->name ?? '-'),
                        TextEntry::make('email_driver')
                            ->label('Email')
                            ->getStateUsing(fn($record) => $record?->user?->email ?? '-'),
                        TextEntry::make('password')
                            ->label('Password')
                            ->copyable()
                    ])
                    ->columns(3),
                Section::make('Identitas')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('no_wa')
                            ->label('No. WhatsApp'),
                        TextEntry::make('nik')
                            ->label('NIK'),
                        TextEntry::make('sim')
                            ->label('SIM'),
                        TextEntry::make('jenis_kelamin')
                            ->label('Jenis Kelamin'),
                        TextEntry::make('tempat')
                            ->label('Tempat Lahir'),
                        TextEntry::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->getStateUsing(fn($record) => filled($record?->tanggal_lahir) ? $record->tanggal_lahir->format('d m Y') : '-'),
                        TextEntry::make('agama')
                            ->label('Agama'),
                        Image::make(
                            fn($record) => filled($record?->photo) ? asset('storage/' . ltrim($record->photo, '/')) : asset('images/placeholder.png'),
                            'Foto Driver'
                        ),
                        TextEntry::make('alamat')
                            ->label('Alamat'),
                        TextEntry::make('rt')
                            ->label('RT'),
                        TextEntry::make('rw')
                            ->label('RW'),
                        TextEntry::make('kelurahan')
                            ->label('Kelurahan/Desa'),
                        TextEntry::make('kecamatan')
                            ->label('Kecamatan'),
                    ]),
                Section::make('Informasi Penempatan')
                    ->schema([
                        TextEntry::make('project.name')
                            ->label('Penempatan'),
                        TextEntry::make('branch.name')
                            ->label('Branch'),
                        TextEntry::make('division.name')
                            ->label('Division'),
                        TextEntry::make('set_salary')
                            ->label('Set Salary')
                            ->getStateUsing(fn($record) => $record?->currentSetSalary()?->name ?? ($record?->setSalary?->name ?? '-')),
                        TextEntry::make('pic')
                            ->label('PIC'),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DriverAttendenceRelationManager::class,
            RelationManagers\OvertimePaysRelationManager::class,
            ReimbursementsRelationManager::class,
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
