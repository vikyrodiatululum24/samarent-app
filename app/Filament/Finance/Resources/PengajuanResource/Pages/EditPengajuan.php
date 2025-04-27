<?php

namespace App\Filament\Finance\Resources\PengajuanResource\Pages;

use App\Filament\Finance\Resources\PengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengajuan extends EditRecord
{
    protected static string $resource = PengajuanResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {

        if (empty($data['complete']['status_finance'])) {
            $data['complete']['status_finance'] = 'unpaid';
        }
        if ($data['complete']['status_finance'] === 'unpaid') {
            $data['keterangan_proses'] = 'finance';
            $this->record->complete->updateOrCreate(
                [
                    'pengajuan_id' => $this->record->id,
                ],
                [
                    'payment_2' => $data['complete']['payment_2'],
                    'bank_2' => $data['complete']['bank_2'],
                    'norek_2' => $data['complete']['norek_2'],
                ]
            );
        } elseif ($data['complete']['status_finance'] === 'paid') {
            $data['keterangan_proses'] = 'otorisasi';
            $this->record->complete()->updateOrCreate(
                [
                    'pengajuan_id' => $this->record->id,
                ],
                [
                    'tanggal_tf_finance' => $data['complete']['tanggal_tf_finance'],
                    'nominal_tf_finance' => $data['complete']['nominal_tf_finance'],
                    'status_finance' => $data['complete']['status_finance'],
                ]
            );
            if (!$this->record->finance) {
                $this->record->finance()->create([
                    'pengajuan_id' => $this->record->id,
                    'user_id' => auth()->user()->id,
                    'bukti_transaksi' =>  $data['finance']['bukti_transaksi'],
                ]);
            } else {
                // Jika finance sudah ada, lakukan updateOrCreate
                $this->record->finance->updateOrCreate(
                    ['pengajuan_id' => $this->record->id], // Kondisi pencarian
                    [
                        'user_id' => auth()->user()->id,
                        'bukti_transaksi' => $data['finance']['bukti_transaksi'],
                    ]
                );
            }
        }

        return $data;
    }

    public function getTitle(): string
    {
        return 'Proses Permohonan'; // Ganti judul halaman
    }

    public static function getResourceLabel(): string
    {
        return 'Proses';
    }

    public static function getPluralResourceLabel(): string
    {
        return 'Proses';
    }
    public static function getNavigationLabel(): string
    {
        return 'Proses'; // Ubah label tombol navigasi menjadi "Proses"
    }
}
