<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
    <title>Print Asuransi</title>
    <style>
        body {
            color: #000;
            font-size: 13px;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            box-sizing: border-box;
            display: block;
        }

        .page {
            width: 185mm;
            min-height: 270mm;
            margin: 0 auto;
        }

        h1 {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 18px;
            letter-spacing: 1px;
        }

        h2 {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            margin-top: 0;
            letter-spacing: 1px;
        }

        table {
            font-size: 13px;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        th,
        td {
            padding: 5px 8px;
            border: 1px solid #bbb;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f5f5f5;
            width: 38%;
            font-weight: 600;
        }

        .meta {
            margin-top: 18px;
            font-size: 11px;
            color: #666;
            text-align: right;
        }

        .img-full {
            width: 100%;
            max-height: 250mm;
            padding: 1mm;
            object-fit: contain;
            display: block;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .img-small {
            width: 80%;
            max-height: 100mm;
            padding: 1mm;
            object-fit: contain;
            display: block;
            margin: 0 auto;
            box-sizing: border-box;
            margin-left: auto;
            margin-right: auto;
        }

        .img-nota {
            max-width: 100%;
            max-height: 120mm;
            padding: 1mm;
            object-fit: contain;
            display: block;
            margin: 0 auto;
            box-sizing: border-box;
            margin-left: auto;
            margin-right: auto;
        }

        .unit-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(2, 1fr);
            gap: 8mm;
            width: 100%;
            box-sizing: border-box;
            height: 140mm;
            align-items: start;
            padding-top: 20mm;
        }

        .unit-img {
            width: 88mm;
            object-fit: contain;
            margin-bottom: 0.5mm;
            display: block;
            box-sizing: border-box;
            max-height: 119mm;
            /* Set a fixed height for uniformity */
        }

        .card {
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        .img-header {
            position: absolute;
            top: -7mm;
            left: -7mm;
            width: 200mm;
            height: 37mm;
            border-bottom: 1px solid #ccc;
            background: #fff;
        }
    </style>
</head>

<body>
    <!-- Halaman Data Utama -->
    <div class="page">
        <div class="img-header">
            <img src="{{ public_path('images/header_samarent.jpg') }}" alt="header samarent" width="100%">
        </div>
        <h1 style="padding-top: 35mm;">Laporan Asuransi</h1>
        <table>
            <tr>
                <th>No. Polisi</th>
                <td>{{ $asuransi->unit->nopol ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tipe Unit</th>
                <td>{{ $asuransi->unit->type ?? '-' }}</td>
            </tr>
            <tr>
                <th>Nama PIC</th>
                <td>{{ $asuransi->nama_pic }}</td>
            </tr>
            <tr>
                <th>Tanggal Pengajuan</th>
                <td>
                    {{ !empty($asuransi->tanggal_pengajuan) ? \Carbon\Carbon::parse($asuransi->tanggal_pengajuan)->format('d M Y') : '-' }}
                </td>
            </tr>
            <tr>
                <th>Unit Pelaksana</th>
                <td>{{ $asuransi->up ?? '-' }}</td>
            </tr>
            <tr>
                <th>Lokasi</th>
                <td>{{ $asuransi->lokasi ?? '-' }}</td>
            </tr>
            <tr>
                <th>Nama Asuransi</th>
                <td>{{ $asuransi->nama ?? '-' }}</td>
            </tr>
            <tr>
                <th>Jenis Asuransi</th>
                <td>{{ $asuransi->jenis ?? '-' }}</td>
            </tr>
            <tr>
                <th>Periode</th>
                <td>
                    {{ !empty($asuransi->periode_mulai) ? $asuransi->periode_mulai : '-' }}
                    -
                    {{ !empty($asuransi->periode_selesai) ? $asuransi->periode_selesai : '-' }}
                </td>
            </tr>
            <tr>
                <th>Nominal</th>
                <td>
                    Rp {{ isset($asuransi->nominal) ? number_format($asuransi->nominal, 0, ',', '.') : '-' }}
                </td>
            </tr>
            <tr>
                <th>Kategori</th>
                <td>{{ $asuransi->kategori ?? '-' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $asuransi->status ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tanggal Kejadian</th>
                <td>
                    {{ !empty($asuransi->tanggal_kejadian) ? \Carbon\Carbon::parse($asuransi->tanggal_kejadian)->format('d M Y') : '-' }}
                </td>
            </tr>
            <tr>
                <th>Keterangan Insiden</th>
                <td>{{ $asuransi->keterangan ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tujuan Pengajuan</th>
                <td>{{ $asuransi->tujuan_pengajuan ?? '-' }}</td>
            </tr>
        </table>
        <div class="meta">
            Dibuat: {{ $asuransi->created_at->format('d M Y H:i') }}
        </div>
    </div>

    <!-- Halaman Dokumen Pendukung (setiap gambar 1 halaman) -->
    @if (!empty($asuransi->foto_ktp) || !empty($asuransi->foto_sim))
        <div class="page">
            <div class="card">
                @if (!empty($asuransi->foto_ktp))
                    <h2>Foto KTP</h2>
                    <img src="{{ public_path('storage/' . $asuransi->foto_ktp) }}" alt="Foto KTP" class="img-small" />
                @endif
            </div>
            <div class="card">
                @if (!empty($asuransi->foto_sim))
                    <h2>Foto SIM</h2>
                    <img src="{{ public_path('storage/' . $asuransi->foto_sim) }}" alt="Foto SIM" class="img-small" />
                @endif
            </div>
        </div>
    @endif

    @if (!empty($asuransi->foto_stnk) || !empty($asuransi->foto_bpkb))
        <div class="page">
            <div class="card">
                @if (!empty($asuransi->foto_stnk))
                    <h2>Foto STNK</h2>
                    <img src="{{ public_path('storage/' . $asuransi->foto_stnk) }}" alt="Foto STNK"
                        class="img-small" />
                @endif
            </div>
            <div class="card">
                @if (!empty($asuransi->foto_bpkb))
                    <h2>Foto BPKB</h2>
                    <img src="{{ public_path('storage/' . $asuransi->foto_bpkb) }}" alt="Foto BPKB"
                        class="img-small" />
                @endif
            </div>
        </div>
    @endif

    @if (!empty($asuransi->foto_polis_asuransi))
        <div class="page">
            <h2>Foto Polis Asuransi</h2>
            <img src="{{ public_path('storage/' . $asuransi->foto_polis_asuransi) }}" alt="Foto Polis Asuransi"
                class="img-full" />
        </div>
    @endif

    @if (!empty($asuransi->foto_ba))
        <div class="page">
            <h2>Foto Berita Acara (BA)</h2>
            <img src="{{ public_path('storage/' . $asuransi->foto_ba) }}" alt="Foto BA" class="img-full" />
        </div>
    @endif

    @if (!empty($asuransi->foto_keterangan_bengkel))
        <div class="page">
            <h2>Foto Keterangan Bengkel</h2>
            <img src="{{ public_path('storage/' . $asuransi->foto_keterangan_bengkel) }}" alt="Foto Keterangan Bengkel"
                class="img-full" />
        </div>
    @endif

    @if (!empty($asuransi->foto_nota))
        <!-- Halaman Foto Unit (max 4 per halaman) -->
        @php
            $notaFotos = is_array($asuransi->foto_nota)
                ? $asuransi->foto_nota
                : json_decode($asuransi->foto_nota, true) ?? [];
            $notas = array_chunk($notaFotos, 4);
        @endphp
        @foreach ($notas as $nota)
            <div class="page">
                <h2>Foto Nota</h2>
                <div class="card">
                    @foreach ($nota as $img)
                        <img src="{{ public_path('storage/' . $img) }}" alt="Foto Nota" class="img-nota" />
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif

    @if (!empty($asuransi->foto_unit))
        <!-- Halaman Foto Unit (max 4 per halaman) -->
        @php
            $unitFotos = is_array($asuransi->foto_unit)
                ? $asuransi->foto_unit
                : json_decode($asuransi->foto_unit, true) ?? [];
            $chunks = array_chunk($unitFotos, 4);
        @endphp
        @foreach ($chunks as $chunk)
            <div class="page">
                <h2>Foto Unit</h2>
                <div class="unit-grid">
                    @foreach ($chunk as $img)
                        <img src="{{ public_path('storage/' . $img) }}" alt="Foto Unit" class="unit-img" />
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif

</body>

</html>
