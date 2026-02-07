<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Reimbursement</title>
    <link rel="icon" type="image/png" href="{{ public_path('images/icon.png') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            padding: 20px;
        }

        .header {
            text-align: center;
        }

        .header h2 {
            margin-bottom: 5px;
            font-size: 18px;
        }

        .header p {
            font-size: 12px;
            color: #666;
        }

        .info {
            margin-bottom: 15px;
        }

        .info table {
            width: 100%;
        }

        .info td {
            padding: 3px 0;
        }

        .info td:first-child {
            width: 120px;
            font-weight: bold;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data th {
            background-color: #ddd;
            padding: 8px 5px;
            text-align: left;
            font-size: 10px;
            border: 1px solid #ddd;
        }

        table.data td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        table.data tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
        }

        .summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }

        .summary table {
            width: 100%;
        }

        .summary td {
            padding: 5px;
        }

        .summary td:first-child {
            font-weight: bold;
            width: 150px;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }

        .signature {
            margin-top: 40px;
        }

        img {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>

<body>
    <div>
        <div class="header">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <th style="width: 20%; text-align: center;">
                        <img src="{{ public_path('images/logo_spj_samarent.jpg') }}" alt="logo samarent">
                    </th>
                    <th style="width: 80%; text-align: center; position: relative;">
                        <div style="position: relative; text-align: center;">
                            <h2 style="text-transform: uppercase; margin: 0;">
                                LAPORAN BIAYA PENGELUARAN OPERASIONAL
                            </h2>
                        </div>
                    </th>
                </tr>
            </table>
        </div>

        <div class="info">
            <table>
                <tr>
                    <td>Company Name</td>
                    <td>: PT. Samana Jaya Propertindo</td>
                </tr>
                <tr>
                    <td>Name</td>
                    <td>: {{ $user->name }}</td>
                </tr>
                <tr>
                    <td>No. Rekening</td>
                    <td>: {{ $user->profile->norek ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Periode</td>
                    <td>: {{ $dari ? \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') : '-' }} s/d
                        {{ $sampai ? \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') : '-' }}</td>
                </tr>
            </table>
        </div>

        @if ($reimbursements->count() > 0)
            <table class="data">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="10%">Tanggal</th>
                        <th width="45%" class="text-right">Keterangan</th>
                        <th width="20%" class="text-right">Dana Masuk</th>
                        <th width="20%" class="text-right">Dana Keluar</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalMasuk = 0;
                        $totalKeluar = 0;
                    @endphp
                    @foreach ($reimbursements as $index => $item)
                        @php
                            $totalMasuk += $item->dana_masuk ?? 0;
                            $totalKeluar += $item->dana_keluar ?? 0;
                            $saldo = $totalMasuk - $totalKeluar;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d-M-Y') }}</td>
                            <td>{{ $item->keterangan ?? '-' }}</td>
                            <td class="text-right">
                                {{ $item->dana_masuk ? number_format($item->dana_masuk, 0, ',', '.') : '-' }}</td>
                            <td class="text-right">
                                {{ $item->dana_keluar ? number_format($item->dana_keluar, 0, ',', '.') : '-' }}</td>
                        </tr>
                    @endforeach
                    <tr style="font-weight: bold;">
                        <td colspan="3" style="border-top: none; height: 5px;">Total Dana Masuk</td>
                        <td colspan="2" class="text-right" style="border-top: none; height: 5px;">
                            {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="font-weight: bold;">
                        <td colspan="3" style="border-top: none; height: 5px;">Total Dana Keluar</td>
                        <td colspan="2" class="text-right" style="border-top: none; height: 5px;">
                            {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="font-weight: bold;">
                        <td colspan="3" style="border-top: none; height: 5px;">Saldo Akhir</td>
                        <td colspan="2" class="text-right" style="border-top: none; height: 5px;">
                            {{ number_format($saldo, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="no-data">
                Tidak ada data reimbursement untuk periode yang dipilih
            </div>
        @endif

        <div class="signature">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <th style="border: 1px solid #ddd; padding : 0.5rem; width: 20%;">Diajukan</th>
                    <th style="border: 1px solid #ddd; padding : 0.5rem; width: 20%;">Disetujui</th>
                    <th style="border: 1px solid #ddd; padding : 0.5rem; width: 20%;">Diketahui</th>
                    <th style="border: 1px solid #ddd; padding : 0.5rem; width: 20%;">Diterima</th>
                </tr>
                <tr>
                    <td style="height: 78px; border: 1px solid #ddd; text-align: center; vertical-align: middle;">
                        &nbsp;
                    </td>
                    <td
                        style="height: 70px; font-size: 12px; border: 1px solid #ddd; text-align: center; vertical-align: middle;">
                        &nbsp;
                    </td>
                    <td
                        style="height: 70px; font-size: 12px; border: 1px solid #ddd; text-align: center; vertical-align: middle;">
                        &nbsp;
                    </td>
                    <td
                        style="height: 70px; font-size: 12px; border: 1px solid #ddd; text-align: center; vertical-align: middle;">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td
                        style="height: 38px; border: 1px solid #ddd; text-align: center; vertical-align: middle; text-transform: capitalize;">
                        {{ $user->name }}</td>
                    <td
                        style="height: 38px; border: 1px solid #ddd; text-align: center; vertical-align: middle; text-transform: capitalize;">
                        &nbsp;</td>
                    <td
                        style="height: 38px; border: 1px solid #ddd; text-align: center; vertical-align: middle; text-transform: capitalize;">
                        &nbsp;</td>
                    <td
                        style="height: 38px; border: 1px solid #ddd; text-align: center; vertical-align: middle; text-transform: capitalize;">
                        &nbsp;</td>
                </tr>
            </table>
        </div>
    </div>
    <div style="page-break-before: always;">
        <div class="header">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <th style="width: 20%; text-align: center;">
                        <img src="{{ public_path('images/logo_spj_samarent.jpg') }}" alt="logo samarent"
                            width="150">
                    </th>
                    <th style="width: 80%; text-align: center; position: relative;">
                        <div style="position: relative; text-align: center;">
                            <h2 style="text-transform: uppercase; margin: 0;">
                                LAPORAN BIAYA PENGELUARAN OPERASIONAL
                            </h2>
                        </div>
                    </th>
                </tr>
            </table>
        </div>

        <div class="info">
            <table>
                <tr>
                    <td>Company Name</td>
                    <td>: PT. Samana Jaya Propertindo</td>
                </tr>
                <tr>
                    <td>Name</td>
                    <td>: {{ $user->name }}</td>
                </tr>
                <tr>
                    <td>No. Rekening</td>
                    <td>: {{ $user->profile->norek ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Periode</td>
                    <td>: {{ $dari ? \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') : '-' }} s/d
                        {{ $sampai ? \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') : '-' }}</td>
                </tr>
            </table>
        </div>

        @if ($reimbursements->count() > 0)
            <table class="data">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="10%">Tanggal</th>
                        <th width="12%">Foto KM Awal</th>
                        <th width="10%" class="text-right">KM Awal</th>
                        <th width="12%">Foto KM Akhir</th>
                        <th width="10%" class="text-right">KM Akhir</th>
                        <th width="10%" class="text-right">Jarak</th>
                        <th width="31%">Tujuan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalMasuk = 0;
                        $totalKeluar = 0;
                        $totalJarak = 0;
                        $no = 1;
                    @endphp
                    @foreach ($reimbursements as $index => $item)
                        @if ($item->type === 'bbm')
                            @php
                                $jarak = $item->km_akhir ? $item->km_akhir - $item->km_awal : 0;
                                $saldo = ($item->dana_masuk ?? 0) - ($item->dana_keluar ?? 0);
                                $totalMasuk += $item->dana_masuk ?? 0;
                                $totalKeluar += $item->dana_keluar ?? 0;
                                $totalJarak += $jarak;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d-M-Y') }}</td>
                                <td>
                                    @if ($item->foto_odometer_awal)
                                        <img src="{{ public_path('storage/' . $item->foto_odometer_awal) }}"
                                            alt="KM Awal">
                                    @endif
                                </td>
                                <td class="text-right">{{ number_format($item->km_awal) }}</td>
                                <td>
                                    @if ($item->foto_odometer_akhir)
                                        <img src="{{ public_path('storage/' . $item->foto_odometer_akhir) }}"
                                            alt="KM Akhir">
                                    @endif
                                </td>
                                <td class="text-right">{{ $item->km_akhir ? number_format($item->km_akhir) : '-' }}</td>
                                <td class="text-right">{{ $jarak > 0 ? number_format($jarak) : '-' }}</td>
                                <td>{{ $item->tujuan_perjalanan ?? '-' }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                Tidak ada data reimbursement untuk periode yang dipilih
            </div>
        @endif
    </div>
    <div style="page-break-before: always;">
        <div class="header">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <th style="width: 20%; text-align: center;">
                        <img src="{{ public_path('images/logo_spj_samarent.jpg') }}" alt="logo samarent"
                            width="150">
                    </th>
                    <th style="width: 80%; text-align: center; position: relative;">
                        <div style="position: relative; text-align: center;">
                            <h2 style="text-transform: uppercase; margin: 0;">
                                LAPORAN BIAYA PENGELUARAN OPERASIONAL
                            </h2>
                        </div>
                    </th>
                </tr>
            </table>
        </div>

        <div class="info">
            <table>
                <tr>
                    <td>Company Name</td>
                    <td>: PT. Samana Jaya Propertindo</td>
                </tr>
                <tr>
                    <td>Name</td>
                    <td>: {{ $user->name }}</td>
                </tr>
                <tr>
                    <td>No. Rekening</td>
                    <td>: {{ $user->profile->norek ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Periode</td>
                    <td>: {{ $dari ? \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') : '-' }} s/d
                        {{ $sampai ? \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') : '-' }}</td>
                </tr>
            </table>
        </div>

        @if ($reimbursements->count() > 0)
            <table class="data">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="10%">Tanggal</th>
                        <th width="12%">Foto Nota</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reimbursements as $index => $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d-M-Y') }}</td>
                            <td>
                                @if ($item->nota)
                                    <img src="{{ public_path('storage/' . $item->nota) }}"
                                        alt="Foto Nota">
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                Tidak ada data reimbursement untuk periode yang dipilih
            </div>
        @endif
    </div>
</body>

</html>
