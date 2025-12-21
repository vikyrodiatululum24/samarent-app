<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Tugas - {{ $formTugas->no_form }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.1;
            padding: 20px;
            position: relative;
        }

        body img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: auto;
        }

        .header {
            margin-top: 120px;
            text-align: center;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }

        .header p {
            font-size: 14px;
            color: #666;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 30%;
            padding: 2px;
            font-weight: bold;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            width: 70%;
            padding: 2px;
            vertical-align: top;
            text-transform: uppercase;
        }

        .section-title {
            padding: 10px 0;
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 2px;
            text-decoration: underline;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.data-table th {
            background-color: #666;
            color: white;
            padding: 4px;
            text-align: left;
            border: 1px solid #333;
        }

        table.data-table td {
            padding: 4px;
            border: 1px solid #333;
            vertical-align: top;
        }

        table.data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .cost-summary,
        .footer {
            width: 100%;
            margin-left: auto;
            margin-top: 10px;
        }

        .cost-summary table,
        .footer table {
            width: 100%;
            border-collapse: collapse;
        }

        .cost-summary td,
        .footer td {
            padding: 8px;
            border: 1px solid #333;
        }

        .footer td .header {
            font-weight: bold;
            background-color: #f0f0f0;
            text-align: center;
        }

        .footer td .body {
            width: 25%;
            vertical-align: top;
        }

        .cost-summary .label,
        .footer .label {
            font-weight: bold;
            background-color: #f0f0f0;
            width: 60%;
        }

        .cost-summary .value {
            text-align: right;
            width: 40%;
            text-transform: uppercase;
        }

        .cost-summary .total {
            font-weight: bold;
            font-size: 14px;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            background-color: #007bff;
            color: white;
            border-radius: 3px;
            margin-right: 5px;
            font-size: 11px;
        }

        .footer {
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .no-print {
            display: none;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <img src="{{ public_path('images/header_samarent.jpg') }}" alt="header samarent" width="100%">
    <div class="header">
        <h1>FORM TUGAS KELUAR KANTOR</h1>
    </div>

    <div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">No. Form</div>
                <div class="info-value">: {{ $formTugas->no_form }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nama Atasan</div>
                <div class="info-value">: {{ $formTugas->nama_atasan }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Staff yang bertugas</div>
                <div class="info-value">:
                    @if (is_array($formTugas->penerima_tugas))
                        @foreach ($formTugas->penerima_tugas as $penerima)
                            <span>{{ $penerima }},</span>
                        @endforeach
                    @else
                        {{ $formTugas->penerima_tugas }}
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Kendaraan</div>
                <div class="info-value">: {{ $formTugas->unit->jenis ?? '-' }}
                    {{ ' - ' . $formTugas->unit->nopol . ' ' . $formTugas->unit->merk . ' ' . $formTugas->unit->type }}
                </div>
            </div>
            @if ($formTugas->lainnya)
                <div class="info-row">
                    <div class="info-label">Lainnya</div>
                    <div class="info-value">: {{ $formTugas->lainnya }}</div>
                </div>
            @endif
            @if ($formTugas->sopir)
                <div class="info-row">
                    <div class="info-label">Sopir</div>
                    <div class="info-value">: {{ $formTugas->sopir }}</div>
                </div>
            @endif
            <div class="info-row">
                <div class="info-label">Waktu Tugas</div>
                <div class="info-value">:
                    @php
                        $diff = $formTugas->tanggal_mulai->diffInDays($formTugas->tanggal_selesai);
                        $hours = $formTugas->tanggal_mulai->diffInHours($formTugas->tanggal_selesai) % 24;
                        $result = '';
                        if ($diff > 0) {
                            $result .= $diff . ' hari';
                        }
                        if ($hours > 0) {
                            $result .= ($result ? ' ' : '') . $hours . ' jam';
                        }
                        if (empty($result)) {
                            $minutes = $formTugas->tanggal_mulai->diffInMinutes($formTugas->tanggal_selesai);
                            $result = $minutes . ' menit';
                        }
                    @endphp
                    {{ $formTugas->tanggal_mulai->format('d F Y') }} s/d
                    {{ $formTugas->tanggal_selesai->format('d F Y') }}. Selama {{ $result }}
                </div>
            </div>
        </div>

        <div class="section-title">Tempat Tujuan & Lokasi</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Nama Tempat & Tanggal</th>
                    <th style="width: 25%;">Lokasi</th>
                    <th style="width: 30%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($formTugas->tujuanTugas as $index => $tujuan)
                    <tr>
                        <td>
                            {{ $tujuan->tempat }} - {{ \Carbon\Carbon::parse($tujuan->tanggal)->format('d F Y') }}
                        </td>
                        <td>{{ $tujuan->location ?? '-' }}</td>
                        <td>{{ $tujuan->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #999;">Tidak ada tujuan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Biaya Anggaran</div>
        <div class="cost-summary">
            <table>
                <tr>
                    <td class="label">BBM</td>
                    <td class="value">Rp {{ number_format($formTugas->bbm ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Toll</td>
                    <td class="value">Rp {{ number_format($formTugas->toll ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Penginapan</td>
                    <td class="value">Rp {{ number_format($formTugas->penginapan ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Uang Dinas</td>
                    <td class="value">Rp {{ number_format($formTugas->uang_dinas ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Entertaint Customer</td>
                    <td class="value">Rp {{ number_format($formTugas->entertaint_customer ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label total">TOTAL BIAYA</td>
                    <td class="value total">Rp {{ number_format($formTugas->total ?? 0, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        @if ($formTugas->deskripsi)
            <div class="section-title">Keterangan Tambahan</div>
            <div style="padding: 10px; border: 1px solid #ccc; margin-bottom: 20px;">
                {{ $formTugas->deskripsi }}
            </div>
        @endif

        <div class="footer">
            <table class="data-table">
                <tr>
                    <td class="label header">Pemohon</td>
                    <td class="label header">Keputusan</td>
                    <td class="label header" colspan="2">Mengetahui</td>
                </tr>
                <tr>
                    <td class="body">
                        <br><br><br><br>
                    </td>
                    <td class="body">
                        <p>*) Setuju/Tidak</p>
                        <br>
                        <p>Jam :</p>
                        <p>Tgl :</p>
                        <p>Nama :</p>
                    </td>
                    <td class="body">
                        <br>
                        <br>
                        <p>Jam :</p>
                        <p>Tgl :</p>
                        <p>Nama :</p>
                    </td>
                    <td class="body">
                        <br>
                        <br>
                        <p>Jam :</p>
                        <p>Tgl :</p>
                        <p>Nama :</p>
                    </td>
                </tr>
                <tr>
                    <td class="label header">
                        <div style="text-transform: capitalize;">{{ $formTugas->pemohon }}</div>
                    </td>
                    <td class="label header">Spv/Head Dept./Div</td>
                    <td class="label header">General Affair</td>
                    <td class="label header">HRD</td>
                </tr>
            </table>
        </div>
</body>

</html>
