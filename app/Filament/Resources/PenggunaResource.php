<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenggunaResource\Pages;
use App\Models\Project;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Schemas\Components\Utilities\Set;

class PenggunaResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Pengguna';

    protected static string | \UnitEnum | null $navigationGroup = 'Pengaturan';

    // protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->label('Password')
                    ->maxLength(255)
                    ->required(fn($context) => $context === 'create') // Wajib hanya saat create
                    ->dehydrateStateUsing(function ($state) {
                        return filled($state) ? bcrypt($state) : null; // Enkripsi jika ada input
                    })
                    // ->afterStateHydrated(function ($component) {
                    //     $component->state(''); // Kosongkan field saat edit agar tidak tampil hash
                    // })
                    ->dehydrated(fn($state) => filled($state)) // Hanya simpan ke DB jika diisi
                    ->disableAutocomplete(),

                Forms\Components\Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'user' => 'User',
                        'finance' => 'Finance',
                        'manager' => 'Manager',
                        'asuransi' => 'Asuransi',
                        'driver' => 'Driver',
                        'admin_driver' => 'Admin Driver',
                        'admin_jual' => 'Admin Penjualan',
                        'president' => 'President',
                        'mekanik' => 'Mekanik',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('role')->sortable(),
                Tables\Columns\TextColumn::make('manager.perusahaan')->label('Perusahaan')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('manager.up')->label('Unit Pelaksana')->searchable()
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('role')
                    ->form([
                        Forms\Components\Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'user' => 'User',
                                'finance' => 'Finance',
                                'manager' => 'Manager',
                                'asuransi' => 'Asuransi',
                                'driver' => 'Driver',
                                'admin_driver' => 'Admin Driver',
                                'admin_jual' => 'Admin Penjualan',

                            ]),
                    ])
                    ->query(fn($query, $data) => $query->when(
                        $data['role'] ?? null,
                        fn($query, $role) => $query->where('role', $role)
                    )),
            ])
            ->actions([
                EditAction::make(),
                Action::make('manager')
                    ->icon('heroicon-o-cog')
                    ->label('Set Project')
                    ->visible(fn($record) => $record->role === 'manager')
                    ->modalHeading('Isi Form Manager')
                    ->modalSubmitActionLabel('Simpan')
                    ->form(function ($record) {
                        $manager = $record->manager; // sekarang hasOne, bukan collection lagi

                        return [
                            Forms\Components\Select::make('up')
                                ->required()
                                ->label('Unit Pelaksana')
                                ->options([
                                    'UP 1' => 'UP 1',
                                    'UP 2' => 'UP 2',
                                    'UP 3' => 'UP 3',
                                    'UP 5' => 'UP 5',
                                    'UP 7' => 'UP 7',
                                    'CUST JEPANG' => 'CUST JEPANG',
                                    'manual' => 'Lainnya',
                                ])
                                ->multiple()
                                ->reactive()
                                ->afterStateUpdated(fn(Set $set, $state) => $set('up_lainnya', $state === 'manual' ? '' : null))
                                ->default($manager?->up),

                            Forms\Components\Select::make('perusahaan')
                                ->required()
                                ->label('Perusahaan')
                                ->options(Project::pluck('name', 'name')->toArray())
                                ->searchable()
                                ->default($manager?->perusahaan),
                        ];
                    })
                    ->action(function (array $data, $record, $action) {
                        $record->manager()->updateOrCreate(
                            ['user_id' => $record->id], // kondisi untuk update jika sudah ada
                            [
                                'up' => $data['up'],
                                'perusahaan' => $data['perusahaan'],
                            ]
                        );

                        $action->success('Data manager berhasil disimpan.');
                    }),
                // Tables\Actions\Action::make('delete-manager') // tombol hapus
                //     ->color('danger')
                //     ->label('')
                //     ->icon('heroicon-o-trash')
                //     ->visible(fn($record) => $record->role === 'manager' && $record->manager !== null)
                //     ->requiresConfirmation()
                //     ->deselectRecordsAfterCompletion()
                //     ->modalHeading('Unmanage')
                //     ->modalSubheading('Apakah Anda yakin ingin Unmanage? Tindakan ini tidak dapat dibatalkan.')
                //     ->modalButton('Ya, Hapus Data')
                //     ->action(function ($record, $action) {
                //         $record->manager?->delete();
                //         $action->success('Data manager berhasil dihapus.');
                //     }),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define relations if needed
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Pengguna'; // singular
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengguna'; // tetap singular
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->email === 'centralakun@samarent.com';
    }

    public static function canView($record): bool
    {
        return Auth::user()?->email === 'centralakun@samarent.com';
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->email === 'centralakun@samarent.com';
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->email === 'centralakun@samarent.com';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenggunas::route('/'),
            'create' => Pages\CreatePengguna::route('/create'),
            'edit' => Pages\EditPengguna::route('/{record}/edit'),
        ];
    }
}
