<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArchivePengajuanResource\Pages;
use App\Models\Pengajuan;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArchivePengajuanResource extends PengajuanResource
{
    protected static ?string $model = Pengajuan::class;
    protected static ?string $slug = 'archive-pengajuan';
    protected static string | \UnitEnum | null $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Archive';
    protected static ?string $pluralLabel = 'Archive';
    protected static ?string $modelLabel = 'Archive';
    protected static ?int $navigationSort = 99;

    public static function canAccess(): bool
    {
        return auth()->user()->email == 'centralakun@samarent.com';
    }

    public static function getEloquentQuery(): Builder
    {
        // Panggil query bawaan model tanpa whereNull('hidden_at') yang ada di parent
        return Pengajuan::query()->whereNotNull('hidden_at');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->actions([
                Action::make('unhide')
                    ->label('Unhide')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Pengajuan $record) => $record->update(['hidden_at' => null]))
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('unhide')
                        ->label('Unhide Selected')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['hidden_at' => null]))
                ]),
            ])
            ->headerActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArchivePengajuans::route('/'),
            'view' => Pages\ViewArchivePengajuan::route('/{record}'),
        ];
    }

    public static function getRecordSubNavigation(\Filament\Resources\Pages\Page $page): array
    {
        return [];
    }
}
