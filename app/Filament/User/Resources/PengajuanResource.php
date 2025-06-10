<?php

namespace App\Filament\User\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Pengajuan;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Hidden;
use Filament\Infolists\Components\Grid;
use Illuminate\Support\Facades\Storage;
use Filament\Infolists\Components\Group;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use App\Filament\User\Resources\PengajuanResource\Pages;
use App\Filament\User\Resources\PengajuanResource\RelationManagers;
use App\Filament\Resources\PengajuanResource\RelationManagers\ServiceUnitRelationManager;

class PengajuanResource extends Resource
{
    protected static ?string $model = Pengajuan::class;

    protected static ?string $slug = 'pengajuan';

    protected static ?string $navigationLabel = 'Pengajuan';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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
                                    ->label('Nama PIC')
                                    ->required()
                                    ->afterStateUpdated(fn($component, $state) => $component->state(strtoupper($state)))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('no_wa')
                                    ->label('No. WhatsApp')
                                    ->required()
                                    ->numeric()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('project')
                                ->required()
                                ->afterStateUpdated(fn($component, $state) => $component->state(strtoupper($state)))
                                ->maxLength(255),
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
                                ->visible(fn(callable $get) => $get('up') === 'manual')
                                ->afterStateUpdated(fn($component, $state) => $component->state(strtoupper($state))),
                            Forms\Components\TextInput::make('provinsi')
                                ->required()
                                ->afterStateUpdated(fn($component, $state) => $component->state(strtoupper($state)))
                                ->maxLength(255),
                            Forms\Components\TextInput::make('kota')
                                ->required()
                                ->afterStateUpdated(fn($component, $state) => $component->state(strtoupper($state)))
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
                                'reimburse' => 'REIMBURSE',
                                'cash advance' => 'CASH ADVANCE',
                                'invoice' => 'INVOICE',
                                'free' => 'FREE',
                            ])
                            ->reactive(),
                        Forms\Components\TextInput::make('payment_1')
                            ->nullable()
                            ->label('Nama Rekening')
                            ->required(fn(callable $get) => $get('keterangan') === 'REIMBURSE')
                            ->maxLength(255)
                            ->disabled(fn(callable $get) => $get('keterangan') !== 'REIMBURSE'),
                        Forms\Components\TextInput::make('bank_1')
                            ->nullable()
                            ->label('Bank')
                            ->required(fn(callable $get) => $get('keterangan') === 'REIMBURSE')
                            ->maxLength(255)
                            ->disabled(fn(callable $get) => $get('keterangan') !== 'REIMBURSE'),
                        Forms\Components\TextInput::make('norek_1')
                            ->nullable()
                            ->label('No. Rekening')
                            ->required(fn(callable $get) => $get('keterangan') === 'REIMBURSE')
                            ->numeric()
                            ->maxLength(255)
                            ->disabled(fn(callable $get) => $get('keterangan') !== 'REIMBURSE'),
                    ])
                    ->columns(2)
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Umum')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Group::make([
                                    TextEntry::make('nama'),
                                    TextEntry::make('no_wa'),
                                ]),
                                Group::make([
                                    TextEntry::make('keterangan_proses')
                                        ->label('Status Proses')
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
                                        ->color(fn(string $state) => match ($state) {
                                            'Customer Service' => 'gray',
                                            'Pengajuan Finance' => 'primary',
                                            'Finance' => 'warning',
                                            'Otorisasi' => 'warning',
                                            'Selesai' => 'success',
                                            default => 'gray',
                                        }),
                                    TextEntry::make('created_at')
                                        ->label('Tanggal Pengajuan')
                                        ->dateTime()
                                        ->getStateUsing(fn($record) => $record->created_at->format('d M Y H:i:s')),
                                ]),
                                Group::make([
                                    TextEntry::make('project'),
                                    TextEntry::make('keterangan'),
                                    TextEntry::make('up'),
                                    TextEntry::make('provinsi'),
                                    TextEntry::make('kota'),
                                ]),
                            ])
                    ]),
                Section::make('Pembayaran')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('payment_1'),
                                TextEntry::make('bank_1'),
                                TextEntry::make('norek_1'),
                            ]),
                    ]),
                Section::make('Detail Kendaraan')
                    ->schema([
                        ViewEntry::make('service_unit.pengajuan_id')
                            ->label('Detail Kendaraan')
                            ->view('filament.resources.pages.pengajuan.detail-kendaraan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewPengajuan::class,
            Pages\EditPengajuan::class,
            // Pages\ProsesPengajuans::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            ServiceUnitRelationManager::class,
        ];
    }

    public static function getGlobalSearchResultUrl(\Illuminate\Database\Eloquent\Model $record): string
    {
        return PengajuanResource::getUrl('view', ['record' => $record]);
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
