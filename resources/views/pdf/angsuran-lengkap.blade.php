<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Lengkap Kontrak Sewa & Riwayat Relasi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            color: #1e293b;
            padding: 16px 20px;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header-logo {
            width: 120px;
            text-align: left;
            vertical-align: middle;
        }

        .header-logo img {
            width: 110px;
            height: auto;
        }

        .header-title {
            text-align: right;
            vertical-align: middle;
        }

        .header-title h2 {
            font-size: 14px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .header-title p {
            font-size: 8.5px;
            color: #64748b;
        }

        .profile-card {
            width: 100%;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 12px;
        }

        .profile-table {
            width: 100%;
            border-collapse: collapse;
        }

        .profile-table td {
            padding: 2.5px 4px;
            font-size: 8.5px;
            vertical-align: top;
        }

        .profile-table td.lbl {
            font-weight: bold;
            color: #475569;
            width: 120px;
        }

        .section-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #0f172a;
            margin: 10px 0 5px 0;
            padding-bottom: 3px;
            border-bottom: 1.5px solid #cbd5e1;
            text-transform: uppercase;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.data th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            font-size: 8px;
            padding: 5px 4px;
            text-align: center;
            border: 1px solid #1e3a8a;
            text-transform: uppercase;
        }

        table.data th.green {
            background-color: #047857;
            border-color: #047857;
        }

        table.data th.amber {
            background-color: #b45309;
            border-color: #b45309;
        }

        table.data td {
            padding: 4px 4px;
            border: 1px solid #cbd5e1;
            font-size: 8px;
            vertical-align: middle;
        }

        table.data tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }

        .badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-aktif, .badge-disetujui { background-color: #dbeafe; color: #1e40af; }
        .badge-lunas, .badge-sudah_dikompensasi { background-color: #d1fae5; color: #065f46; }
        .badge-menunggak, .badge-ditolak { background-color: #fee2e2; color: #991b1b; }
        .badge-dikompensasi_sebagian { background-color: #fef3c7; color: #92400e; }
        .badge-diajukan { background-color: #f1f5f9; color: #475569; }

        .signature-table {
            width: 100%;
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            font-size: 8.5px;
            vertical-align: top;
        }

        .signature-space {
            height: 40px;
        }
    </style>
</head>
<body>
    @foreach($angsurans as $angsuran)
    @if(!$loop->first)
        <div style="page-break-before: always;"></div>
    @endif

    <table class="header-table">
        <tr>
            <td class="header-logo">
                <img src="{{ public_path('images/logo_spj_samarent.jpg') }}" alt="Samarent Logo">
            </td>
            <td class="header-title">
                <h2>Laporan Kontrak Sewa & Riwayat Pembayaran</h2>
                <p>PT. Samana Jaya Propertindo &bull; Unit: {{ $angsuran->unit?->nopol ?? '-' }} &bull; Penyewa: {{ $angsuran->driver?->user?->name ?? '-' }}</p>
                @if(!empty($startDate) || !empty($endDate))
                    <p style="color: #1e3a8a; font-weight: bold;">Periode: {{ !empty($startDate) ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} s/d {{ !empty($endDate) ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Sekarang' }}</p>
                @endif
                <p>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </td>
        </tr>
    </table>

    <div class="profile-card">
        <table class="profile-table">
            <tr>
                <td class="lbl">Nama Penyewa</td>
                <td style="width: 220px;">: <strong>{{ $angsuran->driver?->user?->name ?? '-' }}</strong> (HP: {{ $angsuran->driver?->no_wa ?? '-' }})</td>
                <td class="lbl">Angsuran / Hari</td>
                <td>: <strong>Rp {{ number_format($angsuran->angsuran_per_hari, 0, ',', '.') }}</strong> ({{ $angsuran->frekuensi_per_bulan }} Hari/Bln)</td>
            </tr>
            <tr>
                <td class="lbl">No Pol / Unit</td>
                <td>: <strong>{{ $angsuran->unit?->nopol ?? '-' }}</strong> &bull; {{ ($angsuran->unit?->merk ?? '') . ' ' . ($angsuran->unit?->type ?? '') }}</td>
                <td class="lbl">Normal Angsuran</td>
                <td>: <strong>Rp {{ number_format($angsuran->normal_angsuran, 0, ',', '.') }}</strong> / Bulan</td>
            </tr>
            <tr>
                <td class="lbl">Serah Terima s/d Akhir</td>
                <td>: {{ $angsuran->tgl_serah_terima ? \Carbon\Carbon::parse($angsuran->tgl_serah_terima)->format('d/m/Y') : '-' }} s/d {{ $angsuran->tgl_jatuh_tempo_akhir ? \Carbon\Carbon::parse($angsuran->tgl_jatuh_tempo_akhir)->format('d/m/Y') : '-' }} ({{ $angsuran->jangka_waktu_tahun }} Thn)</td>
                <td class="lbl">Nilai Kontrak Total</td>
                <td>: <strong style="color: #1e3a8a;">Rp {{ number_format($angsuran->nilai_kontrak, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td class="lbl">DP / Deposit</td>
                <td>: DP: {{ $angsuran->dp ? 'Rp ' . number_format($angsuran->dp, 0, ',', '.') : '-' }} &bull; Deposit: {{ $angsuran->deposit ? 'Rp ' . number_format($angsuran->deposit, 0, ',', '.') : '-' }}</td>
                <td class="lbl">Total Setoran Masuk</td>
                <td>: <strong style="color: #065f46;">Rp {{ number_format($angsuran->total_setoran_keseluruhan, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td class="lbl">Status Kontrak</td>
                <td>: <span class="badge badge-{{ $angsuran->status ?? 'aktif' }}">{{ ucfirst($angsuran->status ?? 'Aktif') }}</span></td>
                <td class="lbl">Sisa Kontrak</td>
                <td>: <strong style="color: #991b1b;">Rp {{ number_format($angsuran->sisa_kontrak, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    {{-- BAGIAN 1: RIWAYAT SETORAN HARIAN --}}
    @php
        $setoranQuery = $angsuran->setorans()->orderBy('tgl_setor', 'asc');
        if (!empty($startDate)) {
            $setoranQuery->whereDate('tgl_setor', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $setoranQuery->whereDate('tgl_setor', '<=', $endDate);
        }
        $listSetorans = $setoranQuery->get();
    @endphp
    <div class="section-title" style="color: #047857;">1. Riwayat Setoran Harian (Total: {{ $listSetorans->count() }} Transaksi)</div>
    <table class="data">
        <thead>
            <tr>
                <th class="green" style="width: 25px;">No</th>
                <th class="green" style="width: 80px;">Tanggal Setor</th>
                <th class="green" style="width: 90px;">Periode</th>
                <th class="green" style="width: 100px;">Nominal Setor</th>
                <th class="green" style="width: 120px;">Metode Pembayaran</th>
                <th class="green" style="width: 160px;">Kompensasi Dari Nota</th>
                <th class="green">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $subSetoran = 0; @endphp
            @forelse($listSetorans as $idx => $setor)
                @php $subSetoran += (float) $setor->nominal; @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-bold">{{ $setor->tgl_setor ? \Carbon\Carbon::parse($setor->tgl_setor)->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::create()->month($setor->periode_bulan)->locale('id')->translatedFormat('F') }} {{ $setor->periode_tahun }}</td>
                    <td class="text-right font-bold" style="color: #065f46;">Rp {{ number_format($setor->nominal, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($setor->metode_setor === 'kompensasi_biaya')
                            <span style="color: #92400e; font-weight: bold;">Kompensasi Biaya</span>
                        @elseif($setor->metode_setor === 'transfer')
                            Transfer Bank
                        @else
                            {{ ucfirst($setor->metode_setor) }}
                        @endif
                    </td>
                    <td>{{ $setor->pengeluaran ? $setor->pengeluaran->jenis_pengeluaran . ' (' . $setor->pengeluaran->tgl_pengeluaran->format('d/m/Y') . ')' : '-' }}</td>
                    <td>{{ $setor->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 10px; color: #64748b;">Belum ada riwayat setoran harian untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if($listSetorans->count() > 0)
        <tfoot>
            <tr style="background-color: #d1fae5; font-weight: bold;">
                <td colspan="3" class="text-right" style="padding: 4px 8px;">TOTAL SETORAN TERKUMPUL:</td>
                <td class="text-right" style="color: #065f46;">Rp {{ number_format($subSetoran, 0, ',', '.') }}</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- BAGIAN 2: KLAIM BIAYA & SERVICE PENYEWA --}}
    @php
        $pengeluaranQuery = $angsuran->pengeluarans()->orderBy('tgl_pengeluaran', 'asc');
        if (!empty($startDate)) {
            $pengeluaranQuery->whereDate('tgl_pengeluaran', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $pengeluaranQuery->whereDate('tgl_pengeluaran', '<=', $endDate);
        }
        $listPengeluarans = $pengeluaranQuery->get();
    @endphp
    <div class="section-title" style="color: #b45309;">2. Klaim Biaya Kendala & Service Kendaraan (Total: {{ $listPengeluarans->count() }} Nota)</div>
    <table class="data">
        <thead>
            <tr>
                <th class="amber" style="width: 25px;">No</th>
                <th class="amber" style="width: 80px;">Tanggal Nota</th>
                <th class="amber" style="width: 140px;">Jenis Perbaikan / Kendala</th>
                <th class="amber" style="width: 90px;">Total Biaya</th>
                <th class="amber" style="width: 90px;">Terkompensasi</th>
                <th class="amber" style="width: 90px;">Sisa Saldo</th>
                <th class="amber" style="width: 90px;">Status Klaim</th>
                <th class="amber">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $subTotalBiaya = 0;
                $subTerkompensasi = 0;
                $subSisaBiaya = 0;
            @endphp
            @forelse($listPengeluarans as $idx2 => $peng)
                @php
                    $subTotalBiaya += (float) $peng->nominal;
                    $subTerkompensasi += (float) $peng->total_terkompensasi;
                    $subSisaBiaya += (float) $peng->sisa_kompensasi;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx2 + 1 }}</td>
                    <td class="text-center font-bold">{{ $peng->tgl_pengeluaran ? \Carbon\Carbon::parse($peng->tgl_pengeluaran)->format('d/m/Y') : '-' }}</td>
                    <td class="font-bold">{{ $peng->jenis_pengeluaran ?? '-' }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($peng->nominal, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #065f46;">Rp {{ number_format($peng->total_terkompensasi, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="color: #b45309;">Rp {{ number_format($peng->sisa_kompensasi, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge badge-{{ $peng->status ?? 'diajukan' }}">
                            {{ match($peng->status) {
                                'diajukan' => 'Diajukan',
                                'disetujui' => 'Disetujui',
                                'dikompensasi_sebagian' => 'Sebagian',
                                'sudah_dikompensasi' => 'Lunas Kompensasi',
                                'ditolak' => 'Ditolak',
                                default => ucfirst($peng->status ?? 'Diajukan')
                            } }}
                        </span>
                    </td>
                    <td>{{ $peng->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 10px; color: #64748b;">Belum ada klaim biaya service / kendala yang dicatat untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if($listPengeluarans->count() > 0)
        <tfoot>
            <tr style="background-color: #fef3c7; font-weight: bold;">
                <td colspan="3" class="text-right" style="padding: 4px 8px;">TOTAL BIAYA SERVICE & KOMPENSASI:</td>
                <td class="text-right">Rp {{ number_format($subTotalBiaya, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #065f46;">Rp {{ number_format($subTerkompensasi, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #b45309;">Rp {{ number_format($subSisaBiaya, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <table class="signature-table">
        <tr>
            <td>
                <p>Penyewa / Driver,</p>
                <div class="signature-space"></div>
                <p style="font-weight: bold; text-decoration: underline;">{{ $angsuran->driver?->user?->name ?? 'Penyewa' }}</p>
                <p style="color: #64748b;">No Pol: {{ $angsuran->unit?->nopol ?? '-' }}</p>
            </td>
            <td>
                <p>PT. Samana Jaya Propertindo,</p>
                <div class="signature-space"></div>
                <p style="font-weight: bold; text-decoration: underline;">Bagian Operasional / Keuangan</p>
                <p style="color: #64748b;">Samarent Operational System</p>
            </td>
        </tr>
    </table>
    @endforeach
</body>
</html>
