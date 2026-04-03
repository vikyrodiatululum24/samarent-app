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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
            <div class="bg-white rounded-xl shadow mb-6">
                <img src="{{ asset('images/header_samarent.jpg') }}" alt="header samarent" width="100%"
                    style="border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;">
                <div class="pb-6">
                    <h1 class="text-2xl font-bold text-slate-800 text-center">Form Pengajuan</h1>
                    <p class="text-sm text-slate-500 mt-2 text-center">Silakan isi form di bawah ini dengan data yang
                        lengkap.</p>
                </div>
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
                <form action="{{ route('public.pra-pengajuan.store') }}" method="POST" class="space-y-5"
                    enctype="multipart/form-data">
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
                        <label for="project" class="block text-sm font-medium text-slate-700 mb-1">Perusahaan</label>
                        <select name="project" id="project" required
                            class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Perusahaan</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}"
                                    {{ old('project') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="up" class="block text-sm font-medium text-slate-700 mb-1">Unit
                            Pelaksana</label>
                        <select name="up" id="up" required
                            class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Unit Pelaksana</option>
                            <option value="UP 1" {{ old('up') == 'UP 1' ? 'selected' : '' }}>UP 1</option>
                            <option value="UP 2" {{ old('up') == 'UP 2' ? 'selected' : '' }}>UP 2</option>
                            <option value="UP 3" {{ old('up') == 'UP 3' ? 'selected' : '' }}>UP 3</option>
                            <option value="UP 5" {{ old('up') == 'UP 5' ? 'selected' : '' }}>UP 5</option>
                            <option value="UP 7" {{ old('up') == 'UP 7' ? 'selected' : '' }}>UP 7</option>
                            <option value="CUST JEPANG" {{ old('up') == 'CUST JEPANG' ? 'selected' : '' }}>CUST JEPANG</option>
                            <option value="manual" {{ old('up') == 'manual' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <div id="up_lainnya_wrapper" class="{{ old('up') == 'manual' ? '' : 'hidden' }}">
                        <label for="up_lainnya" class="block text-sm font-medium text-slate-700 mb-1">Unit Pelaksana
                            Lainnya</label>
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

                    <!-- SERVICE UNITS -->
                    <h3 class="font-bold text-lg mb-2">Detail Unit Service</h3>

                    <div id="service-units-wrapper">

                        <!-- ITEM 1 -->
                        <div class="service-unit-item border p-4 rounded mb-3">

                            <div class="mb-3">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Unit</label>
                                <select name="service_units[0][unit_id]" class="unit-select w-full mb-2" required>
                                    <option value="">Pilih Unit</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">
                                            {{ $unit->nopol }} - {{ $unit->merk }} {{ $unit->type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="odometer"
                                    class="block text-sm font-medium text-slate-700 mb-1">Odometer</label>
                                <input type="number" name="service_units[0][odometer]" id="odometer"
                                    class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                            </div>

                            <div class="mb-3">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Service</label>
                                <select name="service_units[0][service][]" multiple class="service-select w-full mb-2"
                                    required>
                                    <option value="Rem Depan">Rem Depan</option>
                                    <option value="Rem Belakang">Rem Belakang</option>
                                    <option value="Lampu Depan">Lampu Depan</option>
                                    <option value="Lampu Belakang">Lampu Belakang</option>
                                    <option value="Ban Depan">Ban Depan</option>
                                    <option value="Ban Belakang">Ban Belakang</option>
                                    <option value="Gear Set">Gear Set</option>
                                    <option value="Kampas Kopling">Kampas Kopling</option>
                                    <option value="Filter Udara">Filter Udara</option>
                                    <option value="Filter Oli">Filter Oli</option>
                                    <option value="Busi">Busi</option>
                                    <option value="Ban Dalam">Ban Dalam</option>
                                    <option value="Spion">Spion</option>
                                    <option value="Lampu Stop">Lampu Stop</option>
                                    <option value="Lampu Sein depan">Lampu Sein depan</option>
                                    <option value="Lampu Sein Belakang">Lampu Sein Belakang</option>
                                    <option value="Bearing Depan">Bearing Depan</option>
                                    <option value="Bearing Belakang">Bearing Belakang</option>
                                    <option value="Accu">Accu</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>

                                <!-- INPUT LAINNYA -->
                                <div class="service-lainnya-wrapper hidden mt-2">
                                    <label for="service_lainnya"
                                        class="block text-sm font-medium text-slate-700 mb-1">Service Lainnya</label>
                                    <div class="flex gap-2">
                                        <input type="text" class="service-lainnya-input border rounded p-2 w-full"
                                            placeholder="Tulis service lainnya">
                                        <button type="button"
                                            class="btn-add-service bg-gray-700 text-white px-3 rounded">
                                            Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>


                            <label for="foto_unit">Foto Unit</label>
                            <input type="file" name="service_units[0][foto_unit]" id="foto_unit"
                                class="foto-unit-input w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 mb-2"
                                accept="image/*" required placeholder="Masukan Foto Unit">
                            <div class="preview-foto-unit mb-3"></div>

                            <label for="foto_odometer">Foto Odometer</label>
                            <input type="file" name="service_units[0][foto_odometer]" id="foto_odometer"
                                class="input-foto-odometer w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 mb-2"
                                accept="image/*" required placeholder="Masukan Odometer">
                            <div class="preview-foto_odometer mb-3"></div>

                            <label for="foto_kondisi">Foto Kondisi</label>
                            <input type="file" multiple name="service_units[0][foto_kondisi][]" id="foto_kondisi"
                                class="input-foto-kondisi w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 mb-2"
                                accept="image/*" required placeholder="Masukan Foto Kondisi">
                            <div class="preview-foto-kondisi flex gap-2 flex-wrap mb-3"></div>

                            <button type="button" class="remove-item text-red-500 text-sm">
                                Hapus
                            </button>
                        </div>

                    </div>

                    <button type="button" id="add-service-unit"
                        class="bg-gray-700 text-white px-4 py-2 rounded mb-4">
                        + Tambah Unit
                    </button>

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
            initSelect2();

            $('#project').select2({
                placeholder: 'Pilih perusahaan',
                allowClear: true
            });

            // Initialize Select2 for unit and service dropdowns
            function initSelect2(target = null) {
                let unit = target ? target.find('.unit-select') : $('.unit-select');
                let service = target ? target.find('.service-select') : $('.service-select');

                unit.select2({
                    placeholder: 'Pilih Unit',
                    width: '100%'
                });

                service.select2({
                    placeholder: 'Pilih Service',
                    closeOnSelect: false,
                    width: '100%'
                });
            }

            let index = 1; // untuk menghitung jumlah item service unit

            // tambah item service unit
            $('#add-service-unit').on('click', function() {

                let html = `
                        <div class="service-unit-item border p-4 rounded mb-3">

                            <div class="mb-3">
                                <label>Unit</label>
                                <select name="service_units[${index}][unit_id]" class="unit-select w-full mb-2" required>
                                    <option value="">Pilih Unit</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">
                                            {{ $unit->nopol }} - {{ $unit->merk }} {{ $unit->type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="odometer" class="block text-sm font-medium text-slate-700 mb-1">Odometer</label>
                                <input type="number" name="service_units[${index}][odometer]" id="odometer"
                                    class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                            </div>

                            <div class="mb-3">
                                <label>Service</label>
                                <select name="service_units[${index}][service][]" multiple class="service-select w-full mb-2" required>
                                    <option value="Rem Depan">Rem Depan</option>
                                    <option value="Rem Belakang">Rem Belakang</option>
                                    <option value="Lampu Depan">Lampu Depan</option>
                                    <option value="Lampu Belakang">Lampu Belakang</option>
                                    <option value="Ban Depan">Ban Depan</option>
                                    <option value="Ban Belakang">Ban Belakang</option>
                                    <option value="Gear Set">Gear Set</option>
                                    <option value="Kampas Kopling">Kampas Kopling</option>
                                    <option value="Filter Udara">Filter Udara</option>
                                    <option value="Filter Oli">Filter Oli</option>
                                    <option value="Busi">Busi</option>
                                    <option value="Ban Dalam">Ban Dalam</option>
                                    <option value="Spion">Spion</option>
                                    <option value="Lampu Stop">Lampu Stop</option>
                                    <option value="Lampu Sein depan">Lampu Sein depan</option>
                                    <option value="Lampu Sein Belakang">Lampu Sein Belakang</option>
                                    <option value="Bearing Depan">Bearing Depan</option>
                                    <option value="Bearing Belakang">Bearing Belakang</option>
                                    <option value="Accu">Accu</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>

                                <!-- INPUT LAINNYA -->
                                <div class="service-lainnya-wrapper hidden mt-2">
                                    <label for="service_lainnya"
                                        class="block text-sm font-medium text-slate-700 mb-1">Service Lainnya</label>
                                    <div class="flex gap-2">
                                        <input type="text" class="service-lainnya-input border rounded p-2 w-full"
                                            placeholder="Tulis service lainnya">
                                        <button type="button"
                                            class="btn-add-service bg-gray-700 text-white px-3 rounded">
                                            Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="foto_unit">Foto Unit</label>
                                <input type="file" name="service_units[${index}][foto_unit]" id="foto_unit"
                                    class="foto-unit-input w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 mb-2"
                                    accept="image/*">
                                <div class="preview-foto-unit mb-3"></div>
                            </div>

                            <div class="mb-3">
                                <label for="foto_odometer">Foto Odometer</label>
                                <input type="file" name="service_units[${index}][foto_odometer]" id="foto_odometer"
                                    class="input-foto-odometer w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 mb-2"
                                    accept="image/*">
                                <div class="preview-foto_odometer mb-3"></div>
                            </div>

                            <div class="mb-3">
                                <label for="foto_kondisi">Foto Kondisi</label>
                                <input type="file" multiple name="service_units[${index}][foto_kondisi][]" id="foto_kondisi"
                                    class="input-foto-kondisi w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 mb-2"
                                    accept="image/*">
                                <div class="preview-foto-kondisi flex gap-2 flex-wrap mb-3"></div>
                            </div>

                            <button type="button" class="remove-item text-red-500 text-sm">
                                Hapus
                            </button>
                        </div>`;

                let newItem = $(html);

                $('#service-units-wrapper').append(newItem);

                initSelect2(newItem);
                index++;
            });

            // hapus item
            $(document).on('click', '.remove-item', function() {
                $(this).closest('.service-unit-item').remove();
            });

            // Show/hide input service lainnya
            $(document).on('change', '.service-select', function() {

                let wrapper = $(this).closest('.service-unit-item');
                let selected = $(this).val() || [];

                if (selected.includes('Lainnya')) {
                    wrapper.find('.service-lainnya-wrapper').removeClass('hidden');
                } else {
                    wrapper.find('.service-lainnya-wrapper').addClass('hidden');
                    wrapper.find('.service-lainnya-input').val('');
                }
            });

            // button add service lainnya
            $(document).on('click', '.btn-add-service', function() {

                let wrapper = $(this).closest('.service-unit-item');
                let input = wrapper.find('.service-lainnya-input');
                let select = wrapper.find('.service-select');

                let value = input.val().trim();

                if (!value) return;

                // cek apakah sudah ada
                let exists = false;

                select.find('option').each(function() {
                    if ($(this).val().toLowerCase() === value.toLowerCase()) {
                        exists = true;
                    }
                });

                // kalau belum ada → tambah option baru
                if (!exists) {
                    let newOption = new Option(value, value, true, true);
                    select.append(newOption);
                } else {
                    // kalau sudah ada → select saja
                    select.find(`option[value="${value}"]`).prop('selected', true);
                }

                // trigger select2 update
                select.trigger('change');

                // reset input
                input.val('').focus();
            });

            const upSelect = document.getElementById('up');
            const wrapper = document.getElementById('up_lainnya_wrapper');
            upSelect.addEventListener('change', function() {
                if (this.value === 'manual') {
                    wrapper.classList.remove('hidden');
                } else {
                    wrapper.classList.add('hidden');
                    document.getElementById('up_lainnya').value = '';
                }
            });

            // Preview foto unit
            $(document).on('change', '.foto-unit-input', function(e) {

                let wrapper = $(this).closest('.service-unit-item');
                let preview = wrapper.find('.preview-foto-unit');

                preview.html(''); // reset

                let file = e.target.files[0];

                if (file) {
                    let reader = new FileReader();

                    reader.onload = function(e) {
                        preview.html(`
                <img src="${e.target.result}"
                    class="w-32 h-32 object-cover rounded border">
            `);
                    }

                    reader.readAsDataURL(file);
                }
            });
            // Preview foto odometer
            $(document).on('change', '.input-foto-odometer', function(e) {

                let wrapper = $(this).closest('.service-unit-item');
                let preview = wrapper.find('.preview-foto_odometer');

                preview.html(''); // reset

                let file = e.target.files[0];

                if (file) {
                    let reader = new FileReader();

                    reader.onload = function(e) {
                        preview.html(`
                <img src="${e.target.result}"
                    class="w-32 h-32 object-cover rounded border">
            `);
                    }

                    reader.readAsDataURL(file);
                }
            });
            // Preview foto kondisi -> multiple
            $(document).on('change', '.input-foto-kondisi', function(e) {

                let wrapper = $(this).closest('.service-unit-item');
                let preview = wrapper.find('.preview-foto-kondisi');

                preview.html(''); // reset

                let files = e.target.files;

                if (files.length > 0) {

                    Array.from(files).forEach(file => {

                        let reader = new FileReader();

                        reader.onload = function(e) {
                            preview.append(`
                    <div class="relative">
                        <img src="${e.target.result}"
                            class="w-24 h-24 object-cover rounded border">
                    </div>
                `);
                        }

                        reader.readAsDataURL(file);

                    });

                }
            });
        });
    </script>
</body>

</html>
