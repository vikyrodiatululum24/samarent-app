<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\PengajuanResource\Pages;
use App\Filament\User\Resources\PengajuanResource\RelationManagers;
use App\Models\Pengajuan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Hidden;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Filament\Facades\Filament;

class PengajuanResource extends Resource
{
    protected static ?string $model = Pengajuan::class;

    protected static ?string $slug = 'pengajuan';

    protected static ?string $navigationLabel = 'Pengajuan';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Informasi Umum')
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Hidden::make('user_id')
                                    ->default(\Illuminate\Support\Facades\Auth::user()->id),
                                Forms\Components\TextInput::make('nama')
                                    ->required()
                                    ->label('Nama PIC')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('no_wa')
                                    ->required()
                                    ->label('No. WhatsApp')
                                    ->numeric()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\TextInput::make('jenis')
                                    ->required()
                                    ->label('Jenis Kendaraan')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('type')
                                    ->required()
                                    ->label('Tipe Unit')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('nopol')
                                    ->required()
                                    ->label('Nomor Polisi')
                                    ->placeholder('Tanpa Spasi')
                                    ->maxLength(255),
                            ])
                    ])
                    ->columns(2)
                    ->columnSpan('full')
                    ->extraAttributes(['class' => 'mb-4']),

                Forms\Components\Fieldset::make('Detail Kendaraan')
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\TextInput::make('odometer')
                                    ->required()
                                    ->numeric()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('service')
                                    ->required()
                                    ->label('Jenis Permintaan Service')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('project')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Group::make([
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
                                ->reactive()
                                ->afterStateUpdated(fn(callable $set, $state) => $set('up_lainnya', $state === 'manual' ? '' : null)),
                            Forms\Components\TextInput::make('up_lainnya')
                                ->label('Unit Pelaksana Lainnya')
                                ->required(fn(callable $get) => $get('up') === 'manual')
                                ->visible(fn(callable $get) => $get('up') === 'manual'),
                            Forms\Components\TextInput::make('provinsi')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('kota')
                                ->required()
                                ->maxLength(255),
                        ])
                    ])
                    ->columns(2)
                    ->columnSpan('full')
                    ->extraAttributes(['class' => 'mb-4']),

                Forms\Components\Fieldset::make('Pembayaran')
                    ->schema([
                        Forms\Components\Select::make('keterangan')
                            ->required()
                            ->label('Jenis Pengajuan')
                            ->options([
                                'Reimburse' => 'Reimburse',
                                'cash advance' => 'Cash Advance',
                                'invoice' => 'Invoice',
                                'free' => 'Free',
                            ])
                            ->reactive(),
                        Forms\Components\TextInput::make('payment_1')
                            ->nullable()
                            ->label('Nama Rekening')
                            ->required(fn(callable $get) => $get('keterangan') === 'Reimburse')
                            ->maxLength(255)
                            ->disabled(fn(callable $get) => $get('keterangan') !== 'Reimburse'),
                        Forms\Components\TextInput::make('bank_1')
                            ->nullable()
                            ->label('Bank')
                            ->required(fn(callable $get) => $get('keterangan') === 'Reimburse')
                            ->maxLength(255)
                            ->disabled(fn(callable $get) => $get('keterangan') !== 'Reimburse'),
                        Forms\Components\TextInput::make('norek_1')
                            ->nullable()
                            ->label('No. Rekening')
                            ->required(fn(callable $get) => $get('keterangan') === 'Reimburse')
                            ->numeric()
                            ->maxLength(255)
                            ->disabled(fn(callable $get) => $get('keterangan') !== 'Reimburse'),
                    ])
                    ->columns(2)
                    ->columnSpan('full')
                    ->extraAttributes(['class' => 'mb-4']),

                Forms\Components\Fieldset::make('Dokumentasi')
                    ->schema([
                        Forms\Components\FileUpload::make('foto_unit')
                            ->image()
                            ->required()
                            ->disk('public')
                            ->directory('foto_unit') // akan simpan di storage/app/public/foto_unit
                            ->visibility('public')
                            ->afterStateUpdated(function ($state, callable $get, callable $set, $livewire) {
                                $record = $livewire->record ?? null; // Ensure $record is available
                                if ($record && $record->foto_unit && $record->foto_unit !== $state) {
                                    Storage::disk('public')->delete($record->foto_unit);
                                }
                            }),
                        Forms\Components\FileUpload::make('foto_odometer')
                            ->image()
                            ->required()
                            ->disk('public')
                            ->directory('foto_odometer') // simpan di storage/app/public/foto_odometer
                            ->visibility('public')
                            ->afterStateUpdated(function ($state, callable $get, callable $set, $livewire) {
                                $record = $livewire->record ?? null; // Ensure $record is available
                                if ($record && $record->foto_odometer && $record->foto_odometer !== $state) {
                                    Storage::disk('public')->delete($record->foto_odometer);
                                }
                            }),
                        Forms\Components\FileUpload::make('foto_kondisi')
                            ->required()
                            ->disk('public')
                            ->directory('foto_kondisi') // simpan di storage/app/public/foto_kondisi
                            ->image()
                            ->multiple()
                            ->maxFiles(3) // Add validation to limit the number of files to 3
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire) {
                                if (count($state) > 3) {
                                    $set('foto_kondisi', array_slice($state, 0, 3)); // Limit to 3 files
                                }
                                $record = $livewire->record ?? null; // Ensure $record is available
                                if ($record && is_array($record->foto_kondisi)) {
                                    $lama = collect($record->foto_kondisi);
                                    $baru = collect($state);
                                    $yangDihapus = $lama->diff($baru);
                                    foreach ($yangDihapus as $path) {
                                        Storage::disk('public')->delete($path);
                                    }
                                }
                            }),
                    ])
                    ->columns(3)
                    ->columnSpan('full')
                    ->extraAttributes(['class' => 'mb-4']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_pengajuan')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->searchable()
                    ->label('Tanggal Pengajuan')
                    ->date('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('User'),
                Tables\Columns\TextColumn::make('nama')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Nama PIC'),
                Tables\Columns\TextColumn::make('nopol')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('No. Polisi'),
                Tables\Columns\TextColumn::make('jenis')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Jenis Kendaraan'),
                Tables\Columns\TextColumn::make('type')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Type Unit'),
                Tables\Columns\TextColumn::make('service')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Permintaan Service'),
                Tables\Columns\TextColumn::make('project')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Project'),
                Tables\Columns\TextColumn::make('up')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true)->label('Unit Pelaksana'),
                Tables\Columns\TextColumn::make('complete.nominal_estimasi')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Nominal Estimasi')
                    ->formatStateUsing(fn($state) => $state !== null ? 'Rp ' . number_format($state, 0, ',', '.') : '-'),
                Tables\Columns\TextColumn::make('keterangan')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('keterangan_proses')
                    ->label('Status Proses')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return match ($record->keterangan_proses) {
                            'cs' => 'Customer Service',
                            'pengajuan finance' => 'Pengajuan Finance',
                            'finance' => 'Input Finance',
                            'otorisasi' => 'Otorisasi',
                            'done' => 'Selesai',
                            default => 'Tidak Diketahui',
                        };
                    })
                    ->color(fn(string $state) => match (true) {
                        str_contains($state, 'Customer Service') => 'gray',
                        str_contains($state, 'Pengajuan Finance') => 'primary',
                        str_contains($state, 'Input Finance') => 'warning',
                        str_contains($state, 'Otorisasi') => 'warning',
                        str_contains($state, 'Selesai') => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('User'),
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

    public static function getRelations(): array
    {
        return [
            // Define any relations if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengajuans::route('/'),
            'create' => Pages\CreatePengajuan::route('/create'),
            'edit' => Pages\EditPengajuan::route('/{record}/edit'),
            'view' => Pages\ViewPengajuan::route('/{record}'),
        ];
    }

    public static function getGlobalSearchResultUrl(\Illuminate\Database\Eloquent\Model $record): string
    {
        return PengajuanResource::getUrl('view', ['record' => $record]);
    }

    public static function getModelLabel(): string
    {
        return 'Pengajuan'; // singular
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengajuan'; // tetap singular
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Filament::auth()->id());
    }
}
