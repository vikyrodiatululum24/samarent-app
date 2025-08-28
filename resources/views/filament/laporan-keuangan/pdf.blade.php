<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan Service</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
        }

        th {
            background-color: #f0f0f0;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h2 class="text-center">Laporan Keuangan Service</h2>
    <h4 class="text-center">Periode: {{ $dari_tanggal }} s/d {{ $sampai_tanggal }}</h4>

    <table>
        <tr>
            <th>Tanggal</th>
            <th>No Pengajuan</th>
            <th>Rek. Penerima</th>
            <th>Rek. Bengkel</th>
            <th>No Polisi</th>
            <th>Keterangan</th>
            <th>Nominal TF Finance</th>
            <th>Nominal TF Bengkel</th>
            <th>Selisih</th>
        </tr>

        @foreach ($data as $item)
            <tr>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                <td>{{ $item->pengajuan->no_pengajuan }}</td>
                <td>{{ $rekPenerima($item) }}</td>
                <td>{{ $rekBengkel($item) }}</td>
                <td>{{ $item->pengajuan?->service_unit?->first()?->unit?->nopol ?? '-' }}</td>
                <td>{{ $item->pengajuan->keterangan }}</td>
                <td class="text-right">{{ number_format($nominalFinance($item), 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($nominalBengkel($item), 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($selisihTf($item), 0, ',', '.') }}</td>
            </tr>
        @endforeach

        <tr class="font-bold">
            <td colspan="6" class="text-left">TOTAL</td>
            <td class="text-right">{{ number_format($totalFinance, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($totalBengkel, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($totalSelisih, 0, ',', '.') }}</td>
        </tr>
    </table>
</body>

</html>
