<?php

namespace App\Filament\Pages;

use App\Models\Unit;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class HistoriPengajuan extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static ?string $title = 'Histori Pengajuan';
    protected static ?string $navigationLabel = 'Histori';
    protected static ?string $slug = 'histori-pengajuan';
    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.histori-pengajuan';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('Tanggal Mulai')
                            ->reactive()
                            ->maxDate(fn(Get $get) => $get('endDate') ?: now()),

                        DatePicker::make('endDate')
                            ->label('Tanggal Akhir')
                            ->reactive()
                            ->minDate(fn(Get $get) => $get('startDate') ?: now())
                            ->maxDate(now()),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function getTableQuery()
    {
        return Unit::query()
            ->withCount(['serviceUnit as total_pengajuan' => function ($query) {
                if (!empty($this->data['startDate'])) {
                    $query->whereDate('created_at', '>=', $this->data['startDate']);
                }

                if (!empty($this->data['endDate'])) {
                    $query->whereDate('created_at', '<=', $this->data['endDate']);
                }
            }]);
    }

    public function getTableColumns(): array
    {
        return [
            TextColumn::make('id')->label('ID')->sortable(),
            TextColumn::make('jenis')->label('Jenis Unit')->searchable(),
            TextColumn::make('merk')->label('Merk')->searchable(),
            TextColumn::make('nopol')->label('No. Polisi')->searchable(),
            TextColumn::make('total_pengajuan')
                ->label('Jumlah Pengajuan')
                ->sortable()
                ->alignCenter()
                

        ];
    }

public function getTableActions(): array
{
    return [
        
        Action::make('detail')
            ->label('Detail')
            ->url(fn($record) => DetailHistori::getUrl(['unit' => $record->id]))
            ->icon('heroicon-o-eye'),
    ];
}

    public function getTableDefaultSort(): ?array
    {
        return ['id' => 'desc']; // atau 'total_pengajuan' => 'desc'
    }

    public function updated($propertyName): void
    {
        if (str_starts_with($propertyName, 'data.')) {
            $this->resetTable(); // Ini yang memicu refresh tabel
        }
    }

    // public function updated($propertyName)
    // {
    //     if (str_starts_with($propertyName, 'data.')) {
    //         $this->resetPage();
    //     }
    // }
}



    // public static function table(Table $table): Table
    // {
    //     return $table
    //         ->columns([
    //             Tables\Columns\TextColumn::make('jenis')
    //                 ->label('Jenis Kendaraan')
    //                 ->searchable(),
    //             Tables\Columns\TextColumn::make('merk')
    //                 ->label('Merk Kendaraan')
    //                 ->searchable(),
    //             Tables\Columns\TextColumn::make('type')
    //                 ->label('Type Kendaraan')
    //                 ->searchable(),
    //             Tables\Columns\TextColumn::make('nopol')
    //                 ->label('No. Polisi')
    //                 ->searchable(),

    //             Tables\Columns\TextColumn::make('total_pengajuan')
    //                 ->label('Total Pengajuan')
    //                 ->getStateUsing(function ($record) {
    //                     return $record->serviceUnit
    //                         ->filter(fn($su) => $su->pengajuan !== null)
    //                         ->count();
    //                 }),

    //             Tables\Columns\IconColumn::make('pengajuan_bulan_ini')
    //                 ->label('Pengajuan Bulan Ini')
    //                 ->getStateUsing(function ($record) {
    //                     $startOfMonth = request()->get('start_date') ? Carbon::parse(request()->get('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
    //                     $endOfMonth = request()->get('end_date') ? Carbon::parse(request()->get('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

    //                     return $record->serviceUnit
    //                         ->filter(function ($su) use ($startOfMonth, $endOfMonth) {
    //                             return $su->pengajuan &&
    //                                 $su->pengajuan->created_at->between($startOfMonth, $endOfMonth);
    //                         })
    //                         ->count() > 0;
    //                 })
    //                 ->boolean(),
    //         ])
    //         ->filters([
    //             //
    //         ])
    //         ->actions([
    //             Tables\Actions\EditAction::make(),
    //         ])
    //         ->bulkActions([
    //             Tables\Actions\BulkActionGroup::make([
    //                 Tables\Actions\DeleteBulkAction::make(),
    //             ]),
    //         ]);
    // }

    // public $units = [];
    // public $currentPage = 1;
    // public $perPage = 10;
    // public $totalPages = 1;
    // public $total = 0;

    // use InteractsWithRecord;

    // public function mount(int | string $record)
    // {
    //     $this->record = $this->resolveRecord($record);
    //     $search = request()->get('search', '');
    //     $this->currentPage = (int) request()->get('page', 1);
    //     $this->perPage = (int) request()->get('perPage', 10);

    //     // Ambil filter tanggal dari request
    //     $startDate = request()->get('start_date');
    //     $endDate = request()->get('end_date');
    //     $startRange = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
    //     $endRange = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfMonth();

    //     $allUnits = Unit::with(['serviceUnit.pengajuan'])->get();

    //     if ($search) {
    //         $allUnits = $allUnits->filter(function ($unit) use ($search) {
    //             return str_contains(strtolower($unit->nopol), strtolower($search)) ||
    //                 str_contains(strtolower($unit->type), strtolower($search));
    //         })->values();
    //     }

    //     $mapped = $allUnits->map(function ($unit) use ($startRange, $endRange) {
    //         $total = $unit->serviceUnit->filter(function ($su) use ($startRange, $endRange) {
    //             return $su->pengajuan &&
    //                 $su->pengajuan->created_at->between($startRange, $endRange);
    //         })->count();
    //         $inRange = $unit->serviceUnit->filter(
    //             fn($su) =>
    //             $su->pengajuan && $su->pengajuan->created_at->between($startRange, $endRange)
    //         )->count();
    //         return [
    //             'id' => $unit->id,
    //             'nopol' => $unit->nopol,
    //             'type' => $unit->type,
    //             'total_pengajuan' => $total,
    //             'pengajuan_bulan_ini' => $inRange > 0,
    //         ];
    //     });

    //     $this->total = $mapped->count();
    //     $this->totalPages = (int) ceil($this->total / $this->perPage);
    //     $this->units = $mapped->slice(($this->currentPage - 1) * $this->perPage, $this->perPage)->values();
    // }

// }
