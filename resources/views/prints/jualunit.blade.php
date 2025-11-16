<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image" href="{{ asset('images/icon.png') }}">
    <title>Laporan Jual Unit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            position: relative;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        .spesifikasi {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            text-transform: uppercase;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .unit-image {
            width: 300px;
            height: 200px;
            object-fit: cover;
            border: 1px solid #ddd;
            display: block;
            margin: 0 auto;
        }

        .images-section {
            page-break-before: always;
        }

        .image-row {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .image-container {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            text-align: center;
            padding: 10px;
        }

        .image-container:first-child {
            padding-right: 20px;
        }

        .image-container:last-child {
            padding-left: 20px;
        }

        .image-label {
            font-weight: bold;
            margin-top: 10px;
            font-size: 12px;
        }

        @media print {
            .images-section {
                page-break-before: always;
            }

            .image-row {
                break-inside: avoid;
                margin-bottom: 40px;
            }
        }

        .signature {
            margin-top: 40px;
            text-align: right;
        }

        .section-title {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
        }

        .photo-page-title {
            text-align: center;
            margin-bottom: 30px;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <img src="{{ public_path('images/header_samarent.jpg') }}" alt="header image" style="width: 200mm; position: relative; top: -50px; left: -50px;">

    <div class="header-section">
        Laporan Penjualan Unit<br>
        {{ $jualunit->unit->merk }} {{ $jualunit->unit->type }} - {{ $jualunit->unit->nopol }}
    </div>

    <!-- Data Unit yang Dijual -->
    <table>
        <tr class="section-title">
            <td colspan="2">INFORMASI PENJUALAN UNIT</td>
        </tr>
        <tr>
            <th style="width: 30%;">Open Price</th>
            <td>{{ 'Rp ' . number_format($jualunit->harga_jual, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Harga Target</th>
            <td>{{ 'Rp ' . number_format($jualunit->harga_netto, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Keterangan</th>
            <td>{{ $jualunit->keterangan ?: '-' }}</td>
        </tr>
    </table>

    <!-- Data Unit -->
    <table>
        <tr class="section-title">
            <td colspan="2">SPESIFIKASI UNIT</td>
        </tr>
        <tr>
            <th style="width: 30%;">No. RKS</th>
            <td class="spesifikasi">{{ $jualunit->unit->no_rks }}</td>
        </tr>
        <tr>
            <th>Penyerahan Unit</th>
            <td class="spesifikasi">
                {{ $jualunit->unit->penyerahan_unit ? \Carbon\Carbon::parse($jualunit->unit->penyerahan_unit)->format('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <th>Jenis</th>
            <td class="spesifikasi">{{ $jualunit->unit->jenis }}</td>
        </tr>
        <tr>
            <th>Merk</th>
            <td class="spesifikasi">{{ $jualunit->unit->merk }}</td>
        </tr>
        <tr>
            <th>Type</th>
            <td class="spesifikasi">{{ $jualunit->unit->type }}</td>
        </tr>
        <tr>
            <th>No. Polisi</th>
            <td class="spesifikasi">{{ $jualunit->unit->nopol }}</td>
        </tr>
        <tr>
            <th>No. Rangka</th>
            <td class="spesifikasi">{{ $jualunit->unit->no_rangka }}</td>
        </tr>
        <tr>
            <th>No. Mesin</th>
            <td class="spesifikasi">{{ $jualunit->unit->no_mesin }}</td>
        </tr>
        <tr>
            <th>Tanggal Pajak</th>
            <td class="spesifikasi">
                {{ $jualunit->unit->tgl_pajak ? \Carbon\Carbon::parse($jualunit->unit->tgl_pajak)->format('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <th>Regional</th>
            <td class="spesifikasi">{{ $jualunit->unit->regional }}</td>
        </tr>
        <tr>
            <th>Warna</th>
            <td class="spesifikasi">{{ $jualunit->unit->warna }}</td>
        </tr>
        <tr>
            <th>Tahun</th>
            <td class="spesifikasi">{{ $jualunit->unit->tahun }}</td>
        </tr>
        <tr>
            <th>BPKB</th>
            <td class="spesifikasi">{{ $jualunit->unit->bpkb }}</td>
        </tr>
    </table>

    <!-- Data Penawar -->
    @if ($jualunit->penawars && $jualunit->penawars->count() > 0)
        <table>
            <tr class="section-title">
                <td colspan="6">DAFTAR PENAWAR</td>
            </tr>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Nama Penawar</th>
                <th style="width: 15%;">No. WhatsApp</th>
                <th style="width: 20%;">Harga Penawaran</th>
                <th style="width: 15%;">Down Payment</th>
                <th style="width: 25%;">Catatan</th>
            </tr>
            @foreach ($jualunit->penawars as $index => $penawar)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $penawar->nama }}</td>
                    <td>{{ $penawar->no_wa }}</td>
                    <td>{{ 'Rp ' . number_format($penawar->harga_penawaran, 0, ',', '.') }}</td>
                    <td>{{ 'Rp ' . number_format($penawar->down_payment, 0, ',', '.') }}</td>
                    <td>{{ $penawar->catatan ?: '-' }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <table>
            <tr class="section-title">
                <td>DAFTAR PENAWAR</td>
            </tr>
            <tr>
                <td style="text-align: center; font-style: italic; padding: 20px;">Belum ada penawar untuk unit ini</td>
            </tr>
        </table>
    @endif

    <div class="signature">
        <table style="border: none; width: 100%;">
            <tr>
                <td style="border: none; text-align: right; padding-right: 50px; width: 70%;">
                    &nbsp;
                </td>
                <td style="border: none; text-align: center; width: 30%;">
                    Jakarta, {{ \Carbon\Carbon::now()->format('d F Y') }}<br><br><br><br>
                    ___________________________<br>
                    (Penanggung Jawab Penjualan)
                </td>
            </tr>
        </table>
    </div>

    <!-- Halaman Khusus untuk Gambar Unit -->
    @if (
        $jualunit->foto_depan ||
            $jualunit->foto_belakang ||
            $jualunit->foto_kiri ||
            $jualunit->foto_kanan ||
            $jualunit->foto_interior ||
            $jualunit->foto_odometer)

        <div class="images-section">
            <div class="photo-page-title">
                DOKUMENTASI FOTO UNIT<br>
                {{ $jualunit->unit->merk }} {{ $jualunit->unit->type }} - {{ $jualunit->unit->nopol }}
            </div>

            @php
                $photos = [];
                if ($jualunit->foto_depan) {
                    $photos[] = ['path' => $jualunit->foto_depan, 'label' => 'Foto Depan'];
                }
                if ($jualunit->foto_belakang) {
                    $photos[] = ['path' => $jualunit->foto_belakang, 'label' => 'Foto Belakang'];
                }
                if ($jualunit->foto_kiri) {
                    $photos[] = ['path' => $jualunit->foto_kiri, 'label' => 'Foto Kiri'];
                }
                if ($jualunit->foto_kanan) {
                    $photos[] = ['path' => $jualunit->foto_kanan, 'label' => 'Foto Kanan'];
                }
                if ($jualunit->foto_interior) {
                    $photos[] = ['path' => $jualunit->foto_interior, 'label' => 'Foto Interior'];
                }
                if ($jualunit->foto_odometer) {
                    $photos[] = ['path' => $jualunit->foto_odometer, 'label' => 'Foto Odometer'];
                }

                $photoChunks = array_chunk($photos, 2);
            @endphp

            @foreach ($photoChunks as $photoRow)
                <div class="image-row">
                    @foreach ($photoRow as $photo)
                        <div class="image-container">
                            <img src="{{ public_path('storage/' . $photo['path']) }}" class="unit-image"
                                alt="{{ $photo['label'] }}">
                            <div class="image-label">{{ $photo['label'] }}</div>
                        </div>
                    @endforeach

                    @if (count($photoRow) == 1)
                        <div class="image-container">
                            <!-- Empty container for single image alignment -->
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</body>

</html>
