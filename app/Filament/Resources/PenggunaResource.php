<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenggunaResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PenggunaResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Pengguna';

    // protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
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
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('role')
                    ->form([
                        Forms\Components\Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'user' => 'User',
                            ]),
                    ])
                    ->query(fn($query, $data) => $query->when(
                        $data['role'] ?? null,
                        fn($query, $role) => $query->where('role', $role)
                    )),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
        return auth()->user()?->name === 'admin';
    }

    public static function canView($record): bool
    {
        return auth()->user()?->name === 'admin';
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->name === 'admin';
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->name === 'admin';
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->name === 'admin';
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
