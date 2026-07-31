<!-- STEP 1: Informasi BASTK -->
<div x-show="step === 1" x-cloak>
    <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b-2 border-blue-500">
        Step 1: Informasi BASTK (Induk)
    </h2>
    <p class="text-sm text-gray-500 mb-6">Data Utama Berita Acara Serah Terima Kendaraan</p>

    <!-- User Selection (created_by) -->
    <div class="mb-6">
        <label for="created_by" class="block text-sm font-medium text-gray-700 mb-2">
            Dibuat Oleh (Pilih User) <span class="text-red-500">*</span>
        </label>
        <select name="created_by" id="created_by" class="w-full" required>
            <option value="">-- Pilih User --</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ old('created_by') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
        @error('created_by')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Jenis BASTK -->
        <div>
            <label for="type_bastk" class="block text-sm font-medium text-gray-700 mb-2">
                Jenis BASTK <span class="text-red-500">*</span>
            </label>
            <select name="type_bastk" id="type_bastk" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="serah" {{ old('type_bastk') == 'serah' ? 'selected' : '' }}>Penyerahan</option>
                <option value="terima" {{ old('type_bastk') == 'terima' ? 'selected' : '' }}>Pengambilan</option>
            </select>
            @error('type_bastk')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Kode BASTK -->
        <div>
            <label for="kode" class="block text-sm font-medium text-gray-700 mb-2">
                Kode BASTK <span class="text-red-500">*</span>
            </label>
            <select name="kode" id="kode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">-- Pilih Kode --</option>
                @foreach($kode as $key => $val)
                    <option value="{{ $key }}" {{ old('kode') == $key ? 'selected' : '' }}>{{ $val }}</option>
                @endforeach
            </select>
            @error('kode')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Pilih Unit -->
        <div>
            <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-2">
                Pilih Unit <span class="text-red-500">*</span>
            </label>
            <select name="unit_id" id="unit_id" class="w-full" required>
                <option value="">-- Pilih Unit --</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->type }} - {{ $unit->nopol }}
                    </option>
                @endforeach
            </select>
            @error('unit_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <!-- Kepada -->
        <div>
            <label for="kepada" class="block text-sm font-medium text-gray-700 mb-2">
                Kepada <span class="text-red-500">*</span>
            </label>
            <input type="text" name="kepada" id="kepada" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('kepada') }}" required placeholder="Nama penerima/pemakai">
            @error('kepada')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- No HP -->
        <div>
            <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-2">
                No HP
            </label>
            <input type="tel" name="no_hp" id="no_hp" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('no_hp') }}" placeholder="Contoh: 08123456789">
            @error('no_hp')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Alamat -->
    <div class="mb-6">
        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
        <textarea name="alamat" id="alamat" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
        @error('alamat')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <!-- Tanggal Serah -->
        <div>
            <label for="tgl_serah" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Serah</label>
            <input type="date" name="tgl_serah" id="tgl_serah" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('tgl_serah') }}">
            @error('tgl_serah')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tanggal Kembali -->
        <div>
            <label for="tgl_kembali" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kembali</label>
            <input type="date" name="tgl_kembali" id="tgl_kembali" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('tgl_kembali') }}">
            @error('tgl_kembali')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <!-- Nama Penyerah -->
        <div>
            <label for="nama_penyerah" class="block text-sm font-medium text-gray-700 mb-2">Nama Penyerah</label>
            <input type="text" name="nama_penyerah" id="nama_penyerah" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('nama_penyerah') }}">
            @error('nama_penyerah')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nama Penerima -->
        <div>
            <label for="nama_penerima" class="block text-sm font-medium text-gray-700 mb-2">Nama Penerima</label>
            <input type="text" name="nama_penerima" id="nama_penerima" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('nama_penerima') }}">
            @error('nama_penerima')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Kondisi Unit (Checkbox List) -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Kondisi Unit <span class="text-red-500">*</span>
        </label>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 bg-gray-50 p-4 rounded-lg border border-gray-200">
            @foreach($kondisi as $kVal => $kLabel)
                <label class="inline-flex items-center space-x-2 text-sm text-gray-700 cursor-pointer">
                    <input type="checkbox" name="kondisi_unit[]" value="{{ $kVal }}"
                           x-model="kondisiSelected"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <span>{{ $kLabel }}</span>
                </label>
            @endforeach
        </div>
        @error('kondisi_unit')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Exchange (Visible when 'exchange' is checked in kondisi_unit) -->
    <div class="mb-6" x-show="kondisiSelected.includes('exchange')" x-cloak>
        <label for="exchange" class="block text-sm font-medium text-gray-700 mb-2">
            Exchange Detail <span class="text-red-500">*</span>
        </label>
        <textarea name="exchange" id="exchange" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Rincian Exchange unit">{{ old('exchange') }}</textarea>
        @error('exchange')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Step 1 Navigation -->
    <div class="flex justify-end mt-8">
        <button type="button" @click="goToStep(2)" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
            Lanjut: Kelengkapan & Kondisi &rarr;
        </button>
    </div>
</div>
