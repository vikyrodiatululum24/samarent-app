<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berhasil - Samarent App</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-8 px-4">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <!-- Success Icon -->
                <div class="mb-6">
                    <svg class="mx-auto h-20 w-20 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <!-- Success Message -->
                <h1 class="text-3xl font-bold text-gray-800 mb-4">Berhasil!</h1>
                <p class="text-gray-600 mb-8">
                    Data reimbursement Anda telah berhasil disimpan.
                    Tim kami akan segera memproses pengajuan reimbursement Anda.
                </p>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="{{ route('reimbursement.create', ['token' => $token]) }}"
                       class="block w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                        Buat Reimbursement Baru
                    </a>

                    <button
                        onclick="window.close()"
                        class="block w-full px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200 font-medium">
                        Tutup Halaman
                    </button>
                </div>

                <!-- Additional Info -->
                <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>Info:</strong> Simpan link form ini untuk pengajuan reimbursement selanjutnya.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6 text-gray-600 text-sm">
                <p>&copy; {{ date('Y') }} Samarent App. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
