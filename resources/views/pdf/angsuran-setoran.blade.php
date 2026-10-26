<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Riwayat Setoran Harian Sewa Angsuran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #1e293b;
            padding: 18px 24px;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #047857;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .header-logo {
            width: 130px;
            text-align: left;
            vertical-align: middle;
        }

        .header-logo img {
            width: 120px;
            height: auto;
        }

        .header-title {
            text-align: right;
            vertical-align: middle;
        }

        .header-title h2 {
            font-size: 15px;
            font-weight: bold;
            color: #047857;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .header-title p {
            font-size: 9px;
            color: #64748b;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9px;
        }

        .meta-table td {
            padding: 2px 4px;
        }

        .meta-table td.label {
            font-weight: bold;
            color: #475569;
            width: 110px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        table.data th {
            background-color: #047857;
            color: #ffffff;
            font-weight: bold;
            font-size: 8.5px;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #047857;
            text-transform: uppercase;
        }

        table.data td {
            padding: 4.5px 4px;
            border: 1px solid #cbd5e1;
            font-size: 8.5px;
            vertical-align: middle;
        }

        table.data tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }

        .signature-table {
            width: 100%;
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            font-size: 9px;
            vertical-align: top;
        }

        .signature-space {
            height: 45px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td class="header-logo">
                <img src="{{ public_path('images/logo_spj_samarent.jpg') }}" alt="Samarent Logo">
            </td>
            <td class="header-title">
                <h2>Laporan Riwayat Setoran Harian</h2>
                <p>PT. Samana Jaya Propertindo &bull; Sistem Monitoring Sewa Kendaraan</p>
                <p>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </td>
        </tr>
    </table>

    @if($angsuran)
    <table class="meta-table">
        <tr>
            <td class="label">Nama Penyewa</td>
            <td>: <strong>{{ $angsuran->driver?->user?->name ?? '-' }}</strong> (No WA: {{ $angsuran->driver?->no_wa ?? '-' }})</td>
            <td class="label">No Pol / Unit</td>
            <td>: <strong>{{ $angsuran->unit?->nopol ?? '-' }}</strong> &bull; {{ ($angsuran->unit?->merk ?? '') . ' ' . ($angsuran->unit?->type ?? '') }}</td>
        </tr>
        <tr>
            <td class="label">Angsuran / Hari</td>
            <td>: Rp {{ number_format($angsuran->angsuran_per_hari, 0, ',', '.') }} (Normal: Rp {{ number_format($angsuran->normal_angsuran, 0, ',', '.') }}/bln)</td>
            <td class="label">Total Setoran</td>
            <td style="color: #047857; font-weight: bold;">: Rp {{ number_format($angsuran->total_setoran_keseluruhan, 0, ',', '.') }} (Sisa: Rp {{ number_format($angsuran->sisa_kontrak, 0, ',', '.') }})</td>
        </tr>
        @if(!empty($startDate) || !empty($endDate))
        <tr>
            <td class="label">Periode Tanggal</td>
            <td colspan="3" style="color: #047857; font-weight: bold;">: {{ !empty($startDate) ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} s/d {{ !empty($endDate) ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Sekarang' }}</td>
        </tr>
        @endif
    </table>
    @else
    <table class="meta-table">
        <tr>
            <td class="label">Total Transaksi</td>
            <td>: {{ count($records) }} Setoran</td>
            <td class="label">Periode Laporan</td>
            <td>: Semua Transaksi</td>
        </tr>
    </table>
    @endif

    <table class="data">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 100px;">Penyewa</th>
                <th style="width: 70px;">No Pol</th>
                <th style="width: 75px;">Tgl Setor</th>
                <th style="width: 85px;">Periode</th>
                <th style="width: 95px;">Nominal Setor</th>
                <th style="width: 110px;">Metode Pembayaran</th>
                <th style="width: 140px;">Kompensasi Dari Nota</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $grandNominal = 0; @endphp
            @forelse($records as $index => $row)
                @php $grandNominal += (float) $row->nominal; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $row->angsuranKendaraan?->driver?->user?->name ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $row->angsuranKendaraan?->unit?->nopol ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $row->tgl_setor ? \Carbon\Carbon::parse($row->tgl_setor)->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::create()->month($row->periode_bulan)->locale('id')->translatedFormat('F') }} {{ $row->periode_tahun }}</td>
                    <td class="text-right font-bold" style="color: #047857;">Rp {{ number_format($row->nominal, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($row->metode_setor === 'kompensasi_biaya')
                            <span style="color: #b45309; font-weight: bold;">Kompensasi Biaya</span>
                        @elseif($row->metode_setor === 'transfer')
                            Transfer Bank
                        @else
                            {{ ucfirst($row->metode_setor) }}
                        @endif
                    </td>
                    <td>{{ $row->pengeluaran ? $row->pengeluaran->jenis_pengeluaran . ' (' . $row->pengeluaran->tgl_pengeluaran->format('d/m/Y') . ')' : '-' }}</td>
                    <td>{{ $row->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 12px; color: #64748b;">Belum ada data setoran yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($records) > 0)
        <tfoot>
            <tr style="background-color: #d1fae5; font-weight: bold;">
                <td colspan="5" class="text-right" style="padding: 5px 8px;">TOTAL SETORAN TERKUMPUL:</td>
                <td class="text-right font-bold" style="color: #047857;">Rp {{ number_format($grandNominal, 0, ',', '.') }}</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <table class="signature-table">
        <tr>
            <td>
                <p>Dibuat Oleh,</p>
                <div class="signature-space"></div>
                <p style="font-weight: bold; text-decoration: underline;">Admin Operasional</p>
                <p style="color: #64748b;">PT. Samana Jaya Propertindo</p>
            </td>
            <td>
                <p>Mengetahui,</p>
                <div class="signature-space"></div>
                <p style="font-weight: bold; text-decoration: underline;">Bagian Keuangan</p>
                <p style="color: #64748b;">PT. Samana Jaya Propertindo</p>
            </td>
        </tr>
    </table>
</body>
</html>
