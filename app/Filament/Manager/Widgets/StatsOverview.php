<?php

namespace App\Filament\Manager\Widgets;

use App\Models\Pengajuan;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $csCount = Pengajuan::whereIn('keterangan_proses', ['cs', 'otorisasi'])->count();
        $financeCount = Pengajuan::whereIn('keterangan_proses', ['pengajuan finance', 'finance'])->count();
        $doneCount = Pengajuan::where('keterangan_proses', 'done')->count();

        return [
            Stat::make('Customer Service', $csCount)
                ->description('Jumlah pengajuan di tahap CS')
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->color('primary'),
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
