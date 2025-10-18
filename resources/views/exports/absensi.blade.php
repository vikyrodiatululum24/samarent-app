<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .no-border td {
            border: none;
            padding: 3px;
        }

        .title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td colspan="2"><strong>TIME SHEET</strong></td>
            <td colspan="9"><strong>: JADWAL KERJA PENGEMUDI (WORKING SCHEDULE FOR DRIVER)</strong></td>
        </tr>
        <tr>
            <td colspan="2">
                <strong>Nama Driver</strong>
                <p style="font-style: italic; margin: 2px 0;">Driver Name</p>
            </td>
            <td colspan="9">
                <p style="margin: 0;">: {{ $driverName ?? 'N/A' }}</p>
            </td>
        </tr>
        <tr>
            <td colspan="2"><strong>Bulan/Month</strong>
            </td>
            <td colspan="9">: {{ $month ? \Carbon\Carbon::create()->month($month)->locale('id')->translatedFormat('F') : 'N/A' }}</td>
        </tr>
    </table>

    <table >
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
                    <td>{{ $attendance->unit?->nopol ?? '-' }}</td>
                    <td>{{ $attendance->start_km ?? '-' }}</td>
                    <td>{{ $attendance->end_km ?? '-' }}</td>
                    <td>{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }}
                    </td>
                    <td>{{ $attendance->time_check ? \Carbon\Carbon::parse($attendance->time_check)->format('H:i') : '-' }}
                    </td>
                    <td>{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '-' }}
                    </td>
                    <td>{{ $attendance->project?->name ?? '-' }}</td>
                    <td>{{ $attendance->endUser?->name ?? '-' }}</td>
                    <td>{{ $attendance->is_complete ? 'Selesai' : 'Belum' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
