<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProsesResource\Pages;
use App\Filament\Resources\ProsesResource\RelationManagers;
use App\Models\Complete;
use App\Models\Proses;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class ProsesResource extends Resource
{
    protected static ?string $model = Complete::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'Customer Service';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Informasi Bengkel')
                    ->schema([
                        Forms\Components\Hidden::make('user_id')
                            ->default(\Illuminate\Support\Facades\Auth::user()->id),
                        Forms\Components\TextInput::make('bengkel_estimasi')
                            ->label('Bengkel Estimasi')
                            ->required()
                            ->default(fn($record) => $record->complete?->bengkel_estimasi),
                        Forms\Components\TextInput::make('no_telp_bengkel')
                            ->label('No. Telp Bengkel')
                            ->required()
                            ->default(fn($record) => $record->complete?->no_telp_bengkel),
                        Forms\Components\TextInput::make('nominal_estimasi')
                            ->label('Nominal Estimasi')
                            ->numeric()
                            ->required()
                            ->default(fn($record) => $record->complete?->nominal_estimasi),
                    ])
                    ->columns(2),
                Forms\Components\Fieldset::make('Informasi Pengajuan')
                    ->schema([
                        Forms\Components\Select::make('kode')
                            ->label('Kode')
                            ->options([
                                'op' => 'OP',
                                'sc' => 'SC',
                                'sp' => 'SP',
                            ])
                            ->required()
                            ->default(fn($record) => $record->complete?->kode),
                    ])
                    ->columns(1),
                Forms\Components\Fieldset::make('Informasi Finance')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_masuk_finance')
                            ->label('Tanggal Masuk Finance')
                            ->required()
                            ->default(fn($record) => $record->complete?->tanggal_masuk_finance),
                        Forms\Components\DatePicker::make('tanggal_tf_finance')
                            ->label('Tanggal Transfer Finance')
                            // ->required()
                            ->default(fn($record) => $record->complete?->tanggal_tf_finance),
                        Forms\Components\TextInput::make('nominal_tf_finance')
                            ->label('Nominal Transfer Finance')
                            ->numeric()
                            // ->required()
                            ->default(fn($record) => $record->complete?->nominal_tf_finance),
                        Forms\Components\TextInput::make('payment_2')
                            ->label('Nama Rekening')
                            // ->required()
                            ->default(fn($record) => $record->complete?->payment_2),
                        Forms\Components\TextInput::make('bank_2')
                            ->label('Bank')
                            // ->required()
                            ->default(fn($record) => $record->complete?->bank_2),
                        Forms\Components\TextInput::make('norek_2')
                            ->label('No. Rekening')
                            // ->required()
                            ->default(fn($record) => $record->complete?->norek_2),
                        Forms\Components\Select::make('status_finance')
                            ->label('Status Finance')
                            ->options([
                                'paid' => 'Paid',
                                'unpaid' => 'Unpaid',
                            ])
                            ->required()
                            ->default(fn($record) => $record->complete?->status_finance ?? 'unpaid'),
                    ])
                    ->columns(2),
                Forms\Components\Fieldset::make('Transfer Bengkel')
                    ->schema([
                        Forms\Components\TextInput::make('nominal_tf_bengkel')
                            ->label('Nominal Transfer Bengkel')
                            ->numeric()
                            ->nullable()
                            ->default(fn($record) => $record->complete?->nominal_tf_bengkel),
                        Forms\Components\TextInput::make('selisih_tf')
                            ->label('Selisih Transfer')
                            ->numeric()
                            ->default(fn($record) => $record->complete?->selisih_tf ?? 0)
                            ->reactive()
                            ->afterStateUpdated(fn(callable $set, $state, callable $get) => $set('selisih_tf', $get('nominal_tf_finance') - $get('nominal_tf_bengkel'))),
                        Forms\Components\DatePicker::make('tanggal_tf_bengkel')
                            ->label('Tanggal Transfer Bengkel')
                            ->nullable()
                            ->default(fn($record) => $record->complete?->tanggal_tf_bengkel),
                        Forms\Components\DatePicker::make('tanggal_pengerjaan')
                            ->label('Tanggal Pengerjaan')
                            ->nullable()
                            ->default(fn($record) => $record->complete?->tanggal_pengerjaan),
                    ])
                    ->columns(2),
                Forms\Components\Fieldset::make('Dokumentasi')
                    ->schema([
                        Forms\Components\FileUpload::make('foto_nota')
                            ->label('Foto Nota')
                            ->disk('public')
                            ->directory('foto_nota')
                            ->nullable()
                            ->default(fn($record) => $record->complete?->foto_nota),
                        Forms\Components\FileUpload::make('foto_pengerjaan_bengkel')
                            ->label('Foto Pengerjaan Bengkel')
                            ->disk('public')
                            ->directory('foto_pengerjaan_bengkel')
                            ->nullable()
                            ->default(fn($record) => $record->complete?->foto_pengerjaan_bengkel),
                        Forms\Components\FileUpload::make('foto_tambahan')
                            ->label('Foto Tambahan')
                            ->disk('public')
                            ->directory('foto_tambahan')
                            ->multiple()
                            ->maxFiles(3)
                            ->nullable()
                            ->default(fn($record) => $record->complete?->foto_tambahan)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire) {
                                if (count($state) > 3) {
                                    $set('foto_tambahan', array_slice($state, 0, 3));
                                }
                                $record = $livewire->record ?? null;
                                if ($record && is_array($record->complete->foto_tambahan)) {
                                    $lama = collect($record->complete->foto_tambahan);
                                    $baru = collect($state);
                                    $yangDihapus = $lama->diff($baru);
                                    foreach ($yangDihapus as $path) {
                                        Storage::disk('public')->delete($path);
                                    }
                                }
                            }),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pengajuan.no_pengajuan')
                    ->label('No Pengajuan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pengajuan.nama')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('bengkel_estimasi')
                    ->label('Bengkel')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_finance')
                    ->label('Status')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return match ($record->status_finance) {
                            'unpaid' => 'Unpaid',
                            'padi' => 'Paid',
                            default => 'Tidak Diketahui',
                        };
                    })
                    ->color(fn(string $state) => match ($state) {
                        'Unpaid' => 'gray',
                        'Paid' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProses::route('/'),
            'create' => Pages\CreateProses::route('/create'),
            'edit' => Pages\EditProses::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Menghilangkan tombol create (newPengajuan)
    }
}
