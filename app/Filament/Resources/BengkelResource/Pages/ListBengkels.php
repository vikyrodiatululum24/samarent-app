<?php

namespace App\Filament\Resources\BengkelResource\Pages;

use App\Filament\Resources\BengkelResource;
use App\Models\Bengkel;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBengkels extends ListRecords
{
    protected static string $resource = BengkelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge(Bengkel::count()),

            'has_maps' => Tab::make('Dengan Google Maps')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('g_maps'))
                ->badge(Bengkel::whereNotNull('g_maps')->count()),

            'no_maps' => Tab::make('Tanpa Google Maps')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('g_maps'))
                ->badge(Bengkel::whereNull('g_maps')->count()),
        ];
    }
}
