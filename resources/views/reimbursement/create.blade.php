<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Biaya Operasional</title>

    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .preview-image {
            max-width: 300px;
            max-height: 300px;
            margin-top: 10px;
        }

        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 10px;
        }

        .select2-dropdown {
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #D1D5DB;
            border-radius: 0.375rem;
            padding: 0.5rem;
        }

        .select2-container {
            width: 100% !important;
        }

        /* Loading Overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-content {
            text-align: center;
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3B82F6;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes progress {
            0% { width: 0%; }
            50% { width: 70%; }
            100% { width: 100%; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Mohon Tunggu...</h3>
            <p class="text-gray-600 mb-1">Sedang mengupload dan memproses foto</p>
            <p class="text-sm text-gray-500">Jangan tutup halaman ini</p>
        </div>
    </div>

    <div class="min-h-screen py-8 px-4">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h1 class="text-3xl font-bold text-gray-800 text-center">Form Biaya Operasional</h1>
                <p class="text-gray-600 text-center mt-2">Silakan isi form berikut dengan lengkap dan benar</p>
            </div>

            <!-- Success Message -->
            @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <p class="font-bold">Berhasil!</p>
                <p>{{ session('success') }}</p>
            </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <p class="font-bold">Error!</p>
                <p>{{ session('error') }}</p>
            </div>
            @endif

            <!-- Validation Errors -->
            @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <p class="font-bold">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="{{ route('reimbursement.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- User Selection -->
                    <div class="mb-6">
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih User (Ketik untuk mencari) <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="user_id"
                            id="user_id"
                            class="w-full"
                            required>
                            <option value="">-- Pilih User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Data Odometer Awal Section -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b-2 border-blue-500">
                            Data Odometer Awal
                        </h2>

                        <div class="mb-4">
                            <label for="km_awal" class="block text-sm font-medium text-gray-700 mb-2">
                                KM Awal <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="km_awal"
                                id="km_awal"
                                value="{{ old('km_awal') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: 12345"
                                min="0"
                                step="1"
                                required>
                            @error('km_awal')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="foto_odometer_awal" class="block text-sm font-medium text-gray-700 mb-2">
                                Foto Odometer Awal (Kamera) <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="file"
                                name="foto_odometer_awal"
                                id="foto_odometer_awal"
                                accept="image/*"
                                capture="environment"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                onchange="previewImage(this, 'preview_awal')"
                                required>
                            <p class="text-sm text-gray-500 mt-1">Format: JPG, JPEG, PNG. Maksimal 10MB</p>
                            <img id="preview_awal" class="preview-image hidden rounded-lg border-2 border-gray-300" alt="Preview">
                            @error('foto_odometer_awal')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Data Odometer Akhir Section -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b-2 border-blue-500">
                            Data Odometer Akhir
                        </h2>

                        <div class="mb-4">
                            <label for="km_akhir" class="block text-sm font-medium text-gray-700 mb-2">
                                KM Akhir
                            </label>
                            <input
                                type="number"
                                name="km_akhir"
                                id="km_akhir"
                                value="{{ old('km_akhir') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: 12445"
                                min="0"
                                step="1">
                            @error('km_akhir')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="foto_odometer_akhir" class="block text-sm font-medium text-gray-700 mb-2">
                                Foto Odometer Akhir (Kamera)
                            </label>
                            <input
                                type="file"
                                name="foto_odometer_akhir"
                                id="foto_odometer_akhir"
                                accept="image/*"
                                capture="environment"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                onchange="previewImage(this, 'preview_akhir')">
                            <p class="text-sm text-gray-500 mt-1">Format: JPG, JPEG, PNG. Maksimal 10MB</p>
                            <img id="preview_akhir" class="preview-image hidden rounded-lg border-2 border-gray-300" alt="Preview">
                            @error('foto_odometer_akhir')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Detail Perjalanan Section -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b-2 border-blue-500">
                            Detail Perjalanan
                        </h2>

                        <div class="mb-4">
                            <label for="tujuan_perjalanan" class="block text-sm font-medium text-gray-700 mb-2">
                                Tujuan Perjalanan <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                name="tujuan_perjalanan"
                                id="tujuan_perjalanan"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: Perjalanan dinas ke Jakarta untuk meeting dengan klien"
                                required>{{ old('tujuan_perjalanan') }}</textarea>
                            @error('tujuan_perjalanan')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                                Keterangan Tambahan
                            </label>
                            <textarea
                                name="keterangan"
                                id="keterangan"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Keterangan tambahan jika ada">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Dana Section -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b-2 border-blue-500">
                            Informasi Dana
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="dana_masuk" class="block text-sm font-medium text-gray-700 mb-2">
                                    Dana Masuk (Rp)
                                </label>
                                <input
                                    type="number"
                                    name="dana_masuk"
                                    id="dana_masuk"
                                    value="{{ old('dana_masuk', 0) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="0"
                                    min="0"
                                    step="0.01">
                                @error('dana_masuk')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="dana_keluar" class="block text-sm font-medium text-gray-700 mb-2">
                                    Dana Keluar (Rp)
                                </label>
                                <input
                                    type="number"
                                    name="dana_keluar"
                                    id="dana_keluar"
                                    value="{{ old('dana_keluar', 0) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="0"
                                    min="0"
                                    step="0.01">
                                @error('dana_keluar')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-4">
                        <button
                            type="reset"
                            class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200 font-medium">
                            Reset Form
                        </button>
                        <button
                            type="submit"
                            id="submitBtn"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                            Submit
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6 text-gray-600 text-sm">
                <p>&copy; 2025 servicesamarent.com. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script>
        // Initialize Select2 for searchable dropdown
        $(document).ready(function() {
            $('#user_id').select2({
                placeholder: '-- Pilih User --',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "User tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    },
                    inputTooShort: function() {
                        return "Ketik untuk mencari";
                    }
                }
            });

            // Form submit handler
            $('form').on('submit', function(e) {
                // Show loading overlay
                $('#loadingOverlay').addClass('active');

                // Disable submit button to prevent double submit
                $('#submitBtn').prop('disabled', true).text('Mohon Tunggu...');

                // Optional: You can also disable reset button
                $('button[type="reset"]').prop('disabled', true);

                // Form will continue to submit normally
                return true;
            });
        });

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };

                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '';
                preview.classList.add('hidden');
            }
        }

        // Show file name when file selected
        $('input[type="file"]').on('change', function() {
            const fileName = this.files[0]?.name;
            if (fileName) {
                const label = $(this).siblings('label');
                const originalText = label.text();
                if (!label.data('original')) {
                    label.data('original', originalText);
                }
                label.html(label.data('original') + ' <span class="text-green-600">✓ ' + fileName + '</span>');
            }
        });

        // Prevent accidental page leave during upload
        let formSubmitted = false;
        $('form').on('submit', function() {
            formSubmitted = true;
        });

        window.addEventListener('beforeunload', function(e) {
            if (!formSubmitted && ($('#foto_odometer_awal').val() || $('#foto_odometer_akhir').val())) {
                e.preventDefault();
                e.returnValue = '';
                return 'Data form belum tersimpan. Yakin ingin meninggalkan halaman?';
            }
        });
    </script>
</body>
</html>
