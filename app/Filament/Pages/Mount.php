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
        // Hitung jumlah service unit untuk setiap bulan (12 bulan terakhir)
        $query = Unit::query();
        $startDate = !empty($this->data['startDate']) ? $this->data['startDate'] : now()->subMonths(11)->startOfMonth()->toDateString();
        $endDate = !empty($this->data['endDate']) ? $this->data['endDate'] : now()->endOfMonth()->toDateString();
        $period = \Carbon\CarbonPeriod::create($startDate, '1 month', $endDate);

        foreach ($period as $month) {
            $monthStart = $month->copy()->startOfMonth()->toDateString();
            $monthEnd = $month->copy()->endOfMonth()->toDateString();
            $alias = 'service_count_' . $month->format('Ym');
            $query->withCount([
                'serviceUnit as ' . $alias => function ($q) use ($monthStart, $monthEnd) {
                    $q->whereDate('created_at', '>=', $monthStart)
                        ->whereDate('created_at', '<=', $monthEnd);
                }
            ]);
        }

        return $query;
    }

    public function getTableColumns(): array
    {
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
            $alias = 'service_count_' . $month->format('Ym');
            $columns[] = TextColumn::make($alias)
                ->label($monthLabel)
                ->alignCenter()
                ->icon(fn($state) => $state > 0 ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                ->color(fn($state) => $state > 0 ? 'success' : 'danger');
        }

        return $columns;
    }

    public function getTableDefaultSort(): ?array
    {
        return ['id' => 'desc']; // atau 'total_pengajuan' => 'desc'
    }
}
