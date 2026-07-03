<?php

namespace App\Filament\President\Resources;

use App\Filament\President\Resources\BosJoulmerResource\Pages;
use App\Models\BosJoulmer;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BosJoulmerResource extends Resource
{
    protected static ?string $model = BosJoulmer::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Pengajuan';

    protected static ?string $navigationLabel = 'Review Atasan';

    protected static ?string $label = 'Review Atasan';

    protected static ?string $pluralLabel = 'Review Atasan';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'review-atasan';

    public static function table(Table $table): Table
    {
        return $table
        ->paginated([10, 25, 50, 100])
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
                        } else {
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
            ])
            ->filters([
                SelectFilter::make('is_approved')
                    ->label('Status Review')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('id', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('is_approved', ['pending', 'rejected'])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBosJoulmers::route('/'),
            'view' => Pages\ViewBosJoulmer::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::$model::where('is_approved', 'pending')
        ->whereHas('pengajuan', function (Builder $q) {
            $q->where('keterangan_proses', 'pengajuan atasan');
        })
        ->count();
    }

    protected static function canReview(BosJoulmer $record): bool
    {
        return $record->is_approved === 'pending';
    }

    public static function canReviewRecord(BosJoulmer $record): bool
    {
        return static::canReview($record);
    }
}
