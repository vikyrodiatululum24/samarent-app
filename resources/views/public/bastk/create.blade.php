<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form BASTK (Berita Acara Serah Terima Kendaraan)</title>

    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .preview-image {
            max-width: 300px;
            max-height: 300px;
            margin-top: 10px;
        }

        /* Select2 Custom Styling matching reimbursement page */
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
    </style>
</head>
<body class="bg-gray-100">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Mohon Tunggu...</h3>
            <p class="text-gray-600 mb-1">Sedang mengupload dan memproses data BASTK</p>
            <p class="text-sm text-gray-500">Jangan tutup halaman ini</p>
        </div>
    </div>

    <div class="min-h-screen py-8 px-4" x-data="bastkWizard()">
        <div class="max-w-5xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h1 class="text-3xl font-bold text-gray-800 text-center">Form BASTK</h1>
                <p class="text-gray-600 text-center mt-2">Berita Acara Serah Terima Kendaraan</p>
            </div>

            <!-- Success Message -->
            @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-sm">
                <p class="font-bold">Berhasil!</p>
                <p>{{ session('success') }}</p>
            </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-sm">
                <p class="font-bold">Error!</p>
                <p>{{ session('error') }}</p>
            </div>
            @endif

            <!-- Validation Errors -->
            @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-sm">
                <p class="font-bold">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Wizard Progress Steps Partial -->
            @include('public.bastk.partials.progress')

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="{{ route('bastk.store') }}" method="POST" enctype="multipart/form-data" id="bastkForm">
                    @csrf

                    <!-- Step 1 Partial -->
                    @include('public.bastk.partials.step1')

                    <!-- Step 2 Partial -->
                    @include('public.bastk.partials.step2')

                    <!-- Step 3 Partial -->
                    @include('public.bastk.partials.step3')
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6 text-gray-600 text-sm">
                <p>&copy; 2025 servicesamarent.com. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script>
        // Alpine.js Wizard Controller
        function bastkWizard() {
            return {
                step: 1,
                kondisiSelected: @json(old('kondisi_unit', [])),
                goToStep(newStep) {
                    if (newStep >= 1 && newStep <= 3) {
                        this.step = newStep;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                }
            };
        }

        // Alpine.js Item Row Component (Mutual exclusion for checkboxes)
        function itemRow(name) {
            return {
                baik: false,
                rusak: false,
                tidakAda: false,
                bbmBars: '',
                onBaikChange() {
                    if (this.baik) {
                        this.rusak = false;
                        this.tidakAda = false;
                    }
                },
                onRusakChange() {
                    if (this.rusak) {
                        this.baik = false;
                        this.tidakAda = false;
                    }
                },
                onTidakAdaChange() {
                    if (this.tidakAda) {
                        this.baik = false;
                        this.rusak = false;
                    }
                }
            };
        }

        // Initialize Select2 & Form Handlers
        $(document).ready(function() {
            // Select2 for User Selection (created_by)
            $('#created_by').select2({
                placeholder: '-- Pilih User --',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() { return "User tidak ditemukan"; },
                    searching: function() { return "Mencari..."; }
                }
            });

            // Select2 for Unit Selection
            $('#unit_id').select2({
                placeholder: '-- Pilih Unit --',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() { return "Unit tidak ditemukan"; },
                    searching: function() { return "Mencari..."; }
                }
            });

            // Form Submit Loading State
            $('#bastkForm').on('submit', function(e) {
                $('#loadingOverlay').addClass('active');
                $('#submitBtn').prop('disabled', true).text('Mohon Tunggu...');
                $('button[type="reset"]').prop('disabled', true);
                return true;
            });
        });

        // Image Preview Handler
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
    </script>
</body>
</html>
