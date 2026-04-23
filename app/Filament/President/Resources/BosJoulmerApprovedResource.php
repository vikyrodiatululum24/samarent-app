<?php

namespace App\Filament\President\Resources;

use App\Filament\President\Resources\BosJoulmerApprovedResource\Pages;
use App\Models\BosJoulmer;
use Filament\Facades\Filament;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BosJoulmerApprovedResource extends Resource
{
    protected static ?string $model = BosJoulmer::class;

    protected static ?string $navigationGroup = 'Pengajuan';

    protected static ?string $navigationLabel = 'Pengajuan Disetujui';

    protected static ?string $label = 'Pengajuan Disetujui';

    protected static ?string $pluralLabel = 'Pengajuan Disetujui';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'pengajuan-disetujui';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pengajuan.no_pengajuan')
                    ->label('No. Pengajuan')
                    ->sortable()
                    ->searchable(),
                    Tables\Columns\TextColumn::make('pengajuan.up')
                        ->label('UP')
                        ->sortable()
                        ->searchable(query: function (Builder $query, string $search) {
                            $query->whereHas('pengajuan', function (Builder $q) use ($search) {
                                $q->where('up', 'like', "%{$search}%");
                            });
                        })
                        ->getStateUsing(function (BosJoulmer $record) {
                            if ($record->pengajuan?->up === 'manual') {
                                return $record->pengajuan->up_lainnya ?? '-';
                            } else{
                                return $record->pengajuan?->up ?? '-';
                            }
                        })
                        ->label('Unit Pelaksana')
                        ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('pengajuan.project')
                        ->label('Project')
                        ->sortable()
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nopol')
                    ->label('No. Polisi')
                    ->getStateUsing(function (BosJoulmer $record) {
                        return $record->pengajuan?->service_unit
                            ?->map(fn($service) => $service->unit?->nopol ?? '-')
                            ->implode('<br>') ?? '-';
                    })
                    ->html()
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('pengajuan.service_unit.unit', function (Builder $q) use ($search) {
                            $q->where('nopol', 'like', "%{$search}%");
                        });
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis Kendaraan')
                    ->getStateUsing(function (BosJoulmer $record) {
                        return $record->pengajuan?->service_unit
                            ?->map(fn($service) => $service->unit?->jenis ?? '-')
                            ->implode('<br>') ?? '-';
                    })
                    ->html()
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('pengajuan.service_unit.unit', function (Builder $q) use ($search) {
                            $q->where('jenis', 'like', "%{$search}%");
                        });
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('service')
                    ->label('Service')
                    ->getStateUsing(function (BosJoulmer $record) {
                        return $record->pengajuan?->service_unit
                            ?->map(fn($service) => $service->service ?? '-')
                            ->implode('<br>') ?? '-';
                    })
                    ->html()
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('pengajuan.service_unit', function (Builder $q) use ($search) {
                            $q->where('service', 'like', "%{$search}%");
                        });
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pengajuan.complete.nominal_estimasi')
                    ->label('Nominal Estimasi')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->money('idr', true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('pengajuan.keterangan_proses')
                    ->label('Status Pengajuan')
                    ->badge()
                    ->getStateUsing(function (BosJoulmer $record) {
                        return match ($record->pengajuan?->keterangan_proses) {
                            'cs' => 'Customer Service',
                            'checker' => 'Verifikasi',
                            'pengajuan atasan' => 'Pengajuan Atasan',
                            'pengajuan finance' => 'Pengajuan Finance',
                            'finance' => 'Input Finance',
                            'otorisasi' => 'Otorisasi',
                            'done' => 'Selesai',
                            default => 'Tidak Diketahui',
                        };
                    })
                    ->color(fn(string $state) => match ($state) {
                        'Customer Service' => 'black',
                        'Verifikasi' => 'danger',
                        'Pengajuan Atasan' => 'info',
                        'Pengajuan Finance' => 'primary',
                        'Input Finance' => 'brown',
                        'Otorisasi' => 'yellow',
                        'Selesai' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('is_approved')
                    ->label('Status Review')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => ucfirst($state))
                    ->color(fn(string $state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('note')
                    ->label('Catatan')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(60),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tanggal Review')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('updated_at', 'desc');
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_approved', 'approved')
            ->whereHas('pengajuan', function (Builder $q) {
                $q->where('keterangan_proses', 'pengajuan atasan');
            })
            ->with(['pengajuan.service_unit.unit', 'user']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function getNavigationGroup(): ?string
    {
        return Filament::getCurrentPanel()?->getId() === 'president' ? null : static::$navigationGroup;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBosJoulmersApproved::route('/'),
            'view' => Pages\ViewBosJoulmerApproved::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::$model::where('is_approved', 'approved')->whereHas('pengajuan', function (Builder $q) {
            $q->where('keterangan_proses', 'pengajuan atasan');
        })->count();
    }
}
