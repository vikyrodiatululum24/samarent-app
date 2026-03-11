<?php

namespace App\Filament\Finance\Resources\PengajuanResource\Pages;

use App\Filament\Finance\Resources\PengajuanResource;
use App\Models\Pengajuan;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;

class ProsesPengajuan extends EditRecord
{
    protected static string $resource = PengajuanResource::class;

    protected static ?string $title = 'Proses Pengajuan Finance';

    public ?array $data = [];

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::SevenExtraLarge;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->load(['complete', 'finance']);

        $data['complete'] = [
            'payment_2' => $this->record->complete?->payment_2,
            'bank_2' => $this->record->complete?->bank_2,
            'norek_2' => $this->record->complete?->norek_2,
            'tanggal_tf_finance' => $this->record->complete?->tanggal_tf_finance,
            'nominal_tf_finance' => $this->record->complete?->nominal_tf_finance,
            'status_finance' => $this->record->complete?->status_finance ?? 'unpaid',
        ];

        $data['finance'] = [
            'user_id' => Auth::user()->id,
            'bukti_transaksi' => $this->record->finance?->bukti_transaksi,
        ];

        return $data;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Finance')
                    ->schema([
                        Hidden::make('finance.user_id')
                            ->default(Auth::user()->id),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('complete.payment_2')
                                    ->label('Nama Rekening')
                                    ->options(
                                        \App\Models\Norek::pluck('name', 'name')->toArray()
                                    )
                                    ->searchable()
                                    ->nullable()
                                    ->live()
                                    ->afterStateUpdated(function ($component, $state, callable $set) {
                                        $component->state(strtoupper($state));
                                        $norek = \App\Models\Norek::where('name', $state)->first();
                                        $set('complete.norek_2', $norek?->norek);
                                        $set('complete.bank_2', $norek?->bank);
                                        $set('complete.payment_2', $norek?->name);
                                    })
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama Rekening')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('norek')
                                            ->label('No. Rekening')
                                            ->required()
                                            ->numeric()
                                            ->maxLength(255),
                                        Forms\Components\Select::make('bank')
                                            ->label('Bank')
                                            ->required()
                                            ->options([
                                                'BCA' => 'BCA',
                                                'MANDIRI' => 'MANDIRI',
                                                'BRI' => 'BRI',
                                                'BNI' => 'BNI',
                                                'PERMATA' => 'PERMATA',
                                                'BTN' => 'BTN',
                                            ]),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        $exists = \App\Models\Norek::where('name', $data['name'])
                                            ->orWhere('norek', $data['norek'])
                                            ->exists();
                                        if ($exists) {
                                            Notification::make()
                                                ->title('Gagal Menambah Rekening')
                                                ->body('Nama rekening atau nomor rekening sudah terdaftar.')
                                                ->danger()
                                                ->send();
                                            return $data['name'];
                                        }
                                        \App\Models\Norek::create(['name' => $data['name'], 'norek' => $data['norek'], 'bank' => $data['bank']]);
                                        Notification::make()
                                            ->title('Berhasil Menambah Rekening')
                                            ->body('Nama rekening dan nomor rekening berhasil ditambahkan.')
                                            ->success()
                                            ->send();
                                        return $data['name'];
                                    })
                                    ->createOptionAction(function ($action) {
                                        $action->modalHeading('Tambah Nama Rekening Baru');
                                    }),
                                Forms\Components\TextInput::make('complete.bank_2')
                                    ->nullable()
                                    ->label('Bank')
                                    ->readOnly(),
                                Forms\Components\TextInput::make('complete.norek_2')
                                    ->nullable()
                                    ->label('No. Rekening')
                                    ->numeric()
                                    ->maxLength(255)
                                    ->readOnly(),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('complete.tanggal_tf_finance')
                                    ->label('Tanggal Transfer')
                                    ->required(fn($get) => $get('complete.status_finance') === 'paid'),

                                Forms\Components\TextInput::make('complete.nominal_tf_finance')
                                    ->label('Nominal Transfer')
                                    ->numeric()
                                    ->required(fn($get) => $get('complete.status_finance') === 'paid'),

                                Forms\Components\Select::make('complete.status_finance')
                                    ->label('Status')
                                    ->options([
                                        'paid' => 'Paid',
                                        'unpaid' => 'Unpaid',
                                    ])
                                    ->live(),
                            ]),
                        Forms\Components\FileUpload::make('finance.bukti_transaksi')
                            ->label('Bukti Transaksi')
                            ->resize(50)
                            ->maxSize(2048)
                            ->helperText('Hanya dapat mengunggah file dengan tipe PDF atau gambar (image).')
                            ->required(fn($get) => $get('complete.status_finance') === 'paid')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->disk('public')
                            ->directory('bukti_transaksi'),
                    ])
                    ->columns(1),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['finance']['user_id'] = Auth::user()->id;

        $this->record->complete()->updateOrCreate(
            ['pengajuan_id' => $this->record->id],
            $data['complete'] ?? []
        );

        $this->record->finance()->updateOrCreate(
            ['pengajuan_id' => $this->record->id],
            $data['finance'] ?? []
        );

        $statusFinance = $data['complete']['status_finance'] ?? 'unpaid';
        $data['keterangan_proses'] = $statusFinance === 'paid' ? 'otorisasi' : 'finance';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
