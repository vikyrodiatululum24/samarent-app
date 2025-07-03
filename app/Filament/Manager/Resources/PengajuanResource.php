<?php

namespace App\Filament\Manager\Resources;

use App\Filament\Manager\Resources\PengajuanResource\Pages;
use App\Filament\Manager\Resources\PengajuanResource\RelationManagers;
use App\Models\Pengajuan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components; // Added this line to fix undefined Components
use Filament\Infolists\Components\ViewEntry; // Added this line to fix undefined ViewEntry
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class PengajuanResource extends Resource
{
    protected static ?string $model = Pengajuan::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->searchable()
                    ->label('Tanggal Pengajuan')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('no_pengajuan')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('nama')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('service_unit')
                    ->label('Service')
                    ->getStateUsing(function ($record) {
                        // Ambil semua service yang berelasi dengan pengajuan ini
                        $services = $record->service_unit()->with('unit')->get();
                        // Format: [nama_service (nopol)], dipisah baris baru
                        return $services->map(function ($service) {
                            return "{$service->service}";
                        })->implode('<br>');
                    })
                    ->html()
                    ->searchable(query: function (Builder $query, string $search) {
                        // Join ke tabel service_unit dan unit, lalu filter berdasarkan nama service atau nopol
                        $query->whereHas('service_unit.unit', function ($q) use ($search) {
                            $q->where('service', 'like', "%{$search}%");
                        });
                    }),
                Tables\Columns\TextColumn::make('nopol')
                    ->label('No. Polisi')
                    ->getStateUsing(function ($record) {
                        // Ambil semua service yang berelasi dengan pengajuan ini
                        $services = $record->service_unit()->with('unit')->get();
                        // Format: [nama_service (nopol)], dipisah baris baru
                        return $services->map(function ($service) {
                            $nopol = $service->unit?->nopol ?? '-';
                            return "{$nopol}";
                        })->implode('<br>');
                    })
                    ->html()
                    ->searchable(query: function (Builder $query, string $search) {
                        // Join ke tabel service_unit dan unit, lalu filter berdasarkan nama service atau nopol
                        $query->whereHas('service_unit.unit', function ($q) use ($search) {
                            $q->where('nopol', 'like', "%{$search}%");
                        });
                    }),

                Tables\Columns\TextColumn::make('up')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('keterangan_proses')
                    ->label('Status Proses')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state) => match (true) {
                        str_contains(strtoupper($state), 'CUSTOMER SERVICE') => 'gray',
                        str_contains(strtoupper($state), 'CHECKER') => 'success',
                        str_contains(strtoupper($state), 'PENGAJUAN FINANCE') => 'primary',
                        str_contains(strtoupper($state), 'INPUT FINANCE') => 'warning',
                        str_contains(strtoupper($state), 'OTORISASI') => 'warning',
                        str_contains(strtoupper($state), 'SELESAI') => 'success',
                        default => 'gray',
                    })
                    ->getStateUsing(function ($record) {
                        return match ($record->keterangan_proses) {
                            'cs' => 'Customer Service',
                            'checker' => 'Checker',
                            'pengajuan finance' => 'Pengajuan Finance',
                            'finance' => 'Input Finance',
                            'otorisasi' => 'Otorisasi',
                            'done' => 'Selesai',
                            default => 'Tidak Diketahui',
                        };
                    }),
            ])
            ->filters([
                //
            ]);
        // ->actions([
        //     Tables\Actions\EditAction::make(),
        // ])
        // ->bulkActions([
        //     Tables\Actions\BulkActionGroup::make([
        //         Tables\Actions\DeleteBulkAction::make(),
        //     ]),
        // ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Informasi Umum')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\Group::make([
                                    Components\TextEntry::make('nama'),
                                    Components\TextEntry::make('no_wa'),
                                ]),
                                Components\Group::make([
                                    Components\TextEntry::make('keterangan_proses')
                                        ->label('Status Proses')
                                        ->badge()
                                        ->getStateUsing(function ($record) {
                                            return match ($record->keterangan_proses) {
                                                'cs' => 'Customer Service',
                                                'checker' => 'Checker',
                                                'pengajuan finance' => 'Pengajuan Finance',
                                                'finance' => 'Finance',
                                                'otorisasi' => 'Otorisasi',
                                                'done' => 'Selesai',
                                                default => 'Tidak Diketahui',
                                            };
                                        })
                                        ->color(fn(string $state) => match ($state) {
                                            'Customer Service' => 'primary',
                                            'Checker' => 'danger',
                                            'Pengajuan Finance' => 'info',
                                            'Finance' => 'warning',
                                            'Otorisasi' => 'danger',
                                            'Selesai' => 'success',
                                            default => 'gray',
                                        }),
                                    Components\TextEntry::make('created_at')
                                        ->label('Tanggal Pengajuan')
                                        ->dateTime()
                                        ->getStateUsing(fn($record) => $record->created_at->format('d M Y H:i:s')),
                                ]),
                            ]),
                    ]),

                Components\Section::make('Detail Kendaraan')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\Group::make([
                                    Components\TextEntry::make('jenis'),
                                    Components\TextEntry::make('type'),
                                    Components\TextEntry::make('nopol'),
                                    Components\TextEntry::make('odometer'),
                                    Components\TextEntry::make('service'),
                                ]),
                                Components\Group::make([
                                    Components\TextEntry::make('project'),
                                    Components\TextEntry::make('keterangan'),
                                    Components\TextEntry::make('up'),
                                    Components\TextEntry::make('provinsi'),
                                    Components\TextEntry::make('kota'),
                                ]),
                            ]),
                    ]),

                Components\Section::make('Pembayaran')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('payment_1'),
                                Components\TextEntry::make('bank_1'),
                                Components\TextEntry::make('norek_1'),
                            ]),
                    ]),
                Components\Section::make('Dokumentasi')
                    ->schema([
                        ViewEntry::make('foto_unit')
                            ->label('Foto Unit')
                            ->view('filament.components.foto-unit')
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                            ]),

                        ViewEntry::make('foto_odometer')
                            ->label('Foto Odometer')
                            ->view('filament.components.foto-odometer')
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                            ]),

                        ViewEntry::make('foto_kondisi')
                            ->label('Foto Kondisi')
                            ->view('filament.components.foto-kondisi')
                            ->columnSpan([
                                'default' => 4,
                                'md' => 3,
                            ]),
                    ])
                    ->columns([
                        'default' => 4,
                        'md' => 5,
                    ]),
                Components\Section::make('Informasi Complete')
                    ->schema([
                        Components\Grid::make(1)
                            ->schema([
                                Components\Group::make([
                                    Components\TextEntry::make('complete.kode')
                                        ->label('Kode'),
                                ]),
                            ]),
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('complete.bengkel_estimasi')
                                    ->label('Bengkel Estimasi'),
                                Components\TextEntry::make('complete.no_telp_bengkel')
                                    ->label('No. Telp Bengkel'),
                                Components\TextEntry::make('complete.nominal_estimasi')
                                    ->label('Nominal Estimasi'),
                            ])
                            ->label('Informasi Bengkel'),
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('complete.tanggal_masuk_finance')
                                    ->label('Tanggal Masuk Finance')
                                    ->dateTime(),
                                Components\TextEntry::make('complete.tanggal_tf_finance')
                                    ->label('Tanggal Transfer Finance')
                                    ->dateTime(),
                                Components\TextEntry::make('complete.nominal_tf_finance')
                                    ->label('Nominal Transfer Finance'),
                            ]),
                        Components\Grid::make(1)
                            ->schema([
                                Components\Group::make([
                                    Components\TextEntry::make('complete.payment_2')
                                        ->label('Nama Rekening'),
                                ]),
                            ]),
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('complete.bank_2')
                                    ->label('Bank'),
                                Components\TextEntry::make('complete.norek_2')
                                    ->label('No. Rekening'),
                                Components\TextEntry::make('complete.status_finance')
                                    ->label('Status Finance'),
                            ]),
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('complete.nominal_tf_bengkel')
                                    ->label('Nominal Transfer Bengkel'),
                                Components\TextEntry::make('complete.selisih_tf')
                                    ->label('Selisih Transfer'),
                                Components\TextEntry::make('complete.tanggal_tf_bengkel')
                                    ->label('Tanggal Transfer Bengkel')
                                    ->dateTime(),
                                Components\TextEntry::make('complete.tanggal_pengerjaan')
                                    ->label('Tanggal Pengerjaan')
                                    ->dateTime(),
                            ]),
                    ])
                    ->visible(fn($record) => !empty($record->complete)), // Only show when complete data is filled
                Components\Section::make('Dokumentasi Complete')
                    ->schema([
                        ViewEntry::make('complete.foto_nota')
                            ->label('Foto Nota')
                            ->view('filament.components.foto-nota')
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                            ]),
                        ViewEntry::make('complete.foto_pengerjaan_bengkel')
                            ->label('Foto Pengerjaan Bengkel')
                            ->view('filament.components.foto-pengerjaan-bengkel')
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                            ]),
                        ViewEntry::make('complete.foto_tambahan')
                            ->label('Foto Tambahan')
                            ->view('filament.components.foto-tambahan')
                            ->columnSpan([
                                'default' => 4,
                                'md' => 3,
                            ]),
                        ViewEntry::make('finance.bukti_transaksi')
                            ->label('Bukti Transaksi')
                            ->view('filament.components.bukti_transaksi')
                            ->columnSpan([
                                'default' => 4,
                                'md' => 3,
                            ]),
                    ])
                    ->columns([
                        'default' => 4,
                        'md' => 5,
                    ])
                    ->visible(fn($record) => !empty($record->complete)),
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
            'index' => Pages\ListPengajuans::route('/'),
            // 'view' => Pages\ViewPengajuan::route('/{record}'),
            // 'create' => Pages\CreatePengajuan::route('/create'),
            // 'edit' => Pages\EditPengajuan::route('/{record}/edit'),
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

    public static function canCreate(): bool
    {
        return false; // Menghilangkan tombol create (newPengajuan)
    }

    // public static function shouldRegisterNavigation(): bool
    // {
    //     return false;
    // }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        // Ambil data manager terkait user
        $manager = $user->manager;
        // Jika user adalah centralakun@samarent.com, tampilkan semua data
        if ($user->email === 'centralakun@samarent.com') {
            return parent::getEloquentQuery();
        }

        // Jika tidak ada manager, kembalikan query kosong
        if (!$manager) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        // Ambil UP dan project yang dimiliki user (manager)
        $ups = array_filter([$manager->up]);
        $projects = array_filter([$manager->perusahaan]);

        $query = parent::getEloquentQuery();

        if (!empty($ups) && !empty($projects)) {
            // Jika user punya keduanya, filter berdasarkan up dan project
            $query->whereIn('up', $ups)
                ->whereIn('project', $projects);
        } elseif (!empty($ups)) {
            // Jika hanya punya up
            $query->whereIn('up', $ups);
        } else {
            // Tidak punya keduanya, kembalikan query kosong
            $query->whereRaw('1 = 0');
        }

        return $query;
    }
}
