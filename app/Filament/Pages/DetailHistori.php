<?php

namespace App\Filament\Pages;

use App\Models\Unit;
use Filament\Pages\Page;
use App\Models\ServiceUnit;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;

class DetailHistori extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $title = 'Detail Histori';
    protected static ?string $slug = 'detail-histori'; // Menambahkan parameter unit di slug
    protected string $view = 'filament.pages.detail-histori';
    protected static bool $shouldRegisterNavigation = false; // Agar tidak tampil di menu navbar

    public ?Unit $unit = null;
    public ?string $tahun = null;
    public ?string $started_at = null;
    public ?string $ended_at = null;

    public function mount(): void
    {
        $unitId = request()->query('unit');
        $this->unit = Unit::findOrFail($unitId);
        $this->tahun = request()->query('tahun');
        $this->started_at = request()->query('started_at');
        $this->ended_at = request()->query('ended_at');
    }


    public function getTableQuery()
    {
        $query = ServiceUnit::query()
            ->with(['pengajuan'])
            ->where('unit_id', $this->unit->id);

        if (! empty($this->started_at)) {
            $query->whereDate('created_at', '>=', $this->started_at);
        }

        if (! empty($this->ended_at)) {
            $query->whereDate('created_at', '<=', $this->ended_at);
        }

        if (! empty($this->tahun)) {
            $query->whereYear('created_at', $this->tahun);
        }

        return $query;
    }

    public function getTableColumns(): array
    {
        return [
            TextColumn::make('created_at')
                ->label('Tanggal Pengajuan')
                ->dateTime('d/m/Y')
                ->sortable(),

            TextColumn::make('pengajuan.no_pengajuan')
                ->label('No Pengajuan')
                ->searchable(),

            TextColumn::make('odometer')
                ->label('Odometer')
                ->sortable(),

            TextColumn::make('service')
                ->label('Jenis Service')
                ->sortable(),
        ];
    }


    public function getTableDefaultSort(): ?array
    {
        return ['id' => 'desc']; // atau 'total_pengajuan' => 'desc'
    }
}
