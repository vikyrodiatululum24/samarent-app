<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormTugasResource\Pages;
use App\Filament\Resources\FormTugasResource\RelationManagers;
use App\Models\FormTugas;
use App\Models\Unit;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class FormTugasResource extends Resource
{
    protected static ?string $model = FormTugas::class;

    protected static ?string $navigationLabel = 'Form Tugas Keluar';

    protected static ?string $modelLabel = 'Form Tugas';

    protected static ?string $pluralModelLabel = 'Form Tugas';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Utama')
                    ->description('Detail informasi form tugas')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\TextInput::make('no_form')
                            ->label('No. Form')
                            ->disabled()
                            ->dehydrated()
                            ->default(fn () => self::generateNoForm())
                            ->maxLength(255)
                            ->placeholder('Auto-generated')
                            ->helperText('Nomor form akan dibuat otomatis')
                            ->columnSpan(1),

                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),

                        Forms\Components\TextInput::make('nama_atasan')
                            ->label('Nama Atasan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan nama atasan')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('pemohon')
                            ->label('Pemohon')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan nama pemohon')
                            ->columnSpan(1),

                        Forms\Components\TagsInput::make('penerima_tugas')
                            ->label('Yang Ditugaskan')
                            ->required()
                            ->placeholder('Tambahkan nama penerima tugas')
                            ->helperText('Tekan Enter untuk menambahkan penerima tugas'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Periode Tugas')
                    ->description('Tanggal mulai dan selesai tugas')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->seconds(false)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->seconds(false)
                            ->after('tanggal_mulai')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Deskripsi & Unit')
                    ->description('Detail tugas dan unit yang digunakan')
                    ->icon('heroicon-o-truck')
                    ->schema([
                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi Tugas')
                            ->rows(4)
                            ->placeholder('Jelaskan detail tugas yang akan dilakukan')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('unit_id')
                            ->label('Unit Kendaraan')
                            ->options(function () {
                                return Unit::query()
                                    ->orderBy('nopol')
                                    ->get()
                                    ->mapWithKeys(function ($unit) {
                                        return [$unit->id => $unit->nopol . ' - ' . $unit->merk . ' ' . $unit->type];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder('Cari berdasarkan nopol')
                            ->helperText('Pilih unit kendaraan yang akan digunakan')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('lainnya')
                            ->label('Lainnya')
                            ->maxLength(255)
                            ->placeholder('Informasi tambahan lainnya')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('sopir')
                            ->label('Sopir')
                            ->maxLength(255)
                            ->placeholder('Nama sopir yang ditugaskan')
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make('Biaya Operasional')
                    ->description('Rincian biaya perjalanan dinas')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\TextInput::make('bbm')
                            ->label('BBM')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::updateTotal($set, $get))
                            ->placeholder('0')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('toll')
                            ->label('Toll')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::updateTotal($set, $get))
                            ->placeholder('0')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('penginapan')
                            ->label('Penginapan')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::updateTotal($set, $get))
                            ->placeholder('0')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('uang_dinas')
                            ->label('Uang Dinas')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::updateTotal($set, $get))
                            ->placeholder('0')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('entertaint_customer')
                            ->label('Entertaint Customer')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::updateTotal($set, $get))
                            ->placeholder('0')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('total')
                            ->label('Total Biaya')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated()
                            ->default(0)
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Tujuan Tugas')
                    ->description('Tambahkan tujuan dan lokasi tugas')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Repeater::make('tujuanTugas')
                            ->relationship()
                            ->schema([
                                Forms\Components\DatePicker::make('tanggal')
                                    ->label('Tanggal')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->columnSpan([
                                        'sm' => 2,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                Forms\Components\TextInput::make('tempat')
                                    ->label('Tempat')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Nama tempat tujuan')
                                    ->columnSpan([
                                        'sm' => 2,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                Forms\Components\TextInput::make('location')
                                    ->label('Lokasi')
                                    ->maxLength(255)
                                    ->placeholder('Alamat lengkap')
                                    ->columnSpan([
                                        'sm' => 2,
                                        'md' => 2,
                                        'lg' => 2,
                                    ]),

                                Forms\Components\Textarea::make('keterangan')
                                    ->label('Keterangan')
                                    ->rows(2)
                                    ->placeholder('Keterangan tambahan')
                                    ->columnSpanFull(),
                            ])
                            ->columns([
                                'sm' => 2,
                                'md' => 2,
                                'lg' => 2,
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('Tambah Tujuan')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['tempat'] ?? 'Tujuan Baru')
                            ->deleteAction(
                                fn ($action) => $action->requiresConfirmation()
                            )
                            ->columnSpanFull()
                    ])
                    ->collapsible(),
            ]);
    }

    protected static function updateTotal(callable $set, callable $get): void
    {
        $bbm = (float) ($get('bbm') ?? 0);
        $toll = (float) ($get('toll') ?? 0);
        $penginapan = (float) ($get('penginapan') ?? 0);
        $uangDinas = (float) ($get('uang_dinas') ?? 0);
        $entertaint = (float) ($get('entertaint_customer') ?? 0);

        $total = $bbm + $toll + $penginapan + $uangDinas + $entertaint;

        $set('total', $total);
    }

    protected static function generateNoForm(): string
    {
        $date = now()->format('Ymd');
        $lastForm = FormTugas::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastForm && str_starts_with($lastForm->no_form, $date)) {
            $lastNumber = (int) substr($lastForm->no_form, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_form')
                    ->label('No. Form')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->copyMessage('No. Form disalin'),

                Tables\Columns\TextColumn::make('nama_atasan')
                    ->label('Atasan')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('pemohon')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('unit.nopol')
                    ->label('Unit')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($record) => $record->unit?->nopol . ' - ' . $record->unit?->merk),

                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Mulai')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->label('Selesai')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total Biaya')
                    ->money('IDR')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('warning'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('unit_id')
                    ->label('Unit')
                    ->options(function () {
                        return Unit::query()
                            ->orderBy('nopol')
                            ->get()
                            ->mapWithKeys(function ($unit) {
                                return [$unit->id => $unit->nopol];
                            });
                    })
                    ->searchable(),

                Tables\Filters\Filter::make('tanggal_mulai')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_mulai', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_mulai', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->color('warning'),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListFormTugas::route('/'),
            'create' => Pages\CreateFormTugas::route('/create'),
            'view' => Pages\ViewFormTugas::route('/{record}'),
            'edit' => Pages\EditFormTugas::route('/{record}/edit'),
        ];
    }
}
