<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
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
            background-color: #b7ffb7;
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
    <h4 class="text-center">Periode: {{ $dari_tanggal ?? '-' }} s/d {{ $sampai_tanggal ?? '-' }}</h4>
    <p class="text-center">Tanggal Cetak: {{ $tanggal }}</p>
    <hr>
    <table>
        <thead>
            <tr>
                <th>No</th>
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
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                    <td>{{ $item->pengajuan->no_pengajuan ?? '-' }}</td>
                    <td>
                        @php
                            $c = $item->pengajuan->complete ?? null;
                        @endphp
                        {{ $c ? $c->payment_2 . ' - ' . $c->norek_2 . ' - ' . $c->bank_2 : '-' }}
                    </td>
                    <td>
                        {{ $c ? $c->nama_rek_bengkel . ' - ' . $c->rek_bengkel . ' - ' . $c->bank_bengkel : '-' }}
                    </td>
                    <td>
                        @php
                            $nopols = $item->pengajuan?->service_unit
                                ?->map(function ($service_unit) {
                                    return $service_unit->unit?->nopol;
                                })
                                ->filter()
                                ->join(', ');
                        @endphp
                        {{ $nopols ?: '-' }}
                    </td>
                    <td>{{ $item->pengajuan->keterangan ?? '-' }}</td>
                    <td>Rp {{ number_format($c->nominal_tf_finance ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($c->nominal_tf_bengkel ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($c->selisih_tf ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7" style="text-align: left;">TOTAL</th>
                <th>Rp {{ number_format($totalFinance, 0, ',', '.') }}</th>
                <th>Rp {{ number_format($totalBengkel, 0, ',', '.') }}</th>
                <th>Rp {{ number_format($totalSelisih, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
