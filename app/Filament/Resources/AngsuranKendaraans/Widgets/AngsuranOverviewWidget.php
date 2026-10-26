<?php

namespace App\Filament\Resources\AngsuranKendaraans\Widgets;

use App\Models\AngsuranKendaraan;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class AngsuranOverviewWidget extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        if (! $this->record instanceof AngsuranKendaraan) {
            return [];
        }

        $record = $this->record;

        // 1. Progres Pelunasan
        $nilaiKontrak = (float) $record->nilai_kontrak;
        $totalSetoran = (float) $record->total_setoran_keseluruhan;
        $sisaKontrak = (float) $record->sisa_kontrak;
        $persenLunas = $nilaiKontrak > 0 ? round(($totalSetoran / $nilaiKontrak) * 100, 1) : 0;

        // 2. Setoran Bulan Ini
        $bulanIni = now()->month;
        $tahunIni = now()->year;
        $namaBulan = Carbon::now()->locale('id')->translatedFormat('F Y');
        $setoranBulanIni = (float) $record->getTotalSetoranBulan($bulanIni, $tahunIni);
        $normalAngsuran = (float) $record->normal_angsuran;
        $selisihBulanIni = $setoranBulanIni - $normalAngsuran;

        if ($setoranBulanIni >= $normalAngsuran && $normalAngsuran > 0) {
            $statusBulanText = 'Lunas Bulan Ini (+Rp ' . number_format($selisihBulanIni, 0, ',', '.') . ')';
            $statusBulanColor = 'success';
            $statusBulanIcon = 'heroicon-m-check-badge';
        } elseif ($setoranBulanIni > 0) {
            $statusBulanText = 'Kurang Bayar (Kurang Rp ' . number_format(abs($selisihBulanIni), 0, ',', '.') . ')';
            $statusBulanColor = 'warning';
            $statusBulanIcon = 'heroicon-m-exclamation-triangle';
        } else {
            $statusBulanText = 'Belum Ada Setoran (Target: Rp ' . number_format($normalAngsuran, 0, ',', '.') . ')';
            $statusBulanColor = 'danger';
            $statusBulanIcon = 'heroicon-m-x-circle';
        }

        // 3. Klaim Biaya Service Kendaraan
        $totalKlaim = (float) $record->pengeluarans()->sum('nominal');
        $sisaKlaim = (float) $record->pengeluarans->sum(fn ($p) => $p->sisa_kompensasi);
        $terkompensasi = (float) $record->pengeluarans->sum(fn ($p) => $p->total_terkompensasi);

        return [
            Stat::make('Total Nilai Kontrak', 'Rp ' . number_format($nilaiKontrak, 0, ',', '.'))
                ->description("Tenor {$record->jangka_waktu_tahun} Thn • Normal: Rp " . number_format($normalAngsuran, 0, ',', '.') . '/bln')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Setoran Terkumpul', 'Rp ' . number_format($totalSetoran, 0, ',', '.'))
                ->description("Progres Pelunasan: {$persenLunas}%")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Sisa Tagihan Kontrak', 'Rp ' . number_format($sisaKontrak, 0, ',', '.'))
                ->description('Jatuh Tempo: ' . ($record->tgl_jatuh_tempo_akhir ? Carbon::parse($record->tgl_jatuh_tempo_akhir)->format('d/m/Y') : '-'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($sisaKontrak <= 0 ? 'success' : ($record->status === 'menunggak' ? 'danger' : 'warning')),

            Stat::make("Setoran {$namaBulan}", 'Rp ' . number_format($setoranBulanIni, 0, ',', '.'))
                ->description($statusBulanText)
                ->descriptionIcon($statusBulanIcon)
                ->color($statusBulanColor),

            Stat::make('Klaim Service Kendaraan', 'Rp ' . number_format($sisaKlaim, 0, ',', '.'))
                ->description("Total Klaim: Rp " . number_format($totalKlaim, 0, ',', '.') . " • Terkompensasi: Rp " . number_format($terkompensasi, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color($sisaKlaim > 0 ? 'amber' : 'success'),
        ];
    }
}
