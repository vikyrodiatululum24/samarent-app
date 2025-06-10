<?php

namespace App\Filament\User\Resources\PengajuanResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
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
            null => Tab::make('All'),
            'OP' => Tab::make()->query(fn ($query) => $query->whereHas('complete', fn ($q) => $q->where('kode', 'op'))),
            'SC' => Tab::make()->query(fn ($query) => $query->whereHas('complete', fn ($q) => $q->where('kode', 'sc'))),
            'SP' => Tab::make()->query(fn ($query) => $query->whereHas('complete', fn ($q) => $q->where('kode', 'sp'))),
            'STNK' => Tab::make()->query(fn ($query) => $query->whereHas('complete', fn ($q) => $q->where('kode', 'stnk'))),
        ];
    }
}