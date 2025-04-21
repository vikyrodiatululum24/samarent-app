<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print SPK</title>
</head>

<body>
    {{-- spk --}}
    <div>
        <img src="{{ public_path('images/header_samarent.jpg') }}" alt="header samarent" width="100%">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr style="height : 20mm;">
                <td style="width: 20%; text-align: center; border: 2px solid black;">
                    <p style="margin: 2px 0;">KM AWAL</p>
                    <h2 style="margin: 2px 0; font-weight: bold;">&nbsp;</h2>
                </td>
                <th>
                    <h1>Surat Perintah Kerja</h1>
                </th>
                <td style="width: 20%; text-align: center; border: 2px solid black;">
                    <p style="margin: 2px 0;">KODE</p>
                    <h1 style="margin: 2px 0; text-transform: uppercase; line-height: 1.2;">
                        {{ $pengajuan->complete->kode }}</h1>
                </td>
            </tr>
        </table>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr style="vertical-align: top;">
                <th style="width: 10%; text-align: left;">
                    <p style="margin: 2px 0;">Bengkel</p>
                    <p style="margin: 2px 0;">Telp</p>
                    <p style="margin: 2px 0;">Estimasi</p>
                    <p style="margin: 2px 0;">No Rek</p>
                </th>
                <td style="width: 30%;">
                    <p style="margin: 2px 0;">: {{ $pengajuan->complete->bengkel_estimasi }}</p>
                    <p style="margin: 2px 0;">: {{ $pengajuan->complete->no_telp_bengkel }}</p>
                    <p style="margin: 2px 0;">: {{ $pengajuan->complete->nominal_estimasi }}</p>
                    <p style="margin: 2px 0;">: </p>
                </td>
                <th style="width: 20%; text-align: left;">
                    <p style="margin: 2px 0;">No WO</p>
                    <p style="margin: 2px 0;">Jenis Pengajuan</p>
                    <p style="margin: 2px 0;">Tanggal WO</p>
                    <p style="margin: 2px 0;">User</p>
                    <p style="margin: 2px 0;">Telp</p>
                </th>
                <td style="width: 30%;">
                    <p style="margin: 2px 0;">: {{ $pengajuan->no_pengajuan }}</p>
                    <p style="margin: 2px 0;">: {{ $pengajuan->keterangan }}</p>
                    <p style="margin: 2px 0;">: {{ $pengajuan->created_at->format('d F Y') }}</p>
                    <p style="margin: 2px 0;">: {{ $pengajuan->up }} - {{ $pengajuan->provinsi }}</p>
                    <p style="margin: 2px 0;">: {{ $pengajuan->no_wa }}</p>
                </td>
            </tr>
        </table>
        <table class="table" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; padding: 0.5rem">No Polisi</th>
                    <th style="border: 1px solid black; padding: 0.5rem">KM</th>
                    <th style="border: 1px solid black; padding: 0.5rem">Type Unit</th>
                    <th style="border: 1px solid black; padding: 0.5rem">Permintaan / Part</th>
                    <th style="border: 1px solid black; padding: 0.5rem">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid black; padding: 0.5rem;">{{ $pengajuan->nopol }}</td>
                    <td style="border: 1px solid black; padding: 0.5rem;">{{ $pengajuan->odometer }}</td>
                    <td style="border: 1px solid black; padding: 0.5rem;">{{ $pengajuan->type }}</td>
                    <td style="border: 1px solid black; padding: 0.5rem;">{{ $pengajuan->service }}</td>
                    <td style="border: 1px solid black; padding: 0.5rem; text-transform:uppercase;">
                        {{ $pengajuan->complete->kode }}</td>
                </tr>
            </tbody>
        </table>
        <p>Perhatian :</p>
        <ol>
            <li>Bila ada pekerjaan atau pergantian spare part di luar SPK, mohon konfirmasi ke PIC</li>
            <li>Tagihan dapat dikirimkan segera dan maksimal 14 hari kerja ke PT Samana Jaya Propertindo</li>
        </ol>
        <div>
            <p style="margin: 2px 0;">Jakarta, {{ now()->format('d F Y') }}</p>
            <p style="margin: 2px 0;">Hormat Kami</p>
            <p style="margin: 2px 0; font-weight: bold;">PT. Samana Jaya Propertindo</p>
        </div>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <th style="border: 1px solid black; padding : 0.5rem; width: 24%;">Dibuat</th>
                <th style="border: 1px solid black; padding : 0.5rem; width: 24%;">Disetujui</th>
                <th style="border: 1px solid black; padding : 0.5rem; width: 24%;">Diperiksa</th>
                <th style="border: 1px solid black; padding : 0.5rem; width: 24%;">Diketahui</th>
            </tr>
            <tr>
                <td style="height: 113px; border: 1px solid black;">&nbsp;</td>
                <td style="height: 113px; border: 1px solid black;">&nbsp;</td>
                <td style="height: 113px; border: 1px solid black;">&nbsp;</td>
                <td style="height: 113px; border: 1px solid black;">&nbsp;</td>
            </tr>
            <tr>
                <td style="height: 38px; border: 1px solid black;">&nbsp;</td>
                <td style="height: 38px; border: 1px solid black;">&nbsp;</td>
                <td style="height: 38px; border: 1px solid black;">&nbsp;</td>
                <td style="height: 38px; border: 1px solid black;">&nbsp;</td>
            </tr>
        </table>
    </div>


    {{-- Lampiran --}}
    <div style="page-break-before: always;">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; margin-top: 10mm;">
            <tr>
                <th style="width: 80%; text-align: center; padding: 10px;">
                    <h1 style="margin: 2px;">LAMPIRAN</h1>
                </th>
                <th style="width: 20%; text-align: center; border: 2px solid black;">
                    <h1 style="text-transform: uppercase; margin: 2px;">{{ $pengajuan->complete->kode }}</h1>
                </th>
            </tr>
        </table>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr style="vertical-align: top;">
                <th style="width: 10%; padding: 10px; text-align: left;">
                    <p style="margin: 2px;">Bengkel</p>
                    <p style="margin: 2px;">Cabang</p>
                    <p style="margin: 2px;">Telp</p>
                    <p style="margin: 2px;">Estimasi</p>
                    <p style="margin: 2px;">No Rek</p>
                </th>
                <td style="width: 30%; padding: 10px;">
                    <p style="margin: 2px;">: {{ $pengajuan->complete->bengkel_estimasi }}</p>
                    <p style="margin: 2px;">: {{ $pengajuan->complete->cabang }}</p>
                    <p style="margin: 2px;">: {{ $pengajuan->complete->no_telp_bengkel }}</p>
                    <p style="margin: 2px;">: {{ $pengajuan->complete->nominal_estimasi }}</p>
                    <p style="margin: 2px;">: {{ $pengajuan->complete->no_rek_bengkel }}</p>
                </td>
                <th style="width: 20%; padding: 10px; text-align: left;">
                    <p style="margin: 2px;">No WO</p>
                    <p style="margin: 2px;">Jenis Pengajuan</p>
                    <p style="margin: 2px;">Tanggal WO</p>
                    <p style="margin: 2px;">User</p>
                    <p style="margin: 2px;">Telp</p>
                </th>
                <td style="width: 30%; padding: 10px;">
                    <p style="margin: 2px;">: {{ $pengajuan->no_pengajuan }}</p>
                    <p style="margin: 2px;">: {{ $pengajuan->keterangan }}</p>
                    <p style="margin: 2px;">: {{ $pengajuan->created_at->format('d F Y') }}</p>
                    <p style="margin: 2px;">: {{ $pengajuan->up }} - {{ $pengajuan->provinsi }}</p>
                    <p style="margin: 2px;">: {{ $pengajuan->no_wa }}</p>
                </td>
            </tr>
        </table>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <th style="width: 50%; padding: 10px; text-align: center; border: 1px solid black;">
                    <p style="margin: 2px;">FOTO UNIT TERLIHAT NOPOL</p>
                </th>
                <th style="width: 50%; padding: 10px; text-align: center; border: 1px solid black">
                    <p style="margin: 2px;">FOTO KM / ODOMETER</p>
                </th>
            </tr>
            <tr>
                <td
                    style="width: 50%; padding: 10px; text-align: center; height: 300px; border: 1px solid black; vertical-align:top; align-items: left;">
                    @if ($pengajuan->foto_unit)
                        <img src="{{ public_path('storage/' . $pengajuan->foto_unit) }}" alt="Foto Unit"
                            width="300">
                    @else
                        <p style="margin: 2px;">tidak ada gambar</p>
                    @endif
                </td>
                <td
                    style="width: 50%; padding: 10px; text-align: center; height: 300px; border: 1px solid black; vertical-align: top;">
                    @if ($pengajuan->foto_odometer)
                        <img src="{{ public_path('storage/' . $pengajuan->foto_odometer) }}" alt="Foto Odometer"
                            width="300">
                    @else
                        <p style="margin: 2px;">tidak ada gambar</p>
                    @endif
                </td>
            </tr>
            <tr>
                <th style="width: 50%; padding: 10px; text-align: center; border: 1px solid black;">
                    <p style="margin: 2px;">FOTO KONDISI PART</p>
                </th>
                <th style="width: 50%; padding: 10px; text-align: center; border: 1px solid black">
                    <p style="margin: 2px;">FOTO NOTA / KWITANSI</p>
                </th>
            </tr>
            <tr>
                <td
                    style="width: 50%; padding: 10px; text-align: center; min-height: 300px; border: 1px solid black; vertical-align: top;">
                    @if ($pengajuan->foto_kondisi)
                        @foreach ($pengajuan->foto_kondisi as $kondisi)
                            <img src="{{ public_path('storage/' . $kondisi) }}" alt="Foto Odometer" width="300">
                        @endforeach
                    @else
                        <p style="margin: 2px;">tidak ada gambar</p>
                    @endif
                    {{-- @php
                        $foto = $pengajuan->foto_kondisi ?? [];
                        $jumlah = count($foto);
                    @endphp
                
                    @if ($jumlah > 0)
                        <div style="margin-top: 10px;">
                            @if ($jumlah === 1)
                                <img src="{{ public_path('storage/' . $foto[0]) }}" alt="Foto Unit" width="300" style="margin: 5px;">
                            @elseif ($jumlah === 2)
                                @foreach ($foto as $img)
                                    <img src="{{ public_path('storage/' . $img) }}" alt="Foto Unit" width="150" style="margin: 5px;">
                                @endforeach
                            @else
                            <div>
                                {{-- Dua gambar pertama --}}
                    {{-- <div style="margin: 0; padding: 0;">
                                    <img src="{{ public_path('storage/' . $foto[0]) }}" alt="Foto Unit" width="150" style="margin: 2px;">
                                    <img src="{{ public_path('storage/' . $foto[1]) }}" alt="Foto Unit" width="150" style="margin: 2px;">
                                </div>
                            
                                {{-- Sisanya di bawah
                                <div style="margin-top: 2px; padding: 0;">
                                    @for ($i = 2; $i < $jumlah; $i++)
                                        <img src="{{ public_path('storage/' . $foto[$i]) }}" alt="Foto Unit" width="100" style="margin: 2px;">
                                    @endfor
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <p style="margin: 2px;">tidak ada gambar</p>
                    @endif --}}
                </td>

                <td
                    style="width: 50%; padding: 10px; text-align: center; min-height: 300px; border: 1px solid black; vertical-align: top;">
                    @if ($pengajuan->complete->foto_nota)
                        <img src="{{ public_path('storage/' . $pengajuan->complete->foto_nota) }}"
                            alt="Foto Odometer" width="300">
                    @else
                        <p style="margin: 2px;">tidak ada gambar</p>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Lampiran 2 --}}
    <div style="page-break-before: always;">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <th style="width: 80%; text-align: center; padding: 10px;">
                    <h1 style="margin: 2px;">LAMPIRAN</h1>
                </th>
                <th style="width: 20%; text-align: center;">
                    <h1 style="text-transform: uppercase; margin: 2px;">{{ $pengajuan->complete->kode }}</h1>
                </th>>
            </tr>
        </table>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr style="vertical-align: top;">
                <th style="width: 10%; padding: 10px; text-align: left;">
                    <p style="margin: 2px;">Bengkel</p>
                    <p style="margin: 2px;">Cabang</p>
                    <p style="margin: 2px;">Telp</p>
                    <p style="margin: 2px;">Estimasi</p>
                    <p style="margin: 2px;">No Rek</p>
                </th>
                <td style="width: 30%; padding: 10px;">
                    <p style="margin: 2px;">: {{ $pengajuan->complete->bengkel_estimasi }}</p>
                    <p style="margin: 2px;">: {{ $pengajuan->complete->cabang }}</p>
                    <p style="margin: 2px;">: {{ $pengajuan->complete->no_telp_bengkel }}</p>
                    <p style="margin: 2px;">: {{ $pengajuan->complete->estimasi }}</p>
                    <p style="margin: 2px;">: {{ $pengajuan->complete->no_rek_bengkel }}</p>
                </td>
                <th style="width: 20%; padding: 10px; text-align: left;">
                    <p style="margin: 2px;">No WO</p>
                    <p style="margin: 2px;">Jenis Pengajuan</p>
                    <p style="margin: 2px;">Tanggal WO</p>
                    <p style="margin: 2px;">User</p>
                    <p style="margin: 2px;">Telp</p>
                </th>
                <td style="width: 30%; padding: 10px;">
                    <p style="margin: 2px;">: {{ $pengajuan->no_pengajuan }}</p>
                    <p style="margin: 2px;">: {{ $pengajuan->keterangan }}</p>
                    <p style="margin: 2px;">: {{ $pengajuan->created_at->format('d F Y') }}</p>
                    <p style="margin: 2px;">: {{ $pengajuan->up }} - {{ $pengajuan->provinsi }}</p>
                    <p style="margin: 2px;">: {{ $pengajuan->no_wa }}</p>
                </td>
            </tr>
        </table>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <th style="width: 50%; padding: 10px; text-align: center; border: 1px solid black;">
                    <p style="margin: 2px;">FOTO PENGERJAAN BENGKEL</p>
                </th>
                <th style="width: 50%; padding: 10px; text-align: center; border: 1px solid black">
                    <p style="margin: 2px;">FOTO TAMBAHAN</p>
                </th>
            </tr>
            <tr>
                <td
                    style="width: 50%; padding: 10px; text-align: center; height: 300px; border: 1px solid black; vertical-align:top; align-items: left;">
                    @if ($pengajuan->complete->foto_pengerjaan_bengkel)
                        <img src="{{ public_path('storage/' . $pengajuan->complete->foto_pengerjaan_bengkel) }}"
                            alt="Foto Unit" width="300">
                    @else
                        <p style="margin: 2px;">tidak ada gambar</p>
                    @endif
                </td>
                @php
                    $fotoTambahan = $pengajuan->complete->foto_tambahan;
                    $fotoArray = is_string($fotoTambahan) ? json_decode($fotoTambahan, true) : $fotoTambahan;
                @endphp
                <td
                    style="width: 50%; padding: 10px; text-align: center; height: 300px; border: 1px solid black; vertical-align: top;">
                    @if (!empty($fotoArray) && isset($fotoArray[0]))
                        <img src="{{ public_path('storage/' . $fotoArray[0]) }}" alt="Foto Tambahan" width="300">
                    @else
                        <p style="margin: 2px;">tidak ada gambar</p>
                    @endif
                </td>
            </tr>
            <tr>
                <th style="width: 50%; padding: 10px; text-align: center; border: 1px solid black;">
                    <p style="margin: 2px;">FOTO TAMBAHAN</p>
                </th>
                <th style="width: 50%; padding: 10px; text-align: center; border: 1px solid black">
                    <p style="margin: 2px;">FOTO TAMBAHAN</p>
                </th>
            </tr>
            <tr>
                <td
                    style="width: 50%; padding: 10px; text-align: center; height: 300px; border: 1px solid black; vertical-align: top;">
                    @if (!empty($fotoArray) && isset($fotoArray[1]))
                        <img src="{{ public_path('storage/' . $fotoArray[1]) }}" alt="Foto Tambahan" width="300">
                    @else
                        <p style="margin: 2px;">tidak ada gambar</p>
                    @endif
                </td>
                <td
                    style="width: 50%; padding: 10px; text-align: center; height: 300px; border: 1px solid black; vertical-align: top;">
                    @if (!empty($fotoArray) && isset($fotoArray[2]))
                        <img src="{{ public_path('storage/' . $fotoArray[2]) }}" alt="Foto Tambahan" width="300">
                    @else
                        <p style="margin: 2px;">tidak ada gambar</p>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- sjp --}}
    <div style="page-break-before: always;">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <th style="width: 20%; text-align: center; border: 1px solid black;">
                    <img src="{{ public_path('images/logo_spj_samarent.jpg') }}" alt="logo samarent" width="150">
                </th>
                <th style="width: 80%; text-align: center; border: 1px solid black; position: relative;">
                    <div style="position: relative; text-align: center;">
                        <h2 style="text-transform: uppercase; margin: 0;">
                            FORM<br>PERMOHONAN DANA SJP
                        </h2>
                        <p style="position: absolute; top: 0; right: 10px; margin: 0; font-weight: normal;">
                            {{ $pengajuan->no_pengajuan }}
                        </p>
                    </div>
                </th>
            </tr>
        </table>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr style="vertical-align: top;">
                <th style="width: 10%; padding: 5px; text-align: left;">
                    <p style="margin: 0;">Tanggal</p>
                </th>
                <td style="width: 30%; padding: 5px;">
                    <p style="margin: 0;">:
                        {{ \Carbon\Carbon::parse($pengajuan->complete->tanggal_masuk_finance)->format('d F Y') }} </p>
                </td>
            </tr>
            <tr style="vertical-align: top;">
                <th style="width: 10%; padding: 5px; text-align: left;">
                    <p style="margin: 0;">Jumlah</p>
                </th>
                <td style="width: 30%; padding: 5px;">
                    <p style="margin: 0;">: Rp. {{ $pengajuan->complete->nominal_estimasi }} </p>
                </td>
            </tr>
            <tr style="vertical-align: top;">
                <th style="width: 10%; padding: 5px; text-align: left;">
                    <p style="margin: 0;">Terbilang</p>
                </th>
                <td style="width: 30%; padding: 5px;">
                    <p style="margin: 0;">: ({{ ucwords(terbilang($pengajuan->complete->nominal_estimasi)) }}
                        rupiah) </p>
                </td>
            </tr>
            <tr style="vertical-align: top;">
                <th style="width: 10%; padding: 5px; text-align: left;">
                    <p style="margin: 0;">Untuk Keperluan</p>
                </th>
                <td style="width: 30%; padding: 5px;">
                    <p style="margin: 0;">: {{ $pengajuan->service.' '. $pengajuan->nopol. ' KM '.
                        $pengajuan->odometer .' '. $pengajuan->service .' '. $pengajuan->type . ' ' . ($pengajuan->up === 'manual' ? $pengajuan->up_lainnya : $pengajuan->up) . ' ' . $pengajuan->provinsi . '-' . $pengajuan->kota }}</p>
                </td>
            </tr>
            <tr style="vertical-align: top;">
                <th style="width: 10%; padding: 5px; text-align: left;">
                    <p style="margin: 0;">Dibayarkan Oleh</p>
                </th>
                <td style="width: 30%; padding: 5px;">
                    <p style="margin: 0;">: PT. Samana Jaya Propertindo </p>
                </td>
            </tr>
            <tr style="vertical-align: top;">
                <th style="width: 10%; padding: 5px; text-align: left;">
                    <p style="margin: 0;">Dibayarkan Kepada</p>
                </th>
                <td style="width: 30%; padding: 5px;">
                    <p style="margin: 0;">: {{ $pengajuan->complete->payment_2 }}
                        {{ $pengajuan->complete->bank_2 }} {{ $pengajuan->complete->norek_2 }} <span
                            style="font-weight: bold">{{ $pengajuan->keterangan }}</span></p>
                </td>
            </tr>
        </table>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <th style="border: 1px solid black; padding : 0.5rem; width: 24%;">Dibuat Oleh</th>
                <th style="border: 1px solid black; padding : 0.5rem; width: 24%;">Disetujui Oleh</th>
                <th style="border: 1px solid black; padding : 0.5rem; width: 24%;">Diperiksa Oleh</th>
                <th style="border: 1px solid black; padding : 0.5rem; width: 24%;">Diketahui Oleh</th>
                <th style="border: 1px solid black; padding : 0.5rem; width: 24%;">Dibukukan Oleh</th>
            </tr>
            <tr>
                <td style="height: 113px; border: 1px solid black;">&nbsp;</td>
                <td style="height: 113px; border: 1px solid black;">&nbsp;</td>
                <td style="height: 113px; border: 1px solid black;">&nbsp;</td>
                <td style="height: 113px; border: 1px solid black;">&nbsp;</td>
                <td style="height: 113px; border: 1px solid black;">&nbsp;</td>
            </tr>
            <tr>
                <td style="height: 38px; border: 1px solid black;">Tgl : {{ $pengajuan->created_at->format('d F Y') }}
                </td>
                <td style="height: 38px; border: 1px solid black;">Tgl :</td>
                <td style="height: 38px; border: 1px solid black;">Tgl :</td>
                <td style="height: 38px; border: 1px solid black;">Tgl :</td>
                <td style="height: 38px; border: 1px solid black;">Tgl :</td>
            </tr>
            <tr>
                <td style="height: 38px; border: 1px solid black;">Nama :</td>
                <td style="height: 38px; border: 1px solid black;">Nama :</td>
                <td style="height: 38px; border: 1px solid black;">Nama :</td>
                <td style="height: 38px; border: 1px solid black;">Nama :</td>
                <td style="height: 38px; border: 1px solid black;">Nama :</td>
            </tr>
            <tr>
                <td style="height: 38px; border: 1px solid black;">Jabatan :</td>
                <td style="height: 38px; border: 1px solid black;">Jabatan :</td>
                <td style="height: 38px; border: 1px solid black;">Jabatan :</td>
                <td style="height: 38px; border: 1px solid black;">Jabatan :</td>
                <td style="height: 38px; border: 1px solid black;">Jabatan :</td>
            </tr>
        </table>
    </div>
</body>

</html>
