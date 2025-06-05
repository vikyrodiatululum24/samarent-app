<?php

namespace App\Filament\Resources\PengajuanResource\Pages;

use App\Filament\Resources\PengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPengajuan extends ViewRecord
{
    protected static string $resource = PengajuanResource::class;
    // protected static string $view = 'filament.resources.pages.pengajuan.detail-kendaraan';

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
                // Actions\Action::make('print_sjp')
                //     ->label('Print SJP')
                //     ->icon('heroicon-o-printer')
                //     ->url(fn ($record) => route('print.sjp', $record->id))
                //     ->openUrlInNewTab(),
                // Actions\Action::make('print_lampiran')
                //     ->label('Print Lampiran')
                //     ->icon('heroicon-o-printer')
                //     ->url(fn ($record) => route('print.lampiran', $record->id))
                //     ->openUrlInNewTab(),
                // Actions\Action::make('print_lampiran2')
                //     ->label('Print Lampiran 2')
                //     ->icon('heroicon-o-printer')
                //     ->url(fn ($record) => route('print.lampiran2', $record->id))
                //     ->openUrlInNewTab(),
            ])
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('primary')
        ];
    }
}
