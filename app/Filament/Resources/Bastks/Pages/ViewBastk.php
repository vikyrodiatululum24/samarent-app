<?php

namespace App\Filament\Resources\Bastks\Pages;

use App\Filament\Resources\Bastks\BastkResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBastk extends ViewRecord
{
    protected static string $resource = BastkResource::class;

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
