<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Klaim Biaya Service & Kendala Kendaraan</title>
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
            border-bottom: 2px solid #b45309;
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
            color: #b45309;
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
            width: 120px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        table.data th {
            background-color: #b45309;
            color: #ffffff;
            font-weight: bold;
            font-size: 8.5px;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #b45309;
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

        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-diajukan { background-color: #f1f5f9; color: #475569; }
        .badge-disetujui { background-color: #dbeafe; color: #1e40af; }
        .badge-dikompensasi_sebagian { background-color: #fef3c7; color: #92400e; }
        .badge-sudah_dikompensasi { background-color: #d1fae5; color: #065f46; }
        .badge-ditolak { background-color: #fee2e2; color: #991b1b; }

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
                <h2>Laporan Klaim Biaya Service Kendaraan</h2>
                <p>PT. Samana Jaya Propertindo &bull; Penggantian Biaya Talangan Penyewa</p>
                <p>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </td>
        </tr>
    </table>

    @if($angsuran)
    <table class="meta-table">
        <tr>
            <td class="label">Nama Penyewa</td>
            <td>: <strong>{{ $angsuran->driver?->user?->name ?? '-' }}</strong> (No WA: {{ $angsuran->driver?->no_wa ?? '-' }})</td>
            <td class="label">No Pol / Kendaraan</td>
            <td>: <strong>{{ $angsuran->unit?->nopol ?? '-' }}</strong> &bull; {{ ($angsuran->unit?->merk ?? '') . ' ' . ($angsuran->unit?->type ?? '') }}</td>
        </tr>
        @if(!empty($startDate) || !empty($endDate))
        <tr>
            <td class="label">Periode Tanggal</td>
            <td colspan="3" style="color: #b45309; font-weight: bold;">: {{ !empty($startDate) ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} s/d {{ !empty($endDate) ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Sekarang' }}</td>
        </tr>
        @endif
    </table>
    @else
    <table class="meta-table">
        <tr>
            <td class="label">Total Pengeluaran</td>
            <td>: {{ count($records) }} Nota Klaim</td>
            <td class="label">Cakupan Data</td>
            <td>: Semua Klaim Biaya Service</td>
        </tr>
    </table>
    @endif

    <table class="data">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 100px;">Penyewa</th>
                <th style="width: 70px;">No Pol</th>
                <th style="width: 75px;">Tgl Nota</th>
                <th style="width: 130px;">Jenis Perbaikan / Kendala</th>
                <th style="width: 85px;">Biaya Nota</th>
                <th style="width: 85px;">Terkompensasi</th>
                <th style="width: 85px;">Sisa Saldo</th>
                <th style="width: 80px;">Status Klaim</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotalBiaya = 0;
                $grandTerkompensasi = 0;
                $grandSisa = 0;
            @endphp
            @forelse($records as $index => $row)
                @php
                    $grandTotalBiaya += (float) $row->nominal;
                    $grandTerkompensasi += (float) $row->total_terkompensasi;
                    $grandSisa += (float) $row->sisa_kompensasi;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $row->angsuranKendaraan?->driver?->user?->name ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $row->angsuranKendaraan?->unit?->nopol ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $row->tgl_pengeluaran ? \Carbon\Carbon::parse($row->tgl_pengeluaran)->format('d/m/Y') : '-' }}</td>
                    <td class="font-bold">{{ $row->jenis_pengeluaran ?? '-' }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($row->nominal, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #047857;">Rp {{ number_format($row->total_terkompensasi, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="color: #b45309;">Rp {{ number_format($row->sisa_kompensasi, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge badge-{{ $row->status ?? 'diajukan' }}">
                            {{ match($row->status) {
                                'diajukan' => 'Diajukan',
                                'disetujui' => 'Disetujui',
                                'dikompensasi_sebagian' => 'Sebagian',
                                'sudah_dikompensasi' => 'Lunas',
                                'ditolak' => 'Ditolak',
                                default => ucfirst($row->status ?? 'Diajukan')
                            } }}
                        </span>
                    </td>
                    <td>{{ $row->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 12px; color: #64748b;">Belum ada data klaim biaya perbaikan / service.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($records) > 0)
        <tfoot>
            <tr style="background-color: #fef3c7; font-weight: bold;">
                <td colspan="5" class="text-right" style="padding: 5px 8px;">TOTAL REKAPITULASI:</td>
                <td class="text-right">Rp {{ number_format($grandTotalBiaya, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #047857;">Rp {{ number_format($grandTerkompensasi, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #b45309;">Rp {{ number_format($grandSisa, 0, ',', '.') }}</td>
                <td colspan="2"></td>
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
                <p>Mengetahui & Menyetujui,</p>
                <div class="signature-space"></div>
                <p style="font-weight: bold; text-decoration: underline;">Bagian Service & Maintenance</p>
                <p style="color: #64748b;">PT. Samana Jaya Propertindo</p>
            </td>
        </tr>
    </table>
</body>
</html>
