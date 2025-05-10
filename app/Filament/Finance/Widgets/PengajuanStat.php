<?php

namespace App\Filament\Finance\Widgets;

use App\Models\Pengajuan;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PengajuanStat extends BaseWidget
{
    protected function getStats(): array
    {
        $csCount = Pengajuan::where('keterangan_proses', 'pengajuan finance')->count();
        $financeCount = Pengajuan::where('keterangan_proses', 'finance')->count();
        $doneCount = Pengajuan::where('keterangan_proses', 'done')->count();

        return [
            Stat::make('Customer Service', $csCount)
                ->description('Jumlah Pengajuan Finance')
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->color('primary'),
            Stat::make('Finance', $financeCount)
                ->description('Jumlah Proses Finance')
                ->descriptionIcon('heroicon-m-currency-dollar', IconPosition::Before)
                ->color('warning'),
            Stat::make('Done', $doneCount)
                ->description('Jumlah pengajuan selesai')
                ->descriptionIcon('heroicon-m-check-circle', IconPosition::Before)
                ->color('success'),
        ];
    }
}
