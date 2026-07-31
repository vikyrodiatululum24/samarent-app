<?php

namespace App\Filament\Resources\Bastks\Schemas;

use App\Helpers\BastkHelper;
use App\Models\Unit;
use Filament\Forms;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Livewire\Form;

class BastkFormLama
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make([
                // Step 1: Informasi BASTK (Induk)
                Step::make('Informasi BASTK')
                    ->description('Data Utama Berita Acara Serah Terima Kendaraan')
                    ->schema([
                        Grid::make(2)->schema([
                            Group::make()
                                ->columnSpanFull()
                                ->columns(3)
                                ->schema([
                                    Forms\Components\Select::make('type_bastk')->label('Jenis BASTK')->options([
                                        'serah' => 'Penyerahan',
                                        'terima' => 'Pengambilan',
                                    ])->required(),
                                    Forms\Components\Select::make('kode')->label('Kode BASTK')->placeholder('Pilih Kode BASTK')->options(BastkHelper::kode())->required(),
                                    Forms\Components\Select::make('unit_id')->label('Pilih Unit')->relationship('unit', 'nopol')->searchable()->preload()->required()->getOptionLabelFromRecordUsing(fn(Unit $record) => "{$record->type} - {$record->nopol}"),
                                ]),
                            Forms\Components\TextInput::make('kepada')->label('Kepada')->required()->maxLength(255),
                            Forms\Components\TextInput::make('no_hp')->label('No HP')->tel()->maxLength(255),
                            Forms\Components\Textarea::make('alamat')->label('Alamat')->columnSpanFull()->rows(2),
                            Forms\Components\DatePicker::make('tgl_serah')->label('Tanggal Serah'),
                            Forms\Components\DatePicker::make('tgl_kembali')->label('Tanggal Kembali'),
                            Forms\Components\TextInput::make('nama_penyerah')->label('Nama Penyerah')->maxLength(255),
                            Forms\Components\TextInput::make('nama_penerima')->label('Nama Penerima')->maxLength(255),
                            Forms\Components\CheckboxList::make('kondisi_unit')->label('Kondisi Unit')->options(BastkHelper::kondisi())->required()
                                ->reactive(),
                            Forms\Components\Textarea::make('exchange')->label('Exchange')->columnSpanFull()->rows(3)->visible(fn($get) => in_array('exchange', $get('kondisi_unit') ?? []))->required(fn($get) => in_array('exchange', $get('kondisi_unit') ?? [])),
                        ]),
                    ]),

                // Step 2: Kelengkapan & Kondisi Unit (Child: bastk_items)
                Step::make('Kelengkapan & Kondisi')
                    ->description('Checklist Kelengkapan dan Status Unit')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Grid::make(6)->schema([
                                    Forms\Components\Hidden::make('kelengkapan'),

                                    Forms\Components\Placeholder::make('kelengkapan_label')->content(fn($get) => $get('kelengkapan') ?? '-')->hiddenLabel()->columnSpan(2),

                                    Forms\Components\Checkbox::make('baik')
                                        ->label(
                                            fn($get) => match ($get('kelengkapan')) {
                                                'Velg Ban' => 'Original',
                                                'Tutup Dop', 'Apar' => 'Ada',
                                                default => 'Baik',
                                            },
                                        )
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, Set $set) {
                                            if ($state) {
                                                $set('rusak', false);
                                                $set('tidak_ada', false);
                                            }
                                        })
                                        ->visible(fn($get) => $get('kelengkapan') !== 'BBM & KM')
                                        ->columnSpan(1),

                                    Forms\Components\Checkbox::make('rusak')
                                        ->label(fn($get) => $get('kelengkapan') === 'Velg Ban' ? 'Racing' : 'Rusak')
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, Set $set) {
                                            if ($state) {
                                                $set('baik', false);
                                                $set('tidak_ada', false);
                                            }
                                        })
                                        ->visible(fn($get) => !in_array($get('kelengkapan'), ['BBM & KM', 'Tutup Dop', 'Apar']))
                                        ->columnSpan(1),

                                    Forms\Components\Checkbox::make('tidak_ada')
                                        ->label('Tidak Ada')
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, Set $set) {
                                            if ($state) {
                                                $set('baik', false);
                                                $set('rusak', false);
                                            }
                                        })
                                        ->visible(fn($get) => !in_array($get('kelengkapan'), ['BBM & KM', 'Velg Ban']))
                                        ->columnSpan(1),

                                    // Fields specific to BBM & KM (hidden on other items)
                                    Forms\Components\Select::make('jenis_bbm')
                                        ->label('Jenis BBM')
                                        ->options([
                                            'Pertalite' => 'Pertalite',
                                            'Pertamax' => 'Pertamax',
                                            'Solar' => 'Solar',
                                            'Dexlite' => 'Dexlite',
                                        ])
                                        ->visible(fn($get) => $get('kelengkapan') === 'BBM & KM')
                                        ->columnSpan(1),

                                    Forms\Components\Select::make('bbm')
                                        ->label('BBM (Bar)')
                                        ->options([
                                            1 => '1 Bar',
                                            2 => '2 Bars',
                                            3 => '3 Bars',
                                            4 => '4 Bars',
                                            5 => '5 Bars',
                                            6 => '6 Bars',
                                            7 => '7 Bars',
                                            8 => '8 Bars',
                                        ])
                                        ->reactive()
                                        ->visible(fn($get) => $get('kelengkapan') === 'BBM & KM')
                                        ->columnSpan(1),

                                    Forms\Components\Placeholder::make('bbm_bar_display')
                                        ->hiddenLabel()
                                        ->content(function ($get) {
                                            $bars = (int) $get('bbm');
                                            if (!$bars) {
                                                return '';
                                            }
                                            $html = '<div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">';
                                            // Fuel bar indicator
                                            $html .= '<div style="display: flex; align-items: flex-end; gap: 3px;">';
                                            $html .= '<span style="font-size: 11px; color: #6b7280; margin-right: 4px; font-weight: 600;">E</span>';
                                            for ($i = 1; $i <= 8; $i++) {
                                                $active = $i <= $bars;
                                                $color = $active ? '#22c55e' : '#e5e7eb';
                                                $border = $active ? '#16a34a' : '#d1d5db';
                                                // Progressive height for fuel bar effect
                                                $height = 14 + $i * 2;
                                                $html .= "<div style='width: 14px; height: {$height}px; background-color: {$color}; border: 1px solid {$border}; border-radius: 2px;' title='Bar {$i}'></div>";
                                            }
                                            $html .= '<span style="font-size: 11px; color: #6b7280; margin-left: 4px; font-weight: 600;">F</span>';
                                            $html .= '</div>';
                                            $html .= '</div>';
                                            return new \Illuminate\Support\HtmlString($html);
                                        })
                                        ->visible(fn($get) => $get('kelengkapan') === 'BBM & KM')
                                        ->columnSpan(1),

                                    Forms\Components\TextInput::make('km')
                                        ->label('KM Kendaraan')
                                        ->inputMode('numeric')
                                        ->suffix('km')
                                        ->rules(['regex:/^[0-9]+$/', 'max:8'])
                                        ->validationMessages([
                                            'regex' => 'KM tidak boleh mengandung huruf atau simbol',
                                            'max' => 'KM tidak boleh lebih dari 8 digit',
                                        ])
                                        ->visible(fn($get) => $get('kelengkapan') === 'BBM & KM')
                                        ->columnSpan(1),
                                ]),
                            ])
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->afterStateHydrated(function ($component, $state) {
                                $master = collect(self::masterKelengkapan());

                                $saved = collect($state)->keyBy('kelengkapan');

                                $items = $master->map(function ($item) use ($saved) {
                                    return $saved[$item['kelengkapan']] ?? [
                                        'kelengkapan' => $item['kelengkapan'],
                                        'baik' => false,
                                        'rusak' => false,
                                        'tidak_ada' => false,
                                    ];
                                });

                                $component->state($items->values()->toArray());
                            })
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('keterangan')->label('Keterangan')->columnSpanFull()->rows(3),
                    ]),
                // Step 3: Dokumentasi Foto (Child: bastk_dokumentasis)
                Step::make('Dokumentasi Foto')
                    ->description('Upload Foto Fisik Unit dan Dokumen Pendukung')
                    ->schema([
                        Group::make()
                            ->relationship('dokumentasi')
                            ->schema([
                                Grid::make(3)->schema([
                                    Forms\Components\FileUpload::make('unit_depan')->label('Foto Unit Depan')->image()->disk('public')->directory('bastk/dokumentasi/unit_depan')->resize(50)->optimize('webp')->maxWidth(1024),
                                    Forms\Components\FileUpload::make('unit_belakang')->label('Foto Unit Belakang')->image()->disk('public')->directory('bastk/dokumentasi/unit_belakang')->resize(50)->optimize('webp')->maxWidth(1024),
                                    Forms\Components\FileUpload::make('unit_samping_kanan')->label('Foto Samping Kanan')->image()->disk('public')->directory('bastk/dokumentasi/unit_samping_kanan')->resize(50)->optimize('webp')->maxWidth(1024),
                                    Forms\Components\FileUpload::make('unit_samping_kiri')->label('Foto Samping Kiri')->image()->disk('public')->directory('bastk/dokumentasi/unit_samping_kiri')->resize(50)->optimize('webp')->maxWidth(1024),
                                    Forms\Components\FileUpload::make('kabin_depan')->label('Foto Kabin Depan')->image()->disk('public')->directory('bastk/dokumentasi/kabin_depan')->resize(50)->optimize('webp')->maxWidth(1024),
                                    Forms\Components\FileUpload::make('kabin_tengah')->label('Foto Kabin Tengah')->image()->disk('public')->directory('bastk/dokumentasi/kabin_tengah')->resize(50)->optimize('webp')->maxWidth(1024),
                                    Forms\Components\FileUpload::make('kabin_belakang')->label('Foto Kabin Belakang')->image()->disk('public')->directory('bastk/dokumentasi/kabin_belakang')->resize(50)->optimize('webp')->maxWidth(1024),
                                    Forms\Components\FileUpload::make('dashboard')->label('Foto Dashboard')->image()->disk('public')->directory('bastk/dokumentasi/dashboard')->resize(50)->optimize('webp')->maxWidth(1024),
                                    Forms\Components\FileUpload::make('odometer')->label('Foto Odometer')->image()->disk('public')->directory('bastk/dokumentasi/odometer')->resize(50)->optimize('webp')->maxWidth(1024),
                                    Forms\Components\FileUpload::make('buku_service')->label('Foto Buku Service')->image()->disk('public')->directory('bastk/dokumentasi/buku_service')->resize(50)->optimize('webp')->maxWidth(1024),
                                    Forms\Components\FileUpload::make('manual_book')->label('Foto Manual Book')->image()->disk('public')->directory('bastk/dokumentasi/manual_book')->resize(50)->optimize('webp')->maxWidth(1024),
                                    Forms\Components\FileUpload::make('ban_serep')->label('Foto Ban Serep')->image()->disk('public')->directory('bastk/dokumentasi/ban_serep')->resize(50)->optimize('webp')->maxWidth(1024),
                                    Forms\Components\FileUpload::make('stnk_depan')->label('Foto STNK Depan')->image()->disk('public')->directory('bastk/dokumentasi/stnk_depan')->resize(50)->optimize('webp')->maxWidth(1024),
                                    Forms\Components\FileUpload::make('stnk_belakang')->label('Foto STNK Belakang')->image()->disk('public')->directory('bastk/dokumentasi/stnk_belakang')->resize(50)->optimize('webp')->maxWidth(1024),
                                    Forms\Components\FileUpload::make('bastk')->label('File Scan BASTK')->image()->disk('public')->directory('bastk/dokumentasi/bastk')->resize(50)->optimize('webp')->maxWidth(1024),
                                ]),
                                Grid::make(2)->schema([Forms\Components\FileUpload::make('kerusakan')->label('Foto Kerusakan (Bisa Banyak)')->image()->multiple()->disk('public')->directory('bastk/dokumentasi/kerusakan')->resize(50)->optimize('webp')->maxWidth(1024), Forms\Components\FileUpload::make('tools')->label('Foto Tools / Peralatan (Bisa Banyak)')->image()->multiple()->disk('public')->directory('bastk/dokumentasi/tools')->resize(50)->optimize('webp')->maxWidth(1024)]),
                            ]),
                    ]),
            ])->columnSpanFull(),
        ]);
    }

    private static function masterKelengkapan(): array
    {
        $kelengkapanFromHelper = \App\Helpers\BastkHelper::kelengkapan();
        return array_map(function ($kelengkapan) {
            return ['kelengkapan' => $kelengkapan];
        }, $kelengkapanFromHelper);
    }
}
