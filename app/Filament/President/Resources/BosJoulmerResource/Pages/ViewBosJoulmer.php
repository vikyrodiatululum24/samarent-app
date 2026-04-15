<?php

namespace App\Filament\President\Resources\BosJoulmerResource\Pages;

use App\Filament\President\Resources\BosJoulmerResource;
use App\Models\BosJoulmer;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class ViewBosJoulmer extends EditRecord
{
    protected static string $resource = BosJoulmerResource::class;

    protected static ?string $title = 'Review Pengajuan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Umum')
                    ->schema([
                        Placeholder::make('no_pengajuan')
                            ->label('No. Pengajuan')
                            ->content(fn() => $this->record->pengajuan?->no_pengajuan ?? '-'),
                        Placeholder::make('no_wa')
                            ->label('Nomor WhatsApp')
                            ->content(fn() => $this->record->pengajuan?->no_wa ?? '-'),
                        Placeholder::make('project')
                            ->label('Project')
                            ->content(fn() => $this->record->pengajuan?->project ?? '-'),
                        Placeholder::make('keterangan')
                            ->label('Keterangan')
                            ->content(fn() => $this->record->pengajuan?->keterangan ?? '-'),
                        Placeholder::make('up')
                            ->label('Unit Pelaksana')
                            ->content(function () {
                                $pengajuan = $this->record->pengajuan;

                                if (! $pengajuan) {
                                    return '-';
                                }

                                if ($pengajuan->up === 'manual') {
                                    return $pengajuan->up_lainnya ?: '-';
                                }

                                return $pengajuan->up ?: '-';
                            }),
                        Placeholder::make('provinsi')
                            ->label('Provinsi')
                            ->content(fn() => $this->record->pengajuan?->provinsi ?? '-'),
                        Placeholder::make('kota')
                            ->label('Kota/Kabupaten')
                            ->content(fn() => $this->record->pengajuan?->kota ?? '-'),
                        Placeholder::make('status_pengajuan')
                            ->label('Status Pengajuan')
                            ->content(function () {
                                return match ($this->record->pengajuan?->keterangan_proses) {
                                    'cs' => 'Customer Service',
                                    'checker' => 'Verifikasi',
                                    'pengajuan atasan' => 'Pengajuan Atasan',
                                    'pengajuan finance' => 'Pengajuan Finance',
                                    'finance' => 'Input Finance',
                                    'otorisasi' => 'Otorisasi',
                                    'done' => 'Selesai',
                                    default => 'Tidak Diketahui',
                                };
                            }),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Pembayaran')
                    ->schema([
                        Placeholder::make('payment_1')
                            ->label('Nama Rekening')
                            ->content(fn() => $this->record->pengajuan?->payment_1 ?? '-'),
                        Placeholder::make('bank_1')
                            ->label('Bank')
                            ->content(fn() => $this->record->pengajuan?->bank_1 ?? '-'),
                        Placeholder::make('norek_1')
                            ->label('No. Rekening')
                            ->content(fn() => $this->record->pengajuan?->norek_1 ?? '-'),
                    ])
                    ->columns(3),
                Forms\Components\Section::make('Detail Kendaraan')
                    ->schema([
                        Forms\Components\View::make('filament.resources.pages.bos-joulmer.detail-kendaraan')
                            ->viewData([
                                'pengajuanId' => $this->record->pengajuan_id,
                            ])
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Informasi Complete')
                    ->schema([
                        Placeholder::make('complete_bengkel_estimasi')
                            ->label('Bengkel Estimasi')
                            ->content(fn() => $this->record->pengajuan?->complete?->bengkel_estimasi ?? '-'),
                        Placeholder::make('complete_nominal_estimasi')
                            ->label('Nominal Estimasi')
                            ->content(fn() => $this->record->pengajuan?->complete?->nominal_estimasi !== null ? 'Rp ' . number_format($this->record->pengajuan->complete->nominal_estimasi, 0, ',', '.') : '-'),
                        Placeholder::make('complete_bengkel_invoice')
                            ->label('Bengkel Invoice')
                            ->content(fn() => $this->record->pengajuan?->complete?->bengkel_invoice ?? '-'),
                        Placeholder::make('complete_bank_2')
                            ->label('Bank')
                            ->content(fn() => $this->record->pengajuan?->complete?->bank_2 ?? '-'),
                        Placeholder::make('complete_payment_2')
                            ->label('Nama Rekening')
                            ->content(fn() => $this->record->pengajuan?->complete?->payment_2 ?? '-'),
                        Placeholder::make('complete_norek_2')
                            ->label('No. Rekening')
                            ->content(fn() => $this->record->pengajuan?->complete?->norek_2 ?? '-'),
                        Placeholder::make('complete_status_finance')
                            ->label('Status Finance')
                            ->content(function () {
                                return match ($this->record->pengajuan?->complete?->status_finance) {
                                    'paid' => 'PAID',
                                    'unpaid' => 'UNPAID',
                                    default => '-',
                                };
                            }),
                    ])
                    ->columns(3)
                    ->visible(fn() => filled($this->record->pengajuan?->complete)),
                Forms\Components\Section::make('Keputusan Review')
                    ->schema([
                        Forms\Components\Textarea::make('note')
                            ->label('Catatan (Opsional)')
                            ->maxLength(255)
                            ->nullable()
                            ->rows(4),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Setujui')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => BosJoulmerResource::canView($this->record)
                    && in_array($this->record->pengajuan?->keterangan_proses, ['pengajuan atasan', 'menunggu atasan'], true))
                ->action(function (): void {
                    $record = $this->record;
                    $formData = $this->form->getState();

                    $record->update([
                        'user_id' => Auth::id(),
                        'is_approved' => 'approved',
                        'note' => $formData['note'] ?? null,
                    ]);

                    Notification::make()
                        ->title('pengajuan disetujui.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', ['record' => $record]));
                }),
            Actions\Action::make('reject')
                ->label('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn() => BosJoulmerResource::canView($this->record)
                    && in_array($this->record->pengajuan?->keterangan_proses, ['pengajuan atasan'], true))
                ->action(function (): void {
                    $record = $this->record;
                    $formData = $this->form->getState();

                    $record->update([
                        'user_id' => Auth::id(),
                        'is_approved' => 'rejected',
                        'note' => $formData['note'] ?? null,
                    ]);

                    Notification::make()
                        ->title('pengajuan ditolak.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', ['record' => $record]));
                }),
            Actions\Action::make('kembali')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->loadMissing([
            'user',
            'pengajuan',
            'pengajuan.complete',
            'pengajuan.finance',
            'pengajuan.service_unit.unit',
        ]);

        $data['note'] = $this->record->note;

        return $data;
    }

    public static function canAccess(array $parameters = []): bool
    {
        if (! parent::canAccess($parameters)) {
            return false;
        }

        if (! isset($parameters['record'])) {
            return true;
        }

        $record = $parameters['record'];

        if (! $record instanceof BosJoulmer) {
            return true;
        }

        return BosJoulmerResource::canView($record);
    }
}
