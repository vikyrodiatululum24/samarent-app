<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Unit;
use Carbon\Carbon;

class HistoriPengajuanTable extends Component
{
    public $units;
    public $search = '';
    public $perPage = 10;
    public $currentPage = 1;

    public function mount()
    {
        $this->loadData();
    }

    public function updatedSearch($value)
    {
        logger('Search value: ' . $value);
    }

    public function loadData()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $allUnits = Unit::with(['serviceUnit.pengajuan'])->get();

        if ($this->search) {
            $allUnits = $allUnits->filter(function ($unit) {
                return str_contains(strtolower($unit->nopol), strtolower($this->search)) ||
                    str_contains(strtolower($unit->type), strtolower($this->search));
            })->values();
        }

        $this->units = $allUnits->map(function ($unit) use ($startOfMonth, $endOfMonth) {
            $total = $unit->serviceUnit->filter(fn($su) => $su->pengajuan !== null)->count();

            $bulanIni = $unit->serviceUnit->filter(
                fn($su) =>
                $su->pengajuan && $su->pengajuan->created_at->between($startOfMonth, $endOfMonth)
            )->count();

            return [
                'nopol' => $unit->nopol,
                'type' => $unit->type,
                'total_pengajuan' => $total,
                'pengajuan_bulan_ini' => $bulanIni > 0,
            ];
        });
    }

    public function render()
    {
        return view('livewire.histori-pengajuan-table');
    }
}
