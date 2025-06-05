<?php

namespace App\Filament\Manager\Pages;

use App\Models\Unit;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;

class DetailHistori extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $title = 'Detail Histori';
    protected static ?string $slug = 'detail-histori'; // Menambahkan parameter unit di slug
    protected static string $view = 'filament.pages.detail-histori';
    protected static bool $shouldRegisterNavigation = false; // Agar tidak tampil di menu navbar

    public ?Unit $unit = null;

    public function mount(): void
    {
        $unitId = request()->query('unit');
        $this->unit = Unit::findOrFail($unitId);
    }

    public function getTableQuery()
    {
        return Unit::query()
            ->with(['serviceUnit', 'serviceUnit.pengajuan'])
            ->where('id', $this->unit->id);
        // ->orderByDesc('serviceUnit.created_at');
    }

    public function getTableColumns(): array
    {
        return [
            TextColumn::make('serviceUnit.created_at')
                ->label('Tanggal Pengajuan')
                ->dateTime('d/m/Y'),
            textColumn::make('serviceUnit.pengajuan.no_pengajuan')
                ->label('No Pengajuan')
                ->searchable(),
            TextColumn::make('serviceUnit.odometer')
                ->label('Odometer')
                ->sortable(),
            TextColumn::make('serviceUnit.service')
                ->label('Jenis Service')
                ->sortable(),
        ];
    }

    public function getTableDefaultSort(): ?array
    {
        return ['id' => 'desc']; // atau 'total_pengajuan' => 'desc'
    }

}
