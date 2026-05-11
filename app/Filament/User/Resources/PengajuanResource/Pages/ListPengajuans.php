<?php

namespace App\Filament\User\Resources\PengajuanResource\Pages;

use Filament\Actions;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\User\Resources\PengajuanResource;

class ListPengajuans extends ListRecords
{
    protected static string $resource = PengajuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

public function getTabs(): array
{
    return [
        'all' => Tab::make('All'),

        'OP' => Tab::make('OP')
            ->modifyQueryUsing(fn ($query) =>
                $query->whereHas('complete', fn ($q) => $q->where('kode', 'op'))
            ),

        'SC' => Tab::make('SC')
            ->modifyQueryUsing(fn ($query) =>
                $query->whereHas('complete', fn ($q) => $q->where('kode', 'sc'))
            ),

        'SP' => Tab::make('SP')
            ->modifyQueryUsing(fn ($query) =>
                $query->whereHas('complete', fn ($q) => $q->where('kode', 'sp'))
            ),

        'STNK' => Tab::make('STNK')
            ->modifyQueryUsing(fn ($query) =>
                $query->whereHas('complete', fn ($q) => $q->where('kode', 'stnk'))
            ),
    ];
}
}
