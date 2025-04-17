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
                    ->label('Print SPK')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('print.spk', $record->id))
                    ->openUrlInNewTab(),
                Actions\Action::make('print_sjp')
                    ->label('Print SJP')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('print.sjp', $record->id))
                    ->openUrlInNewTab(),
                Actions\Action::make('print_lampiran')
                    ->label('Print Lampiran')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('print.lampiran', $record->id))
                    ->openUrlInNewTab(),
                Actions\Action::make('print_lampiran2')
                    ->label('Print Lampiran 2')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('print.lampiran2', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->label('Print')
            ->icon('heroicon-o-printer')
            ->color('primary')
        ];
    }
}
