<?php

namespace App\Filament\Resources\Bastks\Pages;

use App\Filament\Resources\Bastks\BastkLamaResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBastkLama extends ViewRecord
{
    protected static string $resource = BastkLamaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print_bastk')
                ->label('Print BASTK')
                ->icon('heroicon-o-printer')
                ->url(fn($record) => route('print.bastk', $record->id))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
