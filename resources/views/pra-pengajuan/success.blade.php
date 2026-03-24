<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berhasil Mengirim Form</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-xl shadow p-8 text-center">
            <div class="w-16 h-16 rounded-full bg-green-100 mx-auto flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-slate-800 mb-2">Form Berhasil Dikirim</h1>
            <p class="text-slate-600 mb-6">Terima kasih. Data pra pengajuan Anda sudah kami terima.</p>

            <a href="{{ route('public.pra-pengajuan.create') }}" class="inline-block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition">
                Isi Form Baru
            </a>
        </div>
    </div>
</body>
</html>
