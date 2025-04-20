<?php

namespace App\Filament\Widgets;

use App\Models\Pengajuan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $csCount = Pengajuan::where('keterangan_proses', 'cs')->count();
        $financeCount = Pengajuan::where('keterangan_proses', 'finance')->count();
        $doneCount = Pengajuan::where('keterangan_proses', 'done')->count();

        return [
            Stat::make('Customer Service', $csCount)
                ->description('Jumlah pengajuan di tahap CS')
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->color('primary')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('setStatusFilter', { filter: 'cs' })",
                ]),
            Stat::make('Finance', $financeCount)
                ->description('Jumlah pengajuan di tahap Finance')
                ->descriptionIcon('heroicon-m-currency-dollar', IconPosition::Before)
                ->color('warning'),
            Stat::make('Done', $doneCount)
                ->description('Jumlah pengajuan selesai')
                ->descriptionIcon('heroicon-m-check-circle', IconPosition::Before)
                ->color('success'),
        ];
    }
}
