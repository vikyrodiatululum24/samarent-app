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
    </style>
</head>

<body>
    <div>
        <div class="header">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <th style="width: 20%; text-align: center;">
                        <img src="{{ public_path('images/logo_spj_samarent.jpg') }}" alt="logo samarent"
                            style="width: 150px;">
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
                        <th width="10%" class="text-right">Metode Pembayaran</th>
                        <th width="10%" class="text-right">Dana Masuk</th>
                        <th width="10%" class="text-right">Dana Keluar</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $cashMasuk = 0;
                        $cashKeluar = 0;
                        $cashSaldo = 0;
                        $fleetCardMasuk = 0;
                        $fleetCardKeluar = 0;
                        $fleetCardSaldo = 0;
                    @endphp
                    @foreach ($reimbursements as $index => $item)
                        @php
                            $metodePembayaran = strtolower((string) ($item->metode_pembayaran ?? ''));

                            if ($metodePembayaran === 'cash') {
                                $cashMasuk += $item->dana_masuk ?? 0;
                                $cashKeluar += $item->dana_keluar ?? 0;
                                $cashSaldo = $cashMasuk - $cashKeluar;
                            }

                            if ($metodePembayaran === 'fleet card' || $metodePembayaran === 'fleet_card') {
                                $fleetCardMasuk += $item->dana_masuk ?? 0;
                                $fleetCardKeluar += $item->dana_keluar ?? 0;
                                $fleetCardSaldo = $fleetCardMasuk - $fleetCardKeluar;
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d-M-Y') }}</td>
                            <td>{{ $item->keterangan ?? '-' }}</td>
                            <td>{{ $item->metode_pembayaran ? ($item->metode_pembayaran === 'cash' ? 'Cash' : 'Fleet Card') : '-' }}
                            </td>
                            <td class="text-right">
                                {{ $item->dana_masuk ? number_format($item->dana_masuk, 0, ',', '.') : '-' }}</td>
                            <td class="text-right">
                                {{ $item->dana_keluar ? number_format($item->dana_keluar, 0, ',', '.') : '-' }}</td>
                        </tr>
                    @endforeach
                    <tr style="font-weight: bold;">
                        <td colspan="4" style="border-top: none; height: 5px;">Total Dana Masuk Cash</td>
                        <td colspan="2" class="text-right" style="border-top: none; height: 5px;">
                            {{ number_format($cashMasuk, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="font-weight: bold;">
                        <td colspan="4" style="border-top: none; height: 5px;">Total Dana Keluar Cash</td>
                        <td colspan="2" class="text-right" style="border-top: none; height: 5px;">
                            {{ number_format($cashKeluar, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="font-weight: bold;">
                        <td colspan="4" style="border-top: none; height: 5px;">Saldo Cash</td>
                        <td colspan="2" class="text-right" style="border-top: none; height: 5px;">
                            {{ number_format($cashSaldo, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="font-weight: bold;">
                        <td colspan="4" style="border-top: none; height: 5px;">Total Dana Masuk Fleet Card</td>
                        <td colspan="2" class="text-right" style="border-top: none; height: 5px;">
                            {{ number_format($fleetCardMasuk, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="font-weight: bold;">
                        <td colspan="4" style="border-top: none; height: 5px;">Total Dana Keluar Fleet Card</td>
                        <td colspan="2" class="text-right" style="border-top: none; height: 5px;">
                            {{ number_format($fleetCardKeluar, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="font-weight: bold;">
                        <td colspan="4" style="border-top: none; height: 5px;">Saldo Fleet Card</td>
                        <td colspan="2" class="text-right" style="border-top: none; height: 5px;">
                            {{ number_format($fleetCardSaldo, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="no-data">
                Tidak ada data reimbursement untuk periode yang dipilih
            </div>
        @endif

        @if (!empty($group_signature->rule_signatures))
            @php
                // Hitung total kolom (total semua signatures di semua rule)
                $totalCols = $group_signature->rule_signatures->sum(fn($r) => $r->signatures->count());
                $colWidth = $totalCols > 0 ? round(100 / $totalCols, 4) : 25;
            @endphp
            @if ($group_signature->project_id == 2 && $group_signature->branch->name !== 'HO')
                <div class="signature">
                    <table style="width: 70%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; margin-left: auto; margin-right: auto;">
                        {{-- Paksa lebar kolom sama rata --}}
                        
                        <tr>
                            <td colspan="3"
                                style="border: 1px solid #ddd; padding: 0.5rem; text-align: center; font-size: 12px;">
                                HEAD OFFICE</td>
                        </tr>

                        {{-- Row 1: Header tiap rule (colspan = jumlah signatures di rule itu) --}}
                        <tr>
                            <th style="border: 1px solid #ddd; padding: 0.5rem; text-align: center;">Approved By</th>
                            <th colspan="2" style="border: 1px solid #ddd; padding: 0.5rem; text-align: center;">
                                Checked By</th>
                        </tr>

                        {{-- Row 2: Jabatan --}}
                        <tr>
                            <td style="text-transform: capitalize; font-size: 11px; border: 1px solid #ddd; padding: 0.5rem; text-align: center;">Direktur</td>
                            <td style="text-transform: capitalize; font-size: 11px; border: 1px solid #ddd; padding: 0.5rem; text-align: center;">Finance</td>
                            <td style="text-transform: capitalize; font-size: 11px; border: 1px solid #ddd; padding: 0.5rem; text-align: center;">GA</td>
                        </tr>

                        {{-- Row 3: Gambar TTD --}}
                        <tr>
                            <td style="height: 70px; border: 1px solid #ddd; padding: 0.5rem; text-align: center;">
                                &nbsp;</td>
                            <td style="height: 70px; border: 1px solid #ddd; padding: 0.5rem; text-align: center;">
                                &nbsp;</td>
                            <td style="height: 70px; border: 1px solid #ddd; padding: 0.5rem; text-align: center;">
                                &nbsp;</td>
                        </tr>

                        {{-- Row 4: Nama --}}
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 0.5rem; text-align: left;">Name :</td>
                            <td style="border: 1px solid #ddd; padding: 0.5rem; text-align: left;">Name :</td>
                            <td style="border: 1px solid #ddd; padding: 0.5rem; text-align: left;">Name :</td>
                        </tr>
                    </table>
                </div>
            @endif
            <div class="signature">
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed;">
                    {{-- Paksa lebar kolom sama rata --}}
                    <colgroup>
                        @for ($i = 0; $i < $totalCols; $i++)
                            <col style="width: {{ $colWidth }}%;">
                        @endfor
                    </colgroup>

                    @if($group_signature->project_id == 2 && $group_signature->branch->name !== 'HO')
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 0.5rem; text-align: center;"
                            colspan="{{ $totalCols }}">
                            SITE
                        </td>
                    </tr>
                    @endif

                    {{-- Row 1: Header tiap rule (colspan = jumlah signatures di rule itu) --}}
                    <tr>
                        @foreach ($group_signature->rule_signatures as $rule_signature)
                            <th style="border: 1px solid #ddd; padding: 0.5rem; text-align: center; font-size: 12px;"
                                colspan="{{ $rule_signature->signatures->count() }}" rowspan="{{ $rule_signature->signatures->first()->jabatan == '-' ? '2' : '1' }}" >
                                {{ $rule_signature->rules }}
                            </th>
                        @endforeach
                    </tr>

                    {{-- Row 2: Jabatan --}}
                    <tr>
                        @foreach ($group_signature->rule_signatures as $rule_signature)
                            @foreach ($rule_signature->signatures as $signature)
                                @if ($signature->jabatan !== '-')
                                <td
                                    style="padding: 0.5rem; border: 1px solid #ddd; text-align: center; vertical-align: middle; text-transform: capitalize; font-size: 11px;">
                                    {{ $signature->jabatan }}
                                </td>
                                @endif
                            @endforeach
                        @endforeach
                    </tr>

                    {{-- Row 3: Gambar TTD --}}
                    <tr>
                        @foreach ($group_signature->rule_signatures as $rule_signature)
                            @foreach ($rule_signature->signatures as $signature)
                                <td
                                    style="height: 78px; border: 1px solid #ddd; text-align: center; vertical-align: middle;">
                                    @if ($signature->ttd)
                                        <img src="{{ public_path('storage/' . $signature->ttd) }}"
                                            alt="{{ $signature->nama }}"
                                            style="max-width: 90%; max-height: 70px; object-fit: contain;">
                                    @else
                                        &nbsp;
                                    @endif
                                </td>
                            @endforeach
                        @endforeach
                    </tr>

                    {{-- Row 4: Nama --}}
                    <tr>
                        @foreach ($group_signature->rule_signatures as $rule_signature)
                            @foreach ($rule_signature->signatures as $signature)
                                <td
                                    style="padding: 0.5rem; border: 1px solid #ddd; text-align: left; padding-left: 0.5rem ; vertical-align: middle; text-transform: capitalize; font-size: 11px; overflow: hidden;">
                                    @if ($signature->nama)
                                        Name : {{ $signature->nama }}
                                    @else
                                        Name :
                                    @endif
                                </td>
                            @endforeach
                        @endforeach
                    </tr>
                </table>
            </div>
        @else
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
                                            alt="KM Awal" style="width: 150px">
                                    @endif
                                </td>
                                <td class="text-right">{{ number_format($item->km_awal) }}</td>
                                <td>
                                    @if ($item->foto_odometer_akhir)
                                        <img src="{{ public_path('storage/' . $item->foto_odometer_akhir) }}"
                                            alt="KM Akhir" style="width: 150px">
                                    @endif
                                </td>
                                <td class="text-right">{{ $item->km_akhir ? number_format($item->km_akhir) : '-' }}
                                </td>
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
            <table width="100%" cellspacing="0" cellpadding="8">
                <tbody>
                    @foreach ($reimbursements->chunk(2) as $row)
                        <tr>
                            @foreach ($row as $item)
                                <td width="50%" style="border:1px solid #ddd; vertical-align:top;">
                                    <div
                                        style="font-weight:bold; text-align:center; margin-bottom:10px; margin-top: 10px">
                                        {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y') }}
                                    </div>

                                    @if ($item->nota)
                                        <img src="{{ public_path('storage/' . $item->nota) }}" alt="Foto Nota"
                                            style="width:100%; object-fit:contain;">
                                    @endif
                                </td>
                            @endforeach

                            {{-- Jika jumlah data ganjil --}}
                            @if ($row->count() < 2)
                                <td width="50%"></td>
                            @endif
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
