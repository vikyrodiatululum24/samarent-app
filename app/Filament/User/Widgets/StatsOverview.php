<?php

namespace App\Filament\User\Widgets;

use App\Models\Pengajuan;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = auth()->id();
        $csCount = Pengajuan::where('keterangan_proses', 'cs')->where('user_id', $userId)->count();
        $financeCount = Pengajuan::where('keterangan_proses', 'finance')->where('user_id', $userId)->count();
        $doneCount = Pengajuan::where('keterangan_proses', 'done')->where('user_id', $userId)->count();

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
