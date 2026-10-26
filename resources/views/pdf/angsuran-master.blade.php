<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kontrak Sewa & Angsuran Kendaraan</title>
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
            border-bottom: 2px solid #1e3a8a;
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
            color: #1e3a8a;
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
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            font-size: 8px;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #1e3a8a;
            text-transform: uppercase;
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
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-aktif { background-color: #dbeafe; color: #1e40af; }
        .badge-lunas { background-color: #d1fae5; color: #065f46; }
        .badge-menunggak { background-color: #fee2e2; color: #991b1b; }
        .badge-batal { background-color: #f1f5f9; color: #475569; }

        .summary-box {
            width: 100%;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 16px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 3px 6px;
            font-size: 8.5px;
        }

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
                <h2>Laporan Kontrak Sewa & Angsuran Kendaraan</h2>
                <p>PT. Samana Jaya Propertindo &bull; Sistem Manajemen Sewa Operasional</p>
                <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td class="label">Total Kontrak</td>
            <td>: {{ count($records) }} Unit</td>
            <td class="label">Filter Status</td>
            <td>: {{ $status ? ucfirst($status) : 'Semua Status' }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 20px;">No</th>
                <th style="width: 85px;">Nama Penyewa</th>
                <th style="width: 60px;">No Pol</th>
                <th style="width: 75px;">Kendaraan</th>
                <th style="width: 55px;">Serah Terima</th>
                <th style="width: 55px;">Akhir Sewa</th>
                <th style="width: 35px;">Tenor</th>
                <th style="width: 55px;">DP</th>
                <th style="width: 55px;">Angs/Hari</th>
                <th style="width: 65px;">Normal Angs</th>
                <th style="width: 70px;">Nilai Kontrak</th>
                <th style="width: 65px;">Terkumpul</th>
                <th style="width: 65px;">Sisa Kontrak</th>
                <th style="width: 50px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandNilaiKontrak = 0;
                $grandSetoran = 0;
                $grandSisa = 0;
            @endphp
            @forelse($records as $index => $row)
                @php
                    $grandNilaiKontrak += (float) $row->nilai_kontrak;
                    $grandSetoran += (float) $row->total_setoran_keseluruhan;
                    $grandSisa += (float) $row->sisa_kontrak;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $row->driver?->user?->name ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $row->unit?->nopol ?? '-' }}</td>
                    <td>{{ ($row->unit?->merk ?? '') . ' ' . ($row->unit?->type ?? '') }}</td>
                    <td class="text-center">{{ $row->tgl_serah_terima ? \Carbon\Carbon::parse($row->tgl_serah_terima)->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ $row->tgl_jatuh_tempo_akhir ? \Carbon\Carbon::parse($row->tgl_jatuh_tempo_akhir)->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ $row->jangka_waktu_tahun }} Thn</td>
                    <td class="text-right">{{ $row->dp ? 'Rp ' . number_format($row->dp, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">Rp {{ number_format($row->angsuran_per_hari, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row->normal_angsuran, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($row->nilai_kontrak, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #065f46; font-weight: bold;">Rp {{ number_format($row->total_setoran_keseluruhan, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #991b1b; font-weight: bold;">Rp {{ number_format($row->sisa_kontrak, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge badge-{{ $row->status ?? 'aktif' }}">{{ ucfirst($row->status ?? 'Aktif') }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="text-center" style="padding: 15px; color: #64748b;">Belum ada data kontrak angsuran kendaraan.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($records) > 0)
        <tfoot>
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <td colspan="10" class="text-right" style="padding: 5px 8px;">TOTAL KESELURUHAN:</td>
                <td class="text-right font-bold">Rp {{ number_format($grandNilaiKontrak, 0, ',', '.') }}</td>
                <td class="text-right font-bold" style="color: #065f46;">Rp {{ number_format($grandSetoran, 0, ',', '.') }}</td>
                <td class="text-right font-bold" style="color: #991b1b;">Rp {{ number_format($grandSisa, 0, ',', '.') }}</td>
                <td></td>
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
                <p style="font-weight: bold; text-decoration: underline;">Manajemen / Keuangan</p>
                <p style="color: #64748b;">PT. Samana Jaya Propertindo</p>
            </td>
        </tr>
    </table>
</body>
</html>
