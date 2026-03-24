<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pra Pengajuan</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem;
            padding: 0.45rem 0.85rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 42px;
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem;
            padding: 0.2rem 0.45rem;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #dbeafe;
            border: 1px solid #bfdbfe;
            color: #1e3a8a;
            border-radius: 0.375rem;
            padding: 0.15rem 0.5rem;
        }
    </style>
</head>

<body class="bg-slate-100">
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-xl shadow p-6 mb-6">
                <h1 class="text-2xl font-bold text-slate-800 text-center">Form Pra Pengajuan</h1>
                <p class="text-sm text-slate-500 mt-2 text-center">Silakan isi form di bawah ini dengan data yang
                    lengkap.</p>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-300 text-green-700 p-4 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded-lg mb-4">
                    <p class="font-semibold mb-1">Terdapat kesalahan pada form:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow p-6">
                <form action="{{ route('public.pra-pengajuan.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="nama_pic" class="block text-sm font-medium text-slate-700 mb-1">Nama PIC</label>
                        <input type="text" name="nama_pic" id="nama_pic" value="{{ old('nama_pic') }}" required
                            class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="no_wa" class="block text-sm font-medium text-slate-700 mb-1">No. WhatsApp</label>
                        <input type="text" name="no_wa" id="no_wa" value="{{ old('no_wa') }}" required
                            class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="project" class="block text-sm font-medium text-slate-700 mb-1">Project</label>
                        <input type="text" name="project" id="project" value="{{ old('project') }}" required
                            class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="up" class="block text-sm font-medium text-slate-700 mb-1">UP</label>
                        <select name="up" id="up" required
                            class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih UP</option>
                            <option value="UP1" {{ old('up') == 'UP1' ? 'selected' : '' }}>UP1</option>
                            <option value="UP2" {{ old('up') == 'UP2' ? 'selected' : '' }}>UP2</option>
                            <option value="UP3" {{ old('up') == 'UP3' ? 'selected' : '' }}>UP3</option>
                            <option value="UP5" {{ old('up') == 'UP5' ? 'selected' : '' }}>UP5</option>
                            <option value="UP7" {{ old('up') == 'UP7' ? 'selected' : '' }}>UP7</option>
                            <option value="Lainnya" {{ old('up') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <div id="up_lainnya_wrapper" class="{{ old('up') == 'Lainnya' ? '' : 'hidden' }}">
                        <label for="up_lainnya" class="block text-sm font-medium text-slate-700 mb-1">UP Lainnya</label>
                        <input type="text" name="up_lainnya" id="up_lainnya" value="{{ old('up_lainnya') }}"
                            class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="provinsi" class="block text-sm font-medium text-slate-700 mb-1">Provinsi</label>
                            <input type="text" name="provinsi" id="provinsi" value="{{ old('provinsi') }}" required
                                class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="kota" class="block text-sm font-medium text-slate-700 mb-1">Kota</label>
                            <input type="text" name="kota" id="kota" value="{{ old('kota') }}" required
                                class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label for="unitId" class="block text-sm font-medium text-slate-700 mb-1">Unit (Ketik untuk
                            mencari)</label>
                        <select name="unitId" id="unitId" required>
                            <option value="">Pilih Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}"
                                    {{ old('unitId') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->nopol }} - {{ $unit->merk }} {{ $unit->type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="service" class="block text-sm font-medium text-slate-700 mb-1">Service (bisa pilih lebih dari satu)</label>
                        <select name="service[]" id="service" multiple required>
                            <option value="Service Ganti Oli" {{ in_array('Service Ganti Oli', old('service', [])) ? 'selected' : '' }}>Service Ganti Oli</option>
                            <option value="Rem Depan" {{ in_array('Rem Depan', old('service', [])) ? 'selected' : '' }}>Rem Depan</option>
                            <option value="Rem Belakang" {{ in_array('Rem Belakang', old('service', [])) ? 'selected' : '' }}>Rem Belakang</option>
                            <option value="Lampu Depan" {{ in_array('Lampu Depan', old('service', [])) ? 'selected' : '' }}>Lampu Depan</option>
                            <option value="Lampu Belakang" {{ in_array('Lampu Belakang', old('service', [])) ? 'selected' : '' }}>Lampu Belakang</option>
                            <option value="Ban Depan" {{ in_array('Ban Depan', old('service', [])) ? 'selected' : '' }}>Ban Depan</option>
                            <option value="Ban Belakang" {{ in_array('Ban Belakang', old('service', [])) ? 'selected' : '' }}>Ban Belakang</option>
                            <option value="Gear Set" {{ in_array('Gear Set', old('service', [])) ? 'selected' : '' }}>Gear Set</option>
                            <option value="Kampas Kopling" {{ in_array('Kampas Kopling', old('service', [])) ? 'selected' : '' }}>Kampas Kopling</option>
                            <option value="Filter Udara" {{ in_array('Filter Udara', old('service', [])) ? 'selected' : '' }}>Filter Udara</option>
                            <option value="Filter Oli" {{ in_array('Filter Oli', old('service', [])) ? 'selected' : '' }}>Filter Oli</option>
                            <option value="Busi" {{ in_array('Busi', old('service', [])) ? 'selected' : '' }}>Busi</option>
                            <option value="Ban Dalam" {{ in_array('Ban Dalam', old('service', [])) ? 'selected' : '' }}>Ban Dalam</option>
                            <option value="Spion" {{ in_array('Spion', old('service', [])) ? 'selected' : '' }}>Spion</option>
                            <option value="Lampu Stop" {{ in_array('Lampu Stop', old('service', [])) ? 'selected' : '' }}>Lampu Stop</option>
                            <option value="Lampu Sein depan" {{ in_array('Lampu Sein depan', old('service', [])) ? 'selected' : '' }}>Lampu Sein depan</option>
                            <option value="Lampu Sein Belakang" {{ in_array('Lampu Sein Belakang', old('service', [])) ? 'selected' : '' }}>Lampu Sein Belakang</option>
                            <option value="Bearing Depan" {{ in_array('Bearing Depan', old('service', [])) ? 'selected' : '' }}>Bearing Depan</option>
                            <option value="Bearung Belakang" {{ in_array('Bearung Belakang', old('service', [])) ? 'selected' : '' }}>Bearung Belakang</option>
                            <option value="Accu" {{ in_array('Accu', old('service', [])) ? 'selected' : '' }}>Accu</option>
                            <option value="Lainnya" {{ in_array('Lainnya', old('service', [])) ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <div id="service_lainnya_wrapper" class="{{ in_array('Lainnya', old('service', [])) ? '' : 'hidden' }}">
                        <label for="service_lainnya" class="block text-sm font-medium text-slate-700 mb-1">Detail Service Lainnya</label>
                        <div class="flex gap-2">
                            <input type="text" name="service_lainnya" id="service_lainnya"
                                class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Tulis service lainnya lalu klik Tambah">
                            <button type="button" id="btn_tambah_service_lainnya"
                                class="px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium whitespace-nowrap">
                                Tambah
                            </button>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Service tambahan akan masuk ke daftar service yang dipilih.</p>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition">
                        Kirim Form
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#unitId').select2({
                placeholder: 'Pilih unit',
                allowClear: true
            });

            $('#service').select2({
                placeholder: 'Pilih service',
                closeOnSelect: false,
                width: '100%'
            });

            const upSelect = document.getElementById('up');
            const wrapper = document.getElementById('up_lainnya_wrapper');
            const serviceSelect = document.getElementById('service');
            const serviceLainnyaWrapper = document.getElementById('service_lainnya_wrapper');
            const serviceLainnyaInput = document.getElementById('service_lainnya');
            const tambahServiceButton = document.getElementById('btn_tambah_service_lainnya');

            upSelect.addEventListener('change', function() {
                if (this.value === 'Lainnya') {
                    wrapper.classList.remove('hidden');
                } else {
                    wrapper.classList.add('hidden');
                }
            });

            const toggleServiceLainnya = function() {
                const selected = $('#service').val() || [];

                if (selected.includes('Lainnya')) {
                    serviceLainnyaWrapper.classList.remove('hidden');
                } else {
                    serviceLainnyaWrapper.classList.add('hidden');
                    serviceLainnyaInput.value = '';
                }
            };

            $('#service').on('change select2:select select2:unselect', toggleServiceLainnya);
            toggleServiceLainnya();

            const addCustomService = function() {
                const customText = serviceLainnyaInput.value.trim();

                if (!customText) {
                    return;
                }

                const customValue = customText;
                const normalize = (value) => value.trim().toLowerCase();
                const hasOption = Array.from(serviceSelect.options)
                    .some(option => normalize(option.value) === normalize(customValue));

                if (!hasOption) {
                    const newOption = new Option(customText, customValue, true, true);
                    serviceSelect.add(newOption);
                } else {
                    Array.from(serviceSelect.options).forEach(option => {
                        if (normalize(option.value) === normalize(customValue)) {
                            option.selected = true;
                        }
                    });
                }

                $('#service').trigger('change');
                serviceLainnyaInput.value = '';
                serviceLainnyaInput.focus();
            };

            tambahServiceButton.addEventListener('click', addCustomService);

            serviceLainnyaInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    addCustomService();
                }
            });
        });
    </script>
</body>

</html>
