<?php

namespace App\Filament\Pages;

use App\Models\Unit;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class HistoriPengajuan extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static ?string $title = 'Histori Pengajuan';
    protected static ?string $navigationLabel = 'Histori';
    protected static ?string $slug = 'histori-pengajuan';
    protected static string | \UnitEnum | null $navigationGroup = 'Unit';

    protected string $view = 'filament.pages.histori-pengajuan';

    public ?array $data = [];

    // public function mount(): void
    // {
    //     $this->form->fill();
    // }

    // public function getTableQuery()
    // {
    //     return Unit::query()
    //         ->withCount(['serviceUnit as total_pengajuan' =>
    // }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Unit::query()
                ->withCount(['serviceUnit as total_pengajuan' => function ($query) {
                    $filter = $this->getTableFilterState('range') ?? [];

                    if (! empty($filter['started_at'])) {
                        $query->whereDate('created_at', '>=', $filter['started_at']);
                    }

                    if (! empty($filter['ended_at'])) {
                        $query->whereDate('created_at', '<=', $filter['ended_at']);
                    }
                }])
            )
            ->columns([
                TextColumn::make('jenis')->label('Jenis Unit')->searchable(),
                TextColumn::make('merk')->label('Merk')->searchable(),
                TextColumn::make('nopol')->label('No. Polisi')->searchable(),
                TextColumn::make('total_pengajuan')
                    ->label('Jumlah Pengajuan')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->actions([
                Action::make('detail')
                    ->label('Detail')
                    ->url(fn($record) => DetailHistori::getUrl(array_filter([
                        'unit' => $record->id,
                        'started_at' => $this->getTableFilterState('range')['started_at'] ?? null,
                        'ended_at' => $this->getTableFilterState('range')['ended_at'] ?? null,
                    ])))
                    ->icon('heroicon-o-eye'),
            ])
            ->filters([
                Filter::make('range')
                    ->form([
                        Section::make('Filter Tanggal Pengajuan')
                            ->schema([
                                DatePicker::make('started_at')
                                    ->label('Dari Tanggal')
                                    ->maxDate(now())
                                    ->reactive(),
                                DatePicker::make('ended_at')
                                    ->label('Sampai Tanggal')
                                    ->maxDate(now())
                                    ->reactive(),
                            ])
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
                            ])
                    ])
                    ->columnSpanFull()
                    ->query(function ($query, array $data) {
                        if ($data['started_at']) {
                            $query->whereHas('serviceUnit', function ($q) use ($data) {
                                $q->whereDate('created_at', '>=', $data['started_at']);
                            });
                        }

                        if ($data['ended_at']) {
                            $query->whereHas('serviceUnit', function ($q) use ($data) {
                                $q->whereDate('created_at', '<=', $data['ended_at']);
                            });
                        }
                    })
                    ->label('Filter Tanggal'),
            ], layout: FiltersLayout::AboveContent)
            ->defaultSort('id', 'desc');
    }

    // public function getTableColumns(): array
    // {
    //     return [
    //         TextColumn::make('id')->label('ID')->sortable(),
    //         TextColumn::make('jenis')->label('Jenis Unit')->searchable(),
    //         TextColumn::make('merk')->label('Merk')->searchable(),
    //         TextColumn::make('nopol')->label('No. Polisi')->searchable(),
    //         TextColumn::make('total_pengajuan')
    //             ->label('Jumlah Pengajuan')
    //             ->sortable()
    //             ->alignCenter()
    //     ];
    // }

    // public function getTableActions(): array
    // {
    //     return [

    //         Action::make('detail')
    //             ->label('Detail')
    //             ->url(fn($record) => DetailHistori::getUrl(['unit' => $record->id]))
    //             ->icon('heroicon-o-eye'),
    //     ];
    // }

    // public function getTableDefaultSort(): ?array
    // {
    //     return ['id' => 'desc']; // atau 'total_pengajuan' => 'desc'
    // }

    // public function updated($propertyName): void
    // {
    //     if (str_starts_with($propertyName, 'data.')) {
    //         $this->resetTable(); // Ini yang memicu refresh tabel
    //     }
    // }
}
