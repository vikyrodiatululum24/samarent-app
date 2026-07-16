<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReimbursementMonitorResource\Pages;
use App\Models\Reimbursement;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ReimbursementMonitorResource extends Resource
{
    protected static ?string $model = Reimbursement::class;
    protected static ?string $slug = 'monitor-reimbursement';
    protected static string | \UnitEnum | null $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Monitor Reimbursement';
    protected static ?string $modelLabel = 'Monitor Reimbursement';
    protected static ?string $pluralModelLabel = 'Monitor Reimbursement';

    public static function shouldRegisterNavigation(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'admin';
    }

    public static function canViewAny(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'admin';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
        ->paginated([10, 25, 50, 100])
            ->modifyQueryUsing(fn (Builder $query) => $query)
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('km_awal')
                    ->label('KM Awal')
                    ->sortable()
                    ->suffix(' KM'),

                Tables\Columns\TextColumn::make('km_akhir')
                    ->label('KM Akhir')
                    ->sortable()
                    ->suffix(' KM')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('tujuan_perjalanan')
                    ->label('Tujuan')
                    ->searchable()
                    ->limit(30)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('dana_masuk')
                    ->label('Dana Masuk')
                    ->money('IDR')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('dana_keluar')
                    ->label('Dana Keluar')
                    ->money('IDR')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diupdate')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->native(false),
                        DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['dari'])->format('d/m/Y');
                        }
                        if ($data['sampai'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['sampai'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('print_pdf')
                        ->label('Cetak PDF')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->action(function (Collection $records) {
                        $ids = $records->pluck('id')->toArray();
                        return redirect()->route('reimbursement.monitoring-print-pdf', ['ids' => implode(',', $ids)]);
                    })
                    ->openUrlInNewTab(),

                    BulkAction::make('export_excel')
                        ->label('Export Excel')
                        ->icon('heroicon-o-document-text')
                        ->color('success')
                        ->action(function (Collection $records) {
                        $ids = $records->pluck('id')->toArray();
                        return redirect()->route('reimbursement.monitoring-export-excel', ['ids' => implode(',', $ids)]);
                    })
                    ->openUrlInNewTab(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonitorReimbursements::route('/'),
            'view' => Pages\ViewMonitorReimbursement::route('/{record}'),
        ];
    }
}
