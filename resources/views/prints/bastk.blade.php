<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print BASTK</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        .watermark {
            position: fixed;
            top: 25%;
            right: -5%;
            z-index: -1000;
            opacity: .08;
        }

        .watermark img {
            width: 420px;
        }

        .noBastk {
            position: fixed;
            top: 100px;
            right: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #000;
            padding: 5px 10px;
            z-index: 1000;
        }

        .type-bastk {
            position: fixed;
            top: 100px;
            right: 50%;
            transform: translateX(50%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1px 5px;
            z-index: 1000;
        }
    </style>
</head>

<body>

    <div class="watermark">
        <img src="{{ public_path('images/icon.png') }}">
    </div>

    <!-- seluruh isi pdf -->

    <div>
        <div class="noBastk">
            <p style="text-align: center; font-size: 15px; font-weight: bold; margin-bottom: 5px;">{{ $bastk->no_bastk }}</p>
        </div>
        <div class="type-bastk">
            <p style="text-align: center; font-size: 15px; font-weight: bold; margin-bottom: 5px; text-decoration: underline;">{{ $bastk->type_bastk == 'serah' ? 'PENYERAHAN' : 'PENGAMBILAN' }}</p>
        </div>
        <img src="{{ public_path('images/header_samarent.jpg') }}" alt="header samarent" width="100%"
            style="margin-bottom: 5px;">
        <div style="padding: 5px 20px;">
            <table class="header" style="width: 90%;">
                <tr>
                    <td style="font-weight: bold; width: 15%; padding:0.2rem;">
                        Kepada
                    </td>
                    <td style="width: 1%; padding:0.2rem;">
                        :
                    </td>
                    <td style="width: 35%; padding:0.2rem;">
                        {{ strtoupper($bastk->kepada) }}
                    </td>
                    <td style="font-weight: bold; width: 20%; padding:0.2rem;">
                        No. Polisi / No. PO
                    </td>
                    <td style="width: 1%; padding:0.2rem;">
                        :
                    </td>
                    <td style="width: 40%; padding:0.2rem;">
                        {{ $bastk->unit->nopol ?? ' - ' }} / {{ $bastk->unit->no_rks }}
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; width: 15%; padding:0.2rem;">
                        Alamat
                    </td>
                    <td style="width: 1%; padding:0.2rem;">
                        :
                    </td>
                    <td style="width: 35%;" rowspan="2" valign="top" style="vertical-align: top;">
                        {{ strtoupper($bastk->alamat) }}
                    </td>
                    <td style="font-weight: bold; width: 20%; padding:0.2rem;">
                        Type Kendaraan
                    </td>
                    <td style="width: 1%; padding:0.2rem;">
                        :
                    </td>
                    <td style="width: 40%; padding:0.2rem;">
                        {{ strtoupper($bastk->unit->type) }}
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; width: 15%;">
                        &nbsp;
                    </td>
                    <td style="width: 1%;">
                        &nbsp;
                    </td>
                    <td style="font-weight: bold; width: 20%;">
                        Warna / Tahun
                    </td>
                    <td style="width: 1%;">
                        :
                    </td>
                    <td style="width: 40%;">
                        {{ strtoupper($bastk->unit->warna ?? ' - ') }} / {{ strtoupper($bastk->unit->no_rangka) }}
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; width: 15%;">
                        No. Telepon / HP
                    </td>
                    <td style="width: 1%;">
                        :
                    </td>
                    <td style="width: 35%;">
                        {{ $bastk->no_hp }}
                    </td>
                    <td style="font-weight: bold; width: 20%;">
                        No. Mesin / Rangka
                    </td>
                    <td style="width: 1%;">
                        :
                    </td>
                    <td style="width: 40%;">
                        {{ strtoupper($bastk->unit->no_mesin) }} / {{ strtoupper($bastk->unit->no_rangka) }}
                    </td>
                </tr>
                @php
                    $kondisi = $bastk->kondisi_unit ?? [];

                    $pilihan = [
                        'New Lease',
                        'New Car',
                        'Used Car',
                        'End Contract',
                        'Replace',
                        'Temporary',
                        'Body Repair',
                        'Service',
                        'Permer',
                    ];
                @endphp
                <tr>
                    <td>
                        &nbsp;
                    </td>
                    <td style="width: 1%;">
                        :
                    </td>
                    <td style="width: 75%;" colspan="4">
                        <table width="100%">
                            @foreach (collect($pilihan)->chunk(9) as $row)
                                <tr>
                                    <td>
                                        @foreach ($pilihan as $item)
                                        <span style="{{ in_array(strtolower($item), $kondisi) ? 'font-weight: bold;' : 'font-weight: normal;' }}">
                                            {!! in_array(strtolower($item), $kondisi) ? '&#10004;' : '' !!}
                                            {{ $item }}
                                        </span>
                                            @unless ($loop->last)
                                                /
                                            @endunless
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; width: 15%;">
                        &nbsp;
                    </td>
                    <td style="width: 1%;">
                        :
                    </td>
                    <td style="width: 35%;">
                        Exchange {{ $bastk->exchange }}
                    </td>
                </tr>

            </table>
        </div>
        <div style="width: 100%; padding: 0px 20px;">
            <p style="margin-bottom: 2px;">Kendaraan di atas di serahkan . di terimakan dengan kondisi ssb:</p>
            <table style="width: 95%; border: 1px solid; border-collapse: collapse;">
                <tr>
                    <td
                        style="width: 3%; border: 1px solid; border-collapse: collapse; text-align: center; font-weight: bold;">
                        No.
                    </td>
                    <td style="font-weight: bold; width: 35%; border: 1px solid; border-collapse: collapse;">
                        Kelengkapan
                    </td>
                    <td
                        style="font-weight: bold; width: 4%; border: 1px solid; border-collapse: collapse; text-align: center;">
                        B
                    </td>
                    <td
                        style="font-weight: bold; width: 4%; border: 1px solid; border-collapse: collapse; text-align: center">
                        R
                    </td>
                    <td
                        style="font-weight: bold; width: 4%; border: 1px solid; border-collapse: collapse; text-align:center">
                        T
                    </td>
                    <td style="font-weight: bold; width: 50%; border: 1px solid; border-collapse: collapse;">
                        Keterangan
                    </td>
                </tr>
                @php
                    $exclude = ['Velg Ban', 'Tutup Dop', 'Apar', 'BBM & KM'];

                    $items = $bastk->items->reject(function ($item) use ($exclude) {
                        return in_array($item->kelengkapan, $exclude);
                    });
                @endphp
                @foreach ($items as $item)
                    <tr>
                        <td style="width: 3%; border: 1px solid; border-collapse: collapse; text-align: center;">
                            {{ $loop->iteration }}
                        </td>
                        <td style="width: 35%; border: 1px solid; border-collapse: collapse;">
                            {{ $item->kelengkapan }}
                        </td>
                        <td style="width: 4%; border: 1px solid; border-collapse: collapse; text-align: center;">
                            {!! $item->baik ? '&#10004;' : '' !!}
                        </td>
                        <td style="width: 4%; border: 1px solid; border-collapse: collapse; text-align: center;">
                            {!! $item->rusak ? '&#10004;' : '' !!}
                        </td>
                        <td style="width: 4%; border: 1px solid; border-collapse: collapse; text-align: center;">
                            {!! $item->tidak_ada ? '&#10004;' : '' !!}
                        </td>
                        <td>
                            @if ($loop->first)
                                {{ $bastk->keterangan }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            @php
                $velg = $bastk->items->firstWhere('kelengkapan', 'Velg Ban');
                $tutup_dop = $bastk->items->firstWhere('kelengkapan', 'Tutup Dop');
                $apar = $bastk->items->firstWhere('kelengkapan', 'Apar');
            @endphp
            <table width="95%" style="padding-left: 20px; border-collapse:collapse; margin-top:10px;">
                <tr>
                    <td width="10%" style="font-weight: bold;">Velg Ban</td>
                    <td width="20%">
                        : <span style="{{ $velg?->baik ? 'font-weight: bold;' : 'font-weight: normal;' }}">Orignal</span> / <span style="{{ !$velg?->baik ? 'font-weight: bold;' : 'font-weight: normal;' }}">Racing</span>
                    </td>
                    <td width="50%" rowspan="9" style="vertical-align: top; text-align:center;">
                        <img src="{{ public_path('images/car.jpeg') }}" alt="car" width="100%"
                            style="object-fit: cover; height:200px; width:250px;">
                    </td>
                    <td rowspan="3" style="vertical-align: top; text-align:center;">
                        @php
                            $segments = 8;
                            $fuel = $bastk->items->firstWhere('kelengkapan', 'BBM & KM')?->bbm;
                            $jenisBbm = $bastk->items->firstWhere('kelengkapan', 'BBM & KM')?->jenis_bbm;
                        @endphp

                        <table style="border-collapse: separate; border-spacing: 3px 0px;">
                            <tr>
                                <td width="15%" colspan="8" style="text-align: center">Fuel :
                                    {{ $fuel }}/8, {{ $jenisBbm }}</td>
                            </tr>
                            <tr>
                                <td style="padding-right:5px;">E</td>

                                @for ($i = 1; $i <= 4; $i++)
                                    <td>
                                        <div
                                            style="
                width:14px;
                height:10px;
                border:1px solid #000;
                background:{{ $i <= $fuel ? '#000' : '#fff' }};
            ">
                                        </div>
                                    </td>
                                @endfor

                                {{-- Garis pembatas --}}
                                <td style="padding:0 4px; border:none;">
                                    <div style="width:1px; height:16px; background:#000;"></div>
                                </td>

                                @for ($i = 5; $i <= 8; $i++)
                                    <td>
                                        <div
                                            style="
                width:14px;
                height:10px;
                border:1px solid #000;
                background:{{ $i <= $fuel ? '#000' : '#fff' }};
            ">
                                        </div>
                                    </td>
                                @endfor

                                <td style="padding-left:5px;">F</td>
                            </tr>
                            <td width="15%" colspan="8" style="text-align: center">KM :
                                {{ $bastk->items->firstWhere('kelengkapan', 'BBM & KM')?->km ?? '-' }}</td>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="10%" style="font-weight: bold;">Tutup Dop</td>
                    <td width="20%" >
                        : <span style="{{ $tutup_dop?->baik ? 'font-weight: bold;' : 'font-weight: normal;' }}">Ada</span> / <span style="{{ !$tutup_dop?->baik ? 'font-weight: bold;' : 'font-weight: normal;' }}">Tidak Ada</span>
                    </td>

                </tr>
                <tr>
                    <td width="10%" style="font-weight: bold;">Apar</td>
                    <td width="20%">
                        : <span style="{{ $apar?->baik ? 'font-weight: bold;' : 'font-weight: normal;' }}">Ada</span> / <span style="{{ !$apar?->baik ? 'font-weight: bold;' : 'font-weight: normal;' }}">Tidak Ada</span>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center; margin-top: 20px;">Jakarta,
                        {{ \Carbon\Carbon::parse($bastk->tgl_serah)->translatedFormat('d F Y') }}</td>
                    <td colspan="2" style="text-align: center; margin-top: 20px;">Jam ditema : .................
                    </td>
                </tr>
                {{-- tanda tangan penyerah --}}
                <tr>
                    <td colspan="2" style="text-align: center;">Yang Menyerahkan</td>
                    <td colspan="2" style="text-align: center;">Yang Menerima</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center height: 100px;">&nbsp;</td>
                    <td colspan="2" style="text-align: center height: 100px;">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center; padding-top: 50px;">
                        <u>({{ $bastk->nama_penyerah }})</u>
                    </td>
                    <td colspan="2" style="text-align: center; padding-top: 50px;">
                        <u>({{ $bastk->nama_penerima }})</u>
                    </td>
                </tr>
                {{-- <tr>
                    <td colspan="2" style="text-align: center; text-size: 6px;">Nama Leng, Tanda Tangan & Stempel</td>
                    <td colspan="2" style="text-align: center; text-size: 6px;">Nama, Tanda Tangan & Stempel</td>
                </tr> --}}
            </table>
        </div>
    </div>

    <div style="page-break-before: always; padding: 10px 20px;">
        @php

            $dokumentasi = collect();

            $doc = $bastk->dokumentasi;

            // =========================
            // Dokumentasi utama
            // =========================

            $data = [
                'Unit Depan' => $doc->unit_depan,
                'Unit Belakang' => $doc->unit_belakang,
                'Unit Samping Kanan' => $doc->unit_samping_kanan,
                'Unit Samping Kiri' => $doc->unit_samping_kiri,

                'Kabin Depan' => $doc->kabin_depan,
                'Kabin Tengah' => $doc->kabin_tengah,
                'Kabin Belakang' => $doc->kabin_belakang,

                'Dashboard' => $doc->dashboard,
                'Odometer' => $doc->odometer,

                'Buku Service' => $doc->buku_service,
                'Manual Book' => $doc->manual_book,
                'Ban Serep' => $doc->ban_serep,

                'STNK Depan' => $doc->stnk_depan,
                'STNK Belakang' => $doc->stnk_belakang,

                'BASTK' => $doc->bastk,
            ];

            foreach ($data as $label => $image) {
                if (!empty($image)) {
                    $dokumentasi->push([
                        'label' => $label,
                        'image' => $image,
                    ]);
                }
            }

            // =========================
            // Kerusakan
            // =========================

            foreach ($doc->kerusakan ?? [] as $index => $image) {
                if (!empty($image)) {
                    $dokumentasi->push([
                        'label' => 'Kerusakan ' . ($index + 1),
                        'image' => $image,
                    ]);
                }
            }

            // =========================
            // Tools
            // =========================

            foreach ($doc->tools ?? [] as $index => $image) {
                if (!empty($image)) {
                    $dokumentasi->push([
                        'label' => 'Tools ' . ($index + 1),
                        'image' => $image,
                    ]);
                }
            }

        @endphp
        @foreach ($dokumentasi->chunk(4) as $row)
            <table width="100%" style="border-collapse: collapse; margin-bottom:12px;">

                <tr>

                    @foreach ($row as $item)
                        <td width="25%"
                            style="
                    border:1px solid #000;
                    padding:5px;
                    vertical-align:top;
                    text-align:center;
                ">

                            <div style="font-size:10px;font-weight:bold;margin-bottom:4px;">
                                {{ $item['label'] }}
                            </div>

                            @php
                                $path = public_path('storage/' . $item['image']);
                            @endphp

                            @if (file_exists($path))
                                <img src="{{ $path }}"
                                    style="
                            width:100%;
                            object-fit:cover;
                        ">
                            @else
                                <div
                                    style="
                            height:140px;
                            line-height:140px;
                            border:1px dashed #999;
                            color:#999;
                            font-size:9px;
                        ">

                                    Tidak Ada Gambar

                                </div>
                            @endif

                        </td>
                    @endforeach

                    {{-- Jika kurang dari 4 kolom --}}
                    @for ($i = $row->count(); $i < 4; $i++)
                        <td width="25%" style="
                    border:1px solid #000;
                ">
                        </td>
                    @endfor

                </tr>

            </table>
        @endforeach

    </div>
</body>

</html>
