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

        .file-dropzone {
            position: relative;
            display: block;
            width: 100%;
            border: 2px dashed #94a3b8;
            border-radius: 0.75rem;
            background: #f8fafc;
            padding: 1rem;
            text-align: center;
            color: #334155;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .file-dropzone:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .file-dropzone.is-dragover {
            border-color: #2563eb;
            background: #dbeafe;
        }

        .file-dropzone input[type="file"] {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }

        .dropzone-title {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .dropzone-subtitle {
            display: block;
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.15rem;
        }

        .dropzone-filename {
            display: block;
            margin-top: 0.45rem;
            font-size: 0.75rem;
            color: #1e40af;
            word-break: break-word;
        }

        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .preview-item {
            position: relative;
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            overflow: hidden;
            background: #fff;
        }

        .preview-remove-btn {
            position: absolute;
            top: 0.3rem;
            right: 0.3rem;
            width: 1.3rem;
            height: 1.3rem;
            border: 0;
            border-radius: 999px;
            background: rgba(239, 68, 68, 0.92);
            color: #fff;
            font-weight: 700;
            line-height: 1;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .preview-item img {
            width: 100%;
            height: 90px;
            object-fit: cover;
            display: block;
            background: #e2e8f0;
        }

        .preview-caption {
            display: block;
            padding: 0.35rem 0.45rem;
            font-size: 0.7rem;
            color: #334155;
            word-break: break-word;
            line-height: 1.25;
        }

        .preview-empty {
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: #64748b;
        }
    </style>
</head>

<body class="bg-slate-100">
    {{-- navbar --}}
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/logo_samarent.png') }}" alt="Logo Samarent" class="h-10 w-auto">
                <span class="text-lg font-semibold text-slate-800">Samarent</span>
            </div>

            {{-- tombol logout --}}
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg flex items-center space-x-2">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 3H8C6.89543 3 6 3.89543 6 5V19C6 20.1046 6.89543 21 8 21H14" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M10 12H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M18 9L21 12L18 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>

    {{-- Form Section --}}
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm mb-6">
                <img src="{{ asset('images/header_samarent.jpg') }}" alt="header samarent" width="100%"
                    style="border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;">
                <div class="pb-6">
                    <h1 class="text-2xl font-bold text-slate-800 text-center">Form Dokumentasi</h1>
                    <p class="text-sm text-slate-500 mt-2 text-center">Silahkan upload gambar dokumentasi</p>
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

            <div class="bg-white rounded-xl shadow-sm p-6">
                <form id="mekanik-form" action="{{ route('mekanik.store') }}" method="POST" class="space-y-5"
                    enctype="multipart/form-data">
                    @csrf

                    <div>
                        <label for="no-spk" class="block text-sm font-medium text-slate-700 mb-1">No. SPK</label>
                        <select name="no-spk" id="no-spk" required
                            class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih No. SPK</option>
                            @foreach ($pengajuans as $pengajuan)
                                <option value="{{ $pengajuan->id }}"
                                    {{ old('no-spk') == $pengajuan->id ? 'selected' : '' }}>
                                    {{ $pengajuan->no_pengajuan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <h3 class="font-bold text-lg mb-2">Foto Unit Service</h3>

                    <div id="service-units-wrapper">
                        <div class="border border-gray-300 rounded-lg p-2 mb-3">
                            <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.5" class="w-14 h-14 mb-3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 7.5V6.75A2.25 2.25 0 0 1 5.25 4.5h13.5A2.25 2.25 0 0 1 21 6.75v10.5A2.25 2.25 0 0 1 18.75 19.5H5.25A2.25 2.25 0 0 1 3 17.25V7.5Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m7.5 15 2.25-2.25 2.25 2.25 4.5-4.5" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7.5 8.25h.008v.008H7.5V8.25Z" />
                                </svg>
                                <p class="text-sm text-center">Silahkan pilih No. SPK terlebih dahulu untuk menampilkan
                                    unit service.</p>
                            </div>

                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors">
                            Simpan Dokumentasi
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div id="delete-photo-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 px-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-slate-800">Hapus foto?</h2>
                <p class="mt-2 text-sm text-slate-600">
                    Foto ini akan dihapus secara permanen dari daftar.
                </p>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" id="cancel-delete-photo"
                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" id="confirm-delete-photo"
                    class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                    Hapus
                </button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            initSelect2();

            $('#no-spk').select2({
                placeholder: 'Pilih No. SPK',
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

            function updateDropzoneFileName(input) {
                let fileCount = input.files ? input.files.length : 0;
                let fileNameText = 'Belum ada file dipilih';

                if (fileCount > 1) {
                    fileNameText = `${fileCount} file dipilih`;
                } else if (fileCount === 1) {
                    fileNameText = input.files[0].name;
                }

                $(input).closest('.file-dropzone').find('.dropzone-filename').text(fileNameText);
            }

            function bindDropzoneEvents(target = null) {
                let dropzones = target ? target.find('.file-dropzone') : $('.file-dropzone');

                dropzones.each(function() {
                    let zone = $(this);

                    if (zone.data('dnd-bound')) {
                        return;
                    }

                    zone.data('dnd-bound', true);

                    zone.on('dragenter dragover', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        zone.addClass('is-dragover');
                    });

                    // Make the whole dropzone open native file picker.
                    zone.on('click', function(e) {
                        if ($(e.target).closest('a, input, button').length) {
                            return;
                        }

                        let input = zone.find('input[type="file"]')[0];
                        if (input) {
                            input.click();
                        }
                    });

                    zone.on('dragleave dragend drop', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        zone.removeClass('is-dragover');
                    });

                    zone.on('drop', function(e) {
                        let input = zone.find('input[type="file"]')[0];
                        let files = e.originalEvent.dataTransfer.files;

                        if (!input || !files || files.length === 0) {
                            return;
                        }

                        let dataTransfer = new DataTransfer();

                        if (input.multiple) {
                            Array.from(files).forEach(file => dataTransfer.items.add(file));
                        } else {
                            dataTransfer.items.add(files[0]);
                        }

                        input.files = dataTransfer.files;
                        $(input).trigger('change');
                    });
                });
            }

            function escapeHtml(value) {
                if (value === null || value === undefined) return '';
                return String(value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function normalizePhotos(value) {
                if (!value) return [];
                if (Array.isArray(value)) return value.filter(Boolean);
                return [value];
            }

            function normalizeStoragePath(path) {
                if (!path) return '';

                return String(path)
                    .replace(/\\/g, '/')
                    .replace(/^https?:\/\/[^/]+/i, '')
                    .replace(/^\/+/, '')
                    .replace(/^public\//, '')
                    .replace(/^storage\//, '');
            }

            function fileUrl(path) {
                if (!path) return '#';

                const normalized = normalizeStoragePath(path);
                if (!normalized) return '#';

                const basePath = `{{ request()->getBaseUrl() }}`;
                const relativePath = `${basePath}/storage/${normalized}`.replace(/\/{2,}/g, '/');

                return `${window.location.origin}${relativePath}`;
            }

            function renderExistingFiles(title, files, fieldKey = '') {
                let normalized = normalizePhotos(files);

                if (normalized.length === 0) {
                    return `
                        <div class="preview-empty">
                            ${title}: belum ada file tersimpan.
                        </div>
                    `;
                }

                let items = normalized.map((path, idx) => {
                    let safePath = escapeHtml(path);
                    let url = escapeHtml(fileUrl(path));
                    let fileName = escapeHtml(String(path).split('/').pop() || `foto-${idx + 1}`);
                    return `
                        <div class="preview-item" data-existing-path="${safePath}" data-field-key="${escapeHtml(fieldKey)}">
                            <a href="${url}" target="_blank" title="${safePath}">
                                <img src="${url}" alt="${title} ${idx + 1}">
                                <span class="preview-caption">${fileName}</span>
                            </a>
                            <button type="button" class="preview-remove-btn remove-existing-photo" title="Hapus foto">&times;</button>
                        </div>
                    `;
                }).join('');

                return `
                    <div class="mb-2">
                        <p class="text-xs font-semibold text-slate-600 mb-1">${title} (existing)</p>
                        <div class="preview-grid">${items}</div>
                    </div>
                `;
            }

            function renderUnitDetails(unit) {
                const unitData = unit?.unit || {};
                const details = [{
                        label: 'Merk',
                        value: unitData.merk
                    },
                    {
                        label: 'Type',
                        value: unitData.type
                    },
                    {
                        label: 'Nopol',
                        value: unitData.nopol
                    },
                ];

                const hasAnyDetail = details.some((detail) => detail.value !== null && detail.value !== undefined &&
                    detail.value !== '');

                if (!hasAnyDetail) {
                    return `
                        <div class="mb-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Detail Unit</p>
                            <p class="mt-2 text-sm text-slate-500">Detail unit tidak tersedia.</p>
                        </div>
                    `;
                }

                const items = details.map((detail) => {
                    const value = detail.value === null || detail.value === undefined || detail.value ===
                        '' ?
                        '-' :
                        escapeHtml(String(detail.value));

                    return `
                        <div>
                            <span class="block text-xs font-medium uppercase tracking-wide text-slate-500">${escapeHtml(detail.label)}</span>
                            <span class="block text-sm font-semibold text-slate-800">${value}</span>
                        </div>
                    `;
                }).join('');

                return `
                    <div class="mb-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">${items}</div>
                    </div>
                `;
            }

            function renderSelectedFilesPreview(input) {
                let previewContainer = $(input).closest('.file-dropzone').find('.preview-selected');

                if (!previewContainer.length) {
                    return;
                }

                if (!input.files || input.files.length === 0) {
                    previewContainer.html('');
                    return;
                }

                let html = '<p class="text-xs font-semibold text-slate-600 mt-2 mb-1">Preview file terpilih</p>';
                html += '<div class="preview-grid">';

                Array.from(input.files).forEach((file) => {
                    let objectUrl = URL.createObjectURL(file);
                    let safeName = escapeHtml(file.name);
                    html += `
                        <div class="preview-item" data-selected-file-name="${safeName}">
                            <img src="${objectUrl}" alt="${safeName}">
                            <span class="preview-caption">${safeName}</span>
                            <button type="button" class="preview-remove-btn remove-selected-photo" title="Hapus file">&times;</button>
                        </div>
                    `;
                });

                html += '</div>';
                previewContainer.html(html);
            }

            function hydrateExistingInputs() {
                $('.existing-inputs').remove();

                $('#complete-wrapper .preview-item[data-existing-path]').each(function() {
                    const item = $(this);
                    const path = item.data('existing-path');
                    const fieldKey = item.data('field-key');

                    if (!path || fieldKey !== 'foto_nota') return;

                    $('#complete-wrapper').prepend(
                        `<input type="hidden" class="existing-inputs" name="complete[existing_foto_nota][]" value="${escapeHtml(path)}">`
                    );
                });

                $('.service-unit-item').each(function() {
                    const item = $(this);
                    const idxMatch = item.find('input[name^="service_units["][name$="[id]"]').attr('name')
                        ?.match(/service_units\[(\d+)\]/);
                    const idx = idxMatch ? idxMatch[1] : null;

                    if (idx === null) return;

                    item.find('.preview-item[data-existing-path]').each(function() {
                        const preview = $(this);
                        const path = preview.data('existing-path');
                        const fieldKey = preview.data('field-key');

                        if (!path || !fieldKey) return;

                        if (fieldKey === 'foto_kondisi') {
                            item.prepend(
                                `<input type="hidden" class="existing-inputs" name="service_units[${idx}][existing_foto_kondisi][]" value="${escapeHtml(path)}">`
                                );
                        }

                        if (fieldKey === 'foto_tambahan') {
                            item.prepend(
                                `<input type="hidden" class="existing-inputs" name="service_units[${idx}][existing_foto_tambahan][]" value="${escapeHtml(path)}">`
                                );
                        }
                    });
                });
            }

            function enforceMaxFiles(input) {
                const maxFiles = parseInt($(input).data('max-files') || 0, 10);
                if (!maxFiles || !input.files) return true;

                if (input.files.length > maxFiles) {
                    alert(`Maksimal ${maxFiles} foto untuk field ini.`);
                    input.value = '';
                    updateDropzoneFileName(input);
                    renderSelectedFilesPreview(input);
                    return false;
                }

                return true;
            }

            let deletePhotoTarget = null;

            function openDeletePhotoModal(target) {
                deletePhotoTarget = target;
                $('#delete-photo-modal').removeClass('hidden').addClass('flex');
            }

            function closeDeletePhotoModal() {
                deletePhotoTarget = null;
                $('#delete-photo-modal').addClass('hidden').removeClass('flex');
            }

            function resetDynamicSections() {
                $('#complete-wrapper').html(`
                    <div class="border border-gray-300 rounded-lg p-4 mb-3 bg-slate-50">
                        <p class="text-sm text-slate-500">Data nota akan tampil setelah No. SPK dipilih.</p>
                    </div>
                `);

                $('#service-units-wrapper').html(`
                    <div class="border border-gray-300 rounded-lg p-2 mb-3">
                        <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5" class="w-14 h-14 mb-3">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 7.5V6.75A2.25 2.25 0 0 1 5.25 4.5h13.5A2.25 2.25 0 0 1 21 6.75v10.5A2.25 2.25 0 0 1 18.75 19.5H5.25A2.25 2.25 0 0 1 3 17.25V7.5Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m7.5 15 2.25-2.25 2.25 2.25 4.5-4.5" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7.5 8.25h.008v.008H7.5V8.25Z" />
                            </svg>
                            <p class="text-sm text-center">Silahkan pilih No. SPK terlebih dahulu untuk menampilkan unit service.</p>
                        </div>
                    </div>
                `);
            }

            $('#no-spk').on('change', function() {
                let selectedId = $(this).val();

                if (selectedId) {
                    $('#mekanik-form').attr('action', `{{ route('mekanik.update', ['id' => '__ID__']) }}`
                        .replace('__ID__', selectedId));
                } else {
                    $('#mekanik-form').attr('action', `{{ route('mekanik.store') }}`);
                }

                if (!selectedId) {
                    resetDynamicSections();
                    return;
                }

                $.ajax({
                    url: `{{ route('mekanik.pengajuan', ['id' => '__ID__']) }}`.replace('__ID__',
                        selectedId),
                    method: 'GET',
                    success: function(response) {
                        let complete = response.complete;
                        let serviceUnits = response.service_units;
                        let completeHtml = '';
                        let html = '';

                        $('#complete-wrapper').html(completeHtml);

                        if (serviceUnits.length > 0) {
                            serviceUnits.forEach((unit, idx) => {
                                html += `
                                <div class="service-unit-item border border-gray-300 p-4 rounded-sm mb-3">
                                    <input type="hidden" name="service_units[${idx}][id]" value="${unit.id}">
                                    ${renderUnitDetails(unit)}
                                    <div class="grid gap-3 md:grid-cols-2">
                                        <div class="mb-3">
                                            ${renderExistingFiles('Foto unit', unit.foto_unit, 'foto_unit')}
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Upload / Ganti Foto Unit</label>
                                            <div class="file-dropzone">
                                                <span class="dropzone-title">Klik atau seret file ke sini</span>
                                                <span class="dropzone-subtitle">Format: JPG, PNG. Maksimal 5MB.</span>
                                                <input type="file" name="service_units[${idx}][foto_unit]" accept=".jpg,.jpeg,.png">
                                                <span class="dropzone-filename">Belum ada file dipilih</span>
                                                <div class="preview-selected"></div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            ${renderExistingFiles('Foto odometer', unit.foto_odometer, 'foto_odometer')}
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Upload / Ganti Foto Odometer</label>
                                            <div class="file-dropzone">
                                                <span class="dropzone-title">Klik atau seret file ke sini</span>
                                                <span class="dropzone-subtitle">Format: JPG, PNG. Maksimal 5MB.</span>
                                                <input type="file" name="service_units[${idx}][foto_odometer]" accept=".jpg,.jpeg,.png">
                                                <span class="dropzone-filename">Belum ada file dipilih</span>
                                                <div class="preview-selected"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid gap-3 md:grid-cols-2">
                                        <div class="mb-3">
                                            ${renderExistingFiles('Foto kondisi', unit.foto_kondisi, 'foto_kondisi')}
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Upload / Ganti Foto Kondisi</label>
                                            <div class="file-dropzone">
                                                <span class="dropzone-title">Klik atau seret file ke sini</span>
                                                <span class="dropzone-subtitle">Format: JPG, PNG. Maksimal 5MB. Maks 3 foto.</span>
                                                <input type="file" name="service_units[${idx}][foto_kondisi][]" multiple accept=".jpg,.jpeg,.png" data-max-files="3">
                                                <span class="dropzone-filename">Belum ada file dipilih</span>
                                                <div class="preview-selected"></div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            ${renderExistingFiles('Foto pengerjaan bengkel', unit.foto_pengerjaan_bengkel, 'foto_pengerjaan_bengkel')}
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Upload / Ganti Foto Pengerjaan Bengkel</label>
                                            <div class="file-dropzone">
                                                <span class="dropzone-title">Klik atau seret file ke sini</span>
                                                <span class="dropzone-subtitle">Format: JPG, PNG. Maksimal 5MB.</span>
                                                <input type="file" name="service_units[${idx}][foto_pengerjaan_bengkel]" accept=".jpg,.jpeg,.png">
                                                <span class="dropzone-filename">Belum ada file dipilih</span>
                                                <div class="preview-selected"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        ${renderExistingFiles('Foto tambahan', unit.foto_tambahan, 'foto_tambahan')}
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Upload / Ganti Foto Tambahan</label>
                                        <div class="file-dropzone">
                                            <span class="dropzone-title">Klik atau seret file ke sini</span>
                                            <span class="dropzone-subtitle">Format: JPG, PNG. Maksimal 5MB. Maks 3 foto.</span>
                                            <input type="file" name="service_units[${idx}][foto_tambahan][]" multiple accept=".jpg,.jpeg,.png" data-max-files="3">
                                            <span class="dropzone-filename">Belum ada file dipilih</span>
                                            <div class="preview-selected"></div>
                                        </div>
                                    </div>
                                </div>`;
                            });
                        } else {
                            html = `
                            <div class="border border-gray-300 rounded-lg p-2 mb-3">
                                <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="1.5" class="w-14 h-14 mb-3">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7.5V6.75A2.25 2.25 0 0 1 5.25 4.5h13.5A2.25 2.25 0 0 1 21 6.75v10.5A2.25 2.25 0 0 1 18.75 19.5H5.25A2.25 2.25 0 0 1 3 17.25V7.5Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m7.5 15 2.25-2.25 2.25 2.25 4.5-4.5" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.5 8.25h.008v.008H7.5V8.25Z" />
                                    </svg>
                                    <p class="text-sm text-center">Tidak ada unit service untuk No. SPK ini.</p>
                                </div>
                            </div>`;
                        }

                        $('#service-units-wrapper').html(html);
                        hydrateExistingInputs();
                        bindDropzoneEvents();
                    },
                    error: function(xhr) {
                        console.error('Fetch pengajuan error:', xhr.$responseJSON || xhr
                            .responseText);
                        resetDynamicSections();
                        $('#service-units-wrapper').html(`
                            <div class="border border-red-300 bg-red-50 rounded-lg p-4 mb-3">
                                <p class="text-sm text-red-700">Gagal mengambil data pengajuan. Silakan coba lagi.</p>
                            </div>
                        `);
                    }
                });
            });

            $(document).on('change', '.file-dropzone input[type="file"]', function() {
                if (!enforceMaxFiles(this)) {
                    return;
                }
                updateDropzoneFileName(this);
                renderSelectedFilesPreview(this);
            });

            $(document).on('click', '.remove-existing-photo', function(e) {
                e.preventDefault();

                openDeletePhotoModal($(this).closest('.preview-item'));
            });

            $(document).on('click', '#cancel-delete-photo', function() {
                closeDeletePhotoModal();
            });

            $(document).on('click', '#delete-photo-modal', function(e) {
                if (e.target === this) {
                    closeDeletePhotoModal();
                }
            });

            $(document).on('click', '#confirm-delete-photo', function() {
                if (!deletePhotoTarget || !deletePhotoTarget.length) {
                    closeDeletePhotoModal();
                    return;
                }

                deletePhotoTarget.remove();
                hydrateExistingInputs();
                closeDeletePhotoModal();
            });

            $(document).on('click', '.remove-selected-photo', function(e) {
                e.preventDefault();

                const previewItem = $(this).closest('.preview-item');
                const fileName = previewItem.data('selected-file-name');
                const input = previewItem.closest('.file-dropzone').find('input[type="file"]')[0];

                if (!input || !input.files) {
                    return;
                }

                const dataTransfer = new DataTransfer();
                Array.from(input.files).forEach((file) => {
                    if (file.name !== fileName) {
                        dataTransfer.items.add(file);
                    }
                });

                input.files = dataTransfer.files;
                $(input).trigger('change');
            });

            bindDropzoneEvents();
        });
    </script>
</body>

</html>
