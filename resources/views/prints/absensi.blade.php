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
            padding: 8px;
            text-align: left;
            font-size: 10px;
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
            top: -7mm;
            left: -7mm;
            width: 200mm;
            height: 37mm;
            background: #fff;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/header_samarent.jpg') }}" alt="header" class="img-header" />
        <h2 style="padding-top: 27mm;">Laporan Absensi Driver</h2>
    </div>

    <div class="driver-info">
        <table>
            <tr>
                <td><strong>Nama Driver:</strong></td>
                <td>{{ $driver->user->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Bulan/Month:</strong></td>
                <td>{{ $month ?? 'N/A' }}</td>
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

    <div class="footer" style="margin-top: 30px;">
        <p>Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>

    {{-- halaman baru untuk dokumentasi photo absensi --}}
    <div style="page-break-before: always;">
        <h3 style="text-align: center; margin-top: 10px;">Dokumentasi Foto Absensi</h3>
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
</body>

</html>
