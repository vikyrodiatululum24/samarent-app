<!-- STEP 3: Dokumentasi Foto -->
<div x-show="step === 3" x-cloak>
    <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b-2 border-blue-500">
        Step 3: Dokumentasi Foto
    </h2>
    <p class="text-sm text-gray-500 mb-6">Upload Foto Fisik Unit dan Dokumen Pendukung</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        @php
            $fotoSingle = [
                'unit_depan' => 'Foto Unit Depan',
                'unit_belakang' => 'Foto Unit Belakang',
                'unit_samping_kanan' => 'Foto Samping Kanan',
                'unit_samping_kiri' => 'Foto Samping Kiri',
                'kabin_depan' => 'Foto Kabin Depan',
                'kabin_tengah' => 'Foto Kabin Tengah',
                'kabin_belakang' => 'Foto Kabin Belakang',
                'dashboard' => 'Foto Dashboard',
                'odometer' => 'Foto Odometer',
                'buku_service' => 'Foto Buku Service',
                'manual_book' => 'Foto Manual Book',
                'ban_serep' => 'Foto Ban Serep',
                'stnk_depan' => 'Foto STNK Depan',
                'stnk_belakang' => 'Foto STNK Belakang',
                'bastk' => 'File Scan BASTK',
            ];
        @endphp

        @foreach($fotoSingle as $field => $label)
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label for="dokumentasi_{{ $field }}" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $label }}
                </label>
                <input type="file" name="dokumentasi[{{ $field }}]" id="dokumentasi_{{ $field }}" accept="image/*"
                       class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white"
                       onchange="previewImage(this, 'preview_{{ $field }}')">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG</p>
                <img id="preview_{{ $field }}" class="preview-image hidden rounded-lg border border-gray-300 mt-2" alt="Preview">
            </div>
        @endforeach
    </div>

    <!-- Multiple Files: Kerusakan & Tools -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <label for="dokumentasi_kerusakan" class="block text-sm font-medium text-gray-700 mb-2">
                Foto Kerusakan (Bisa Banyak)
            </label>
            <input type="file" name="dokumentasi[kerusakan][]" id="dokumentasi_kerusakan" accept="image/*" multiple
                   class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
            <p class="text-xs text-gray-500 mt-1">Dapat memilih lebih dari 1 file</p>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <label for="dokumentasi_tools" class="block text-sm font-medium text-gray-700 mb-2">
                Foto Tools / Peralatan (Bisa Banyak)
            </label>
            <input type="file" name="dokumentasi[tools][]" id="dokumentasi_tools" accept="image/*" multiple
                   class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
            <p class="text-xs text-gray-500 mt-1">Dapat memilih lebih dari 1 file</p>
        </div>
    </div>

    <!-- Step 3 Navigation & Submit -->
    <div class="flex justify-between items-center mt-8">
        <button type="button" @click="goToStep(2)" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200 font-medium">
            &larr; Kembali ke Kelengkapan & Kondisi
        </button>
        <div class="flex gap-4">
            <button type="reset" class="px-6 py-3 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition duration-200 font-medium">
                Reset Form
            </button>
            <button type="submit" id="submitBtn" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                Simpan BASTK
            </button>
        </div>
    </div>
</div>
