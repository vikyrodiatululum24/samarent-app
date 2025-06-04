<?php

namespace App\Filament\Pages;

use App\Models\Unit;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;

class Mount extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $view = 'filament.pages.mount';

    protected static ?string $title = 'Histori Perbulan';
    protected static ?string $navigationLabel = 'Histori Perbulan';
    protected static ?string $slug = 'mount';
    protected static ?string $navigationGroup = 'Unit';

    public ?array $data = [];


    public function getTableQuery()
    {
        // Ambil semua unit, tidak perlu eager load count di sini
        return Unit::query();
    }

    public function getTableColumns(): array
    {
        // Ambil range bulan dari filter atau default 12 bulan terakhir
        $startDate = !empty($this->data['startDate']) ? $this->data['startDate'] : now()->subMonths(11)->startOfMonth()->toDateString();
        $endDate = !empty($this->data['endDate']) ? $this->data['endDate'] : now()->endOfMonth()->toDateString();

        $period = \Carbon\CarbonPeriod::create($startDate, '1 month', $endDate);

        $columns = [
            TextColumn::make('id')->label('ID')->sortable(),
            TextColumn::make('jenis')->label('Jenis Unit')->searchable(),
            TextColumn::make('merk')->label('Merk')->searchable(),
            TextColumn::make('nopol')->label('No. Polisi')->searchable(),
        ];

        foreach ($period as $month) {
            $monthLabel = $month->format('M Y');
            $monthStart = $month->copy()->startOfMonth()->toDateString();
            $monthEnd = $month->copy()->endOfMonth()->toDateString();

            $columns[] = TextColumn::make('service_count_' . $month->format('Ym'))
                ->label($monthLabel)
                ->alignCenter()
                ->getStateUsing(function ($record) use ($monthStart, $monthEnd) {
                    return $record->serviceUnit()
                        ->whereDate('created_at', '>=', $monthStart)
                        ->whereDate('created_at', '<=', $monthEnd)
                        ->count();
                });
        }

        return $columns;
    }

    // public function getTableActions(): array
    // {
    //     return [

    //         Action::make('detail')
    //             ->label('Detail')
    //             ->url(fn($record) => DetailHistori::getUrl(['unit' => $record->id]))
    //             ->icon('heroicon-o-eye'),
    //     ];
    // }

    public function getTableDefaultSort(): ?array
    {
        return ['id' => 'desc']; // atau 'total_pengajuan' => 'desc'
    }
}
