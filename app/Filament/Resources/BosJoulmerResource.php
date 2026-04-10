<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BosJoulmerResource\Pages;
use App\Models\BosJoulmer;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BosJoulmerResource extends Resource
{
    protected static ?string $model = BosJoulmer::class;

    protected static ?string $navigationGroup = 'Pengajuan';

    protected static ?string $navigationLabel = 'Review Atasan';

    protected static ?string $label = 'Review Atasan';

    protected static ?string $pluralLabel = 'Review Atasan';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'review-atasan';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pengajuan.no_pengajuan')
                    ->label('No. Pengajuan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pengajuan.nama')
                    ->label('Nama PIC')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pengajuan.project')
                    ->label('Project')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis Kendaraan')
                    ->getStateUsing(function (BosJoulmer $record) {
                        return $record->pengajuan?->service_unit
                            ?->map(fn ($service) => $service->unit?->jenis ?? '-')
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
                            ?->map(fn ($service) => $service->service ?? '-')
                            ->implode('<br>') ?? '-';
                    })
                    ->html()
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('pengajuan.service_unit', function (Builder $q) use ($search) {
                            $q->where('service', 'like', "%{$search}%");
                        });
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nopol')
                    ->label('No. Polisi')
                    ->getStateUsing(function (BosJoulmer $record) {
                        return $record->pengajuan?->service_unit
                            ?->map(fn ($service) => $service->unit?->nopol ?? '-')
                            ->implode('<br>') ?? '-';
                    })
                    ->html()
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('pengajuan.service_unit.unit', function (Builder $q) use ($search) {
                            $q->where('nopol', 'like', "%{$search}%");
                        });
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pengajuan.keterangan_proses')
                    ->label('Status Pengajuan')
                    ->badge()
                    ->getStateUsing(function (BosJoulmer $record) {
                        return match ($record->pengajuan?->keterangan_proses) {
                            'cs' => 'Customer Service',
                            'checker' => 'Verifikasi',
                            'menunggu atasan' => 'Menunggu Atasan',
                            'pengajuan finance' => 'Pengajuan Finance',
                            'finance' => 'Input Finance',
                            'otorisasi' => 'Otorisasi',
                            'done' => 'Selesai',
                            default => 'Tidak Diketahui',
                        };
                    })
                    ->color(fn (string $state) => match ($state) {
                        'Customer Service' => 'black',
                        'Verifikasi' => 'danger',
                        'Menunggu Atasan' => 'info',
                        'Pengajuan Finance' => 'primary',
                        'Input Finance' => 'brown',
                        'Otorisasi' => 'yellow',
                        'Selesai' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('is_approved')
                    ->label('Status Review')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->color(fn (string $state) => match ($state) {
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
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
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
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('id', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Data Pengajuan')
                    ->schema([
                        Components\TextEntry::make('pengajuan.no_pengajuan')->label('No. Pengajuan'),
                        Components\TextEntry::make('pengajuan.nama')->label('Nama PIC'),
                        Components\TextEntry::make('pengajuan.project')->label('Project'),
                        Components\TextEntry::make('pengajuan.keterangan')->label('Keterangan'),
                        Components\TextEntry::make('pengajuan.keterangan_proses')
                            ->label('Status Pengajuan')
                            ->badge()
                            ->getStateUsing(function (BosJoulmer $record) {
                                return match ($record->pengajuan?->keterangan_proses) {
                                    'cs' => 'Customer Service',
                                    'checker' => 'Verifikasi',
                                    'menunggu atasan' => 'Menunggu Atasan',
                                    'pengajuan finance' => 'Pengajuan Finance',
                                    'finance' => 'Input Finance',
                                    'otorisasi' => 'Otorisasi',
                                    'done' => 'Selesai',
                                    default => 'Tidak Diketahui',
                                };
                            })
                            ->color(fn (string $state) => match ($state) {
                                'Customer Service' => 'black',
                                'Verifikasi' => 'danger',
                                'Menunggu Atasan' => 'info',
                                'Pengajuan Finance' => 'primary',
                                'Input Finance' => 'brown',
                                'Otorisasi' => 'yellow',
                                'Selesai' => 'success',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),
                Components\Section::make('Review Bos')
                    ->schema([
                        Components\TextEntry::make('is_approved')
                            ->label('Status Review')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => ucfirst($state))
                            ->color(fn (string $state) => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            }),
                        Components\TextEntry::make('note')
                            ->label('Catatan')
                            ->placeholder('-'),
                        Components\TextEntry::make('user.name')
                            ->label('Reviewer'),
                        Components\TextEntry::make('updated_at')
                            ->label('Waktu Review')
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['pengajuan.service_unit.unit', 'user']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function canViewAny(): bool
    {
        return static::hasBosAccess();
    }

    public static function canView($record): bool
    {
        return static::hasBosAccess();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return static::hasBosAccess();
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
        return (string) static::$model::where('is_approved', 'pending')->count();
    }

    protected static function canReview(BosJoulmer $record): bool
    {
        return static::hasBosAccess() && $record->is_approved === 'pending';
    }

    public static function canReviewRecord(BosJoulmer $record): bool
    {
        return static::canReview($record);
    }

    protected static function hasBosAccess(): bool
    {
        return in_array(Auth::user()?->email, [
            'president@samarent.com',
            'centralakun@samarent.com',
        ], true);
    }
}
