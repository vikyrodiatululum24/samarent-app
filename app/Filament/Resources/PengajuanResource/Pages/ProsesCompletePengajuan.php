<?php

namespace App\Filament\Resources\PengajuanResource\Pages;

use App\Filament\Resources\PengajuanResource;
use App\Services\LogUpdateStatusPengajuanService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProsesCompletePengajuan extends EditRecord
{
    protected static string $resource = PengajuanResource::class;

    protected static ?string $title = 'Proses Data Pengajuan';

    protected array $completeData = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Informasi Bengkel')
                    ->schema([
                        Hidden::make('user_id')
                            ->default(Auth::user()->id),
                        Forms\Components\TextInput::make('complete.bengkel_estimasi')
                            ->label('Nama Bengkel Estimasi')
                            ->required(),
                        Forms\Components\TextInput::make('complete.no_telp_bengkel')
                            ->label('No. Telp Bengkel')
                            ->required(),
                        Forms\Components\TextInput::make('complete.nominal_estimasi')
                            ->label('Nominal Estimasi')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2),
                Forms\Components\Fieldset::make('Informasi Pengajuan')
                    ->schema([
                        Forms\Components\Select::make('complete.kode')
                            ->label('Kode')
                            ->options([
                                'op' => 'OP',
                                'sc' => 'SC',
                                'sp' => 'SP',
                                'stnk' => 'STNK',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('complete.tanggal_masuk_finance')
                            ->label('Tanggal Masuk Finance')
                            ->required(),
                        Forms\Components\Select::make('complete.prioritas')
                            ->label('Prioritas')
                            ->options([
                                'pengambilan_ba' => 'PENGAMBILAN BA',
                                'segera' => 'SEGERA',
                                'urgent' => 'URGENT',
                                'lainnya' => 'LAINNYA',
                            ])
                            ->required()
                            ->default('pengambilan_ba'),
                    ])
                    ->columns(2),
                Forms\Components\Fieldset::make('Informasi Finance')
                    ->schema([
                        Forms\Components\DatePicker::make('complete.tanggal_tf_finance')
                            ->label('Tanggal Transfer Finance')
                            ->readOnly(),
                        Forms\Components\TextInput::make('complete.nominal_tf_finance')
                            ->label('Nominal Transfer Finance')
                            ->numeric()
                            ->readOnly(),
                        Forms\Components\TextInput::make('complete.payment_2')
                            ->label('Rekening Atas Nama')
                            ->readOnly(),
                        Forms\Components\TextInput::make('complete.bank_2')
                            ->label('Bank')
                            ->readOnly(),
                        Forms\Components\TextInput::make('complete.norek_2')
                            ->label('No. Rekening')
                            ->readOnly(),
                        Forms\Components\TextInput::make('complete.status_finance')
                            ->label('Status Finance')
                            ->default('unpaid')
                            ->readOnly(),
                    ])
                    ->columns(2),
                Forms\Components\Fieldset::make('Transfer Bengkel')
                    ->schema([
                        Forms\Components\Select::make('complete.nama_rek_bengkel')
                            ->label('Nama Rekening Bengkel')
                            ->options(
                                \App\Models\Norek::pluck('name', 'name')->toArray()
                            )
                            ->searchable()
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(function ($component, $state, callable $set) {
                                $component->state(strtoupper($state));
                                $norek = \App\Models\Norek::where('name', $state)->first();
                                $set('complete.rek_bengkel', $norek?->norek);
                                $set('complete.bank_bengkel', $norek?->bank);
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
                        Forms\Components\TextInput::make('complete.rek_bengkel')
                            ->nullable()
                            ->label('No. Rekening Bengkel')
                            ->readOnly()
                            ->maxLength(255)
                            ->numeric(),
                        Forms\Components\TextInput::make('complete.bank_bengkel')
                            ->nullable()
                            ->label('Bank')
                            ->maxLength(255)
                            ->readOnly(),
                        Forms\Components\TextInput::make('complete.nominal_tf_bengkel')
                            ->label('Nominal Transfer Bengkel')
                            ->numeric()
                            ->live(onBlur: true)
                            ->required(fn($record) => $record->complete?->status_finance === 'paid')
                            ->nullable()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $nominalFinance = (float) ($get('complete.nominal_tf_finance') ?? 0);
                                $nominalBengkel = (float) ($get('complete.nominal_tf_bengkel') ?? 0);
                                $set('complete.selisih_tf', $nominalFinance - $nominalBengkel);
                            }),
                        Forms\Components\TextInput::make('complete.selisih_tf')
                            ->label('Selisih Transfer')
                            ->numeric()
                            ->required(fn($record) => $record->complete?->status_finance === 'paid')
                            ->readOnly(),
                        Forms\Components\DatePicker::make('complete.tanggal_tf_bengkel')
                            ->label('Tanggal Transfer Bengkel')
                            ->nullable()
                            ->required(fn(callable $get) => !empty($get('complete.nominal_tf_bengkel'))),
                        Forms\Components\DatePicker::make('complete.tanggal_pengerjaan')
                            ->label('Tanggal Pengerjaan')
                            ->nullable()
                            ->required(fn(callable $get) => !empty($get('complete.nominal_tf_bengkel'))),
                    ])
                    ->columns(2),
                Forms\Components\Fieldset::make('Dokumentasi')
                    ->schema([
                        Forms\Components\TextInput::make('complete.bengkel_invoice')
                            ->label('Bengkel Invoice'),
                        Forms\Components\FileUpload::make('complete.foto_nota')
                            ->label('Foto Nota')
                            ->image()
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('foto_nota')
                            ->multiple()
                            ->maxFiles(3)
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire) {
                                if (is_array($state) && count($state) > 3) {
                                    $set('complete.foto_nota', array_slice($state, 0, 3));
                                }
                                $record = $livewire->record ?? null;
                                if ($record && $record->complete && is_array($record->complete->foto_nota)) {
                                    $lama = collect($record->complete->foto_nota);
                                    $baru = collect($state);
                                    $yangDihapus = $lama->diff($baru);
                                    foreach ($yangDihapus as $path) {
                                        Storage::disk('public')->delete($path);
                                    }
                                }
                            }),
                    ])
                    ->columns(2),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load complete relationship data into the form
        if ($this->record->complete) {
            $data['complete'] = $this->record->complete->toArray();
        } else {
            // Initialize dengan nilai default jika complete belum ada
            $data['complete'] = [
                'user_id' => Auth::user()->id,
                'prioritas' => 'pengambilan_ba',
                'status_finance' => 'unpaid',
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract complete data
        $completeData = $data['complete'] ?? [];

        // Remove complete from main data to avoid issues
        unset($data['complete']);

        // Store complete data for after hook
        $this->completeData = $completeData;

        return $data;
    }

    protected function afterSave(): void
    {
        // Update or create complete record
        if (isset($this->completeData)) {
            // Pastikan user_id selalu ada
            $this->completeData['user_id'] = $this->completeData['user_id'] ?? Auth::user()->id;

            $this->record->complete()->updateOrCreate([], $this->completeData);

            // Update keterangan_proses based on status_finance
            if (isset($this->completeData['status_finance'])) {
                if ($this->completeData['status_finance'] === 'paid' && isset($this->completeData['nominal_tf_bengkel'])) {
                    $this->record->update(['keterangan_proses' => 'done']);
                } elseif ($this->completeData['status_finance'] === 'unpaid') {
                    $this->record->update(['keterangan_proses' => 'checker']);
                }
            }

        }

        Notification::make()
            ->title('Data pengajuan berhasil diproses.')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(PengajuanResource::getUrl('index'))
                ->color('gray'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
