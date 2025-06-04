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
    protected static ?string $navigationGroup = 'Unit';

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

}