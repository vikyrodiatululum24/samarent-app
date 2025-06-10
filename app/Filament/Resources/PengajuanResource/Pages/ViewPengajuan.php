<?php

namespace App\Filament\Resources\PengajuanResource\Pages;

use App\Filament\Resources\PengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPengajuan extends ViewRecord
{
    protected static string $resource = PengajuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('print_spk')
                    ->label('Preview')
                    ->icon('heroicon-o-printer')
                    ->url(fn($record) => route('preview', $record->id))
                    ->openUrlInNewTab(),
                Actions\Action::make('print_spk')
                    ->label('Download SPK')
                    ->icon('heroicon-o-printer')
                    ->url(fn($record) => route('print.spk', $record->id))
                    ->openUrlInNewTab()
                    ->badge(fn($record) => \App\Models\Cetak::where('pengajuan_id', $record->id)->exists() ? 'Sudah di-print' : null)
            ])
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('primary')
        ];
    }
}
