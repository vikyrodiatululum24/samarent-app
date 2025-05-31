<?php

namespace App\Filament\Pages;

use App\Models\Unit;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class DetailHistori extends Page
{
    protected static ?string $title = 'Detail Histori';
    protected static ?string $slug = 'detail-histori'; // Menambahkan parameter unit di slug
    protected static string $view = 'filament.pages.detail-histori';

    public ?Unit $unit = null;

    public function mount(): void
    {
        $unitId = request()->query('unit');
        $this->unit = Unit::findOrFail($unitId);
    }
}


// class DetailHistori extends Page
// {

//     protected static ?string $title = 'Detail Histori';
//     protected static ?string $navigationLabel = null; // agar tidak muncul di sidebar

//     public $unit;

//     public $dataHistori;

//     public function mount(): void
//     {
//         $unit = request()->route('unit'); // ambil dari route param
//         dd($unit);
//         $this->unit = $unit;
//         $this->dataHistori = Histori::where('unit_id', $unit)->get();
//     }

//     public static function getRouteParameters(): array
//     {
//         return ['unit'];
//     }
// }
