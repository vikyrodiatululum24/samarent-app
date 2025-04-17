<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print SPK</title>

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <link rel="stylesheet" href="{{ asset('css/filament/pdf/style.css') }}">
</head>

<body>
    <div id="konten" class="mt-5cm mx-auto p-4 bg-white w-[21cm] h-[24.7cm]">
        <div class="flex items-center justify-between mb-4">
            <div class="w-4cm border-2 border-black h-3cm text-center mr-2 py-3">
                <p class="uppercase font-bold text-xl">km awal</p>
                <p class="uppercase font-bold text-2xl">{{ $pengajuan->odometer }}</p>
            </div>
            <h1 class="text-4xl font-bold text-center">Surat Perintah Kerja</h1>
            <div class="w-4cm border-2 border-black h-3cm text-center mr-2 py-3">
                <p class="uppercase font-bold text-xl">kode</p>
                <p class="uppercase font-bold text-5xl">{{ $pengajuan->complete->kode }}</p>
            </div>
        </div>
        <div class="flex w-full justify-between">
            <div class="flex">
                <div>
                    <p class="">Bengkel</p>
                    <p class="">Telp</p>
                    <p class="">Estimasi</p>
                    <p class="">No Rek</p>
                </div>
                <div class="mx-4">
                    <p class="capitalize ">: {{ $pengajuan->complete->bengkel_estimasi }}</p>
                    <p class="capitalize ">: {{ $pengajuan->complete->no_telp_bengkel }}</p>
                </div>
            </div>
            <div class="flex mb-4">
                <div>
                    <p class="">No WO</p>
                    <p class="">Jenis Pengajuan</p>
                    <p class="">Tanggal WO</p>
                    <p class="">User</p>
                    <p class="">Telp</p>
                </div>
                <div class="mx-4">
                    <p class="capitalize">: {{ $pengajuan->no_pengajuan }}</p>
                    <p class="capitalize">: {{ $pengajuan->keterangan }}</p>
                    <p class="capitalize">: {{ $pengajuan->created_at->format('d F Y') }}</p>
                    <p class="capitalize">: {{ $pengajuan->up }} - {{ $pengajuan->provinsi }}</p>
                    <p class="capitalize">: {{ $pengajuan->no_wa }}</p>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <table class="w-full border-2 border-black text-center mt-4">
                <thead>
                    <tr>
                        <th class="border-2 border-black text-center font-bold p-2">No Polisi</th>
                        <th class="border-2 border-black text-center font-bold p-2">KM</th>
                        <th class="border-2 border-black text-center font-bold p-2">Type Unit</th>
                        <th class="border-2 border-black text-center font-bold p-2">Permintaan / Part</th>
                        <th class="border-2 border-black text-center font-bold p-2">Jumlah</th>
                        <th class="border-2 border-black text-center font-bold p-2">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border-2 border-black text-center p-2">{{ $pengajuan->nopol }}</td>
                        <td class="border-2 border-black text-center p-2">{{ $pengajuan->odometer }}</td>
                        <td class="border-2 border-black text-center p-2">{{ $pengajuan->type }}</td>
                        <td class="border-2 border-black text-center p-2">{{ $pengajuan->service }}</td>
                        <td class="border-2 border-black text-center p-2">{{ $pengajuan->jumlah_part }}</td>
                        <td class="border-2 border-black text-center p-2">{{ $pengajuan->keterangan }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mb-6">
            <p>Perhatian :</p>
            <ol class="list-decimal ml-4">
                <li>Bila ada pekerjaan atau pergantian spare part di luar SPK, mohon konfirmasi ke PIC</li>
                <li>Tagihan dapat dikirimkan segera dan maksimal 14 hari kerja ke PT Samana Jaya Propertindo</li>
            </ol>
        </div>
        <div class="mb-6">
            <p>Jakarta, {{ now()->format('d F Y') }}</p>
            <p class="">Hormat Kami</p>
            <p>PT. Samana Jaya Propertindo</p>
        </div>

        <table class="w-full border-2 border-black text-center mt-4">
            <tr>
                <th class="border-2 border-black text-center p-2 w-4cm">Dibuat</th>
                <th class="border-2 border-black text-center p-2 w-4cm">Disetujui</th>
                <th class="border-2 border-black text-center p-2 w-4cm">Diperiksa</th>
                <th class="border-2 border-black text-center p-2 w-4cm">Diketahui</th>
            </tr>
            <tr class="h-3cm">
                <td class="border-2 border-black text-center p-2 w-4cm"></td>
                <td class="border-2 border-black text-center p-2 w-4cm"></td>
                <td class="border-2 border-black text-center p-2 w-4cm"></td>
                <td class="border-2 border-black text-center p-2 w-4cm"></td>
            </tr>
            <tr class="h-[1cm]">
                <td class="border-2 border-black text-center p-2 w-4cm"></td>
                <td class="border-2 border-black text-center p-2 w-4cm"></td>
                <td class="border-2 border-black text-center p-2 w-4cm"></td>
                <td class="border-2 border-black text-center p-2 w-4cm"></td>
            </tr>
        </table>
    </div>
</body>

</html>
