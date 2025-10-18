<!DOCTYPE html>
<html>

<head>
    <title>Laporan Absensi Driver</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 2px;
            text-align: left;
            font-size: 11px;
        }

        .table th {
            background-color: #f4f4f4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .img-header {
            position: absolute;
            top: -9mm;
            left: -9mm;
            width: 204mm;
            height: 37mm;
            background: #fff;
        }

        .driver-info {
            margin-bottom: 20px;
            font-size: 11px;
            padding-top: 20mm;
        }

        /* Tambahkan style untuk watermark */
        .watermark {
            position: fixed;
            top: 50%;
            right: -45%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: -1;
            pointer-events: none;
        }

        .watermark img {
            width: 400px;
            /* Sesuaikan ukuran watermark */
            height: auto;
        }

        /* Pastikan konten tetap di atas watermark */
        .content {
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body>
    <div class="watermark">
        <img src="{{ public_path('images/icon.png') }}" alt="Watermark">
    </div>

    <div class="content">
        <div class="header">
            <img src="{{ public_path('images/header_samarent.jpg') }}" alt="header" class="img-header" />
        </div>

        <div class="driver-info">
            <table>
                <tr>
                    <td><strong>TIME SHEET</strong></td>
                    <td><strong>: JADWAL KERJA PENGEMUDI (WORKING SCHEDULE FOR DRIVER)</strong></td>
                </tr>
                <tr>
                    <td>
                        <strong>Nama Driver</strong>
                        <p style="font-style: italic; margin: 2px 0;">Driver Name</p>
                    </td>
                    <td style="vertical-align: top;">
                        <p style="margin: 0;">: {{ $driver->user->name ?? 'N/A' }}</p>
                    </td>
                </tr>
                <tr>
                    <td><strong>Bulan/Month</strong>
                    </td>
                    <td>: {{ $month ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Tanggal</th>
                    <th>No. Polisi</th>
                    <th>KM. Awal</th>
                    <th>KM. Akhir</th>
                    <th>Jam Masuk</th>
                    <th>Jam Cek</th>
                    <th>Jam Keluar</th>
                    <th>Pelanggan</th>
                    <th>End User</th>
                    <th>Approved</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendences as $attendance)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($attendance->date)->locale('id')->isoFormat('dddd') }}</td>
                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d-m-Y') }}</td>
                        <td>{{ $attendance->unit ? $attendance->unit->nopol : '-' }}</td>
                        <td>{{ $attendance->start_km ?? '-' }}</td>
                        <td>{{ $attendance->end_km ?? '-' }}</td>
                        <td>{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }}
                        </td>
                        <td>{{ $attendance->time_check ? \Carbon\Carbon::parse($attendance->time_check)->format('H:i') : '-' }}
                        </td>
                        <td>{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '-' }}
                        </td>
                        <td>{{ $attendance->project->name ?? '-' }}</td>
                        <td>{{ $attendance->endUser->name ?? '-' }}</td>
                        <td>{{ $attendance->is_complete ? 'Selesai' : 'Belum' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer" style="font-size: 10px;">
            <p>Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}</p>
        </div>

        <div style="margin-top: 30px;">
            <table style="width: 70%; margin: 0 auto;" class="table">
                <thead>
                    <tr>
                        <th style="text-align: center;">Dibuat Oleh</th>
                        <th style="text-align: center;">Disetujui Oleh</th>
                        <th style="text-align: center;">SAMARENT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="height: 70px; text-align: center; vertical-align: bottom;">
                            &nbsp;
                        </td>
                        <td style="height: 70px; text-align: center; vertical-align: bottom;">
                            &nbsp;
                        </td>
                        <td style="height: 70px; text-align: center; vertical-align: bottom;">
                            &nbsp;
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td style="">
                            Nama&nbsp;&nbsp;&nbsp;&nbsp;: <br>
                            Jabatan&nbsp;: <br>
                            Tanggal : <br>
                        </td>
                        <td style="">
                            Nama&nbsp;&nbsp;&nbsp;&nbsp;: <br>
                            Jabatan&nbsp;: <br>
                            Tanggal : <br>
                        </td>
                        <td style="">
                            Nama&nbsp;&nbsp;&nbsp;&nbsp;: <br>
                            Jabatan&nbsp;: <br>
                            Tanggal : <br>
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>

    {{-- halaman baru untuk dokumentasi photo absensi --}}
    <div style="page-break-before: always;">
        <h3 style="text-align: center; margin-top: 10px; font-size: 11px;">Dokumentasi Foto Absensi</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Foto Masuk</th>
                    <th>Foto Check</th>
                    <th>Foto Keluar</th>
                </tr>
            </thead>
            @foreach ($attendences as $attendance)
                @if ($attendance->photo_check || $attendance->photo_in || $attendance->photo_out)
                    <tbody>
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d-m-Y') }}</td>
                            <td class="text-center">
                                @if ($attendance->photo_in)
                                    <img src="{{ public_path($attendance->photo_in) }}"
                                        style="max-width: 100px; display: block; margin: 0 auto;"><br>
                                    <small>{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }}</small>
                                @else
                                    <p>Tidak ada foto</p>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($attendance->photo_check)
                                    <img src="{{ public_path($attendance->photo_check) }}"
                                        style="max-width: 100px; display: block; margin: 0 auto;"><br>
                                    <small>{{ $attendance->time_check ? \Carbon\Carbon::parse($attendance->time_check)->format('H:i') : '-' }}</small>
                                @else
                                    <p>Tidak ada foto</p>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($attendance->photo_out)
                                    <img src="{{ public_path($attendance->photo_out) }}"
                                        style="max-width: 100px; display: block; margin: 0 auto;"><br>
                                    <small>{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '-' }}</small>
                                @else
                                    <p>Tidak ada foto</p>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                @endif
            @endforeach
        </table>
    </div>

<script type="text/php">
if (isset($pdf)) {
    $pdf->page_script('
        $font = $fontMetrics->get_font("Helvetica", "normal");
        $size = 9;
        $text = "Halaman " . $PAGE_NUM . " dari " . $PAGE_COUNT;
        $width = $fontMetrics->get_text_width($text, $font, $size);
        $x = ($pdf->get_width() - $width) / 2;
        $y = $pdf->get_height() - 20;
        $pdf->text($x, $y, $text, $font, $size, [0, 0, 0]); // tambahkan argumen warna RGB
    ');
}
</script>

</script>

</body>

</html>
