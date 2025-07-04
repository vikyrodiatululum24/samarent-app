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
                        str_contains(strtoupper($state), 'INPUT FINANCE') => 'brown',
                        str_contains(strtoupper($state), 'OTORISASI') => 'yellow',
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
