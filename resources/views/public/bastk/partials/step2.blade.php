<!-- STEP 2: Kelengkapan & Kondisi Unit -->
<div x-show="step === 2" x-cloak>
    <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b-2 border-blue-500">
        Step 2: Kelengkapan & Kondisi
    </h2>
    <p class="text-sm text-gray-500 mb-6">Checklist Kelengkapan dan Status Unit</p>

    <div class="overflow-x-auto mb-6">
        <table class="w-full text-left text-sm border-collapse border border-gray-200">
            <thead>
                <tr class="bg-gray-100 text-gray-700 border-b border-gray-200">
                    <th class="p-3 border-r border-gray-200 font-semibold w-1/3">Kelengkapan</th>
                    <th class="p-3 font-semibold text-center">Status / Pilihan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kelengkapan as $index => $item)
                    <tr class="border-b border-gray-200 hover:bg-gray-50" x-data="itemRow('{{ $item }}')">
                        <td class="p-3 font-medium text-gray-800 border-r border-gray-200">
                            {{ $item }}
                            <input type="hidden" name="items[{{ $index }}][kelengkapan]" value="{{ $item }}">
                        </td>
                        <td class="p-3">
                            @if($item === 'BBM & KM')
                                <!-- Special layout for BBM & KM -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis BBM</label>
                                        <select name="items[{{ $index }}][jenis_bbm]" class="w-full text-xs px-2 py-1.5 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                            <option value="">-- Jenis BBM --</option>
                                            <option value="Pertalite">Pertalite</option>
                                            <option value="Pertamax">Pertamax</option>
                                            <option value="Solar">Solar</option>
                                            <option value="Dexlite">Dexlite</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">BBM (Bar)</label>
                                        <select name="items[{{ $index }}][bbm]" x-model="bbmBars" class="w-full text-xs px-2 py-1.5 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                            <option value="">-- Pilih Bar --</option>
                                            @for($b = 1; $b <= 8; $b++)
                                                <option value="{{ $b }}">{{ $b }} Bar</option>
                                            @endfor
                                        </select>
                                        <!-- Visual Bar Indicator -->
                                        <div class="flex items-end gap-1 mt-2" x-show="bbmBars > 0">
                                            <span class="text-[10px] text-gray-500 font-bold">E</span>
                                            <template x-for="i in 8">
                                                <div class="w-3 rounded-xs border transition-all duration-150"
                                                     :style="'height: ' + (12 + i*2) + 'px;'"
                                                     :class="i <= bbmBars ? 'bg-green-500 border-green-600' : 'bg-gray-200 border-gray-300'"></div>
                                            </template>
                                            <span class="text-[10px] text-gray-500 font-bold">F</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">KM Kendaraan</label>
                                        <div class="flex items-center">
                                            <input type="number" name="items[{{ $index }}][km]" class="w-full text-xs px-2 py-1.5 border border-gray-300 rounded-l focus:ring-1 focus:ring-blue-500" placeholder="Contoh: 12345">
                                            <span class="bg-gray-100 text-gray-500 text-xs px-2 py-1.5 border border-l-0 border-gray-300 rounded-r">km</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Standard Checkbox Options -->
                                <div class="flex flex-wrap items-center justify-around gap-4">
                                    <!-- Checkbox Baik / Original / Ada -->
                                    <label class="inline-flex items-center space-x-1 cursor-pointer">
                                        <input type="checkbox" name="items[{{ $index }}][baik]" value="1"
                                               x-model="baik" @change="onBaikChange()"
                                               class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <span class="text-xs text-gray-700">
                                            @if($item === 'Velg Ban') Original @elseif(in_array($item, ['Tutup Dop', 'Apar'])) Ada @else Baik @endif
                                        </span>
                                    </label>

                                    <!-- Checkbox Rusak / Racing -->
                                    @if(!in_array($item, ['Tutup Dop', 'Apar']))
                                    <label class="inline-flex items-center space-x-1 cursor-pointer">
                                        <input type="checkbox" name="items[{{ $index }}][rusak]" value="1"
                                               x-model="rusak" @change="onRusakChange()"
                                               class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                        <span class="text-xs text-gray-700">
                                            @if($item === 'Velg Ban') Racing @else Rusak @endif
                                        </span>
                                    </label>
                                    @endif

                                    <!-- Checkbox Tidak Ada -->
                                    @if($item !== 'Velg Ban')
                                    <label class="inline-flex items-center space-x-1 cursor-pointer">
                                        <input type="checkbox" name="items[{{ $index }}][tidak_ada]" value="1"
                                               x-model="tidakAda" @change="onTidakAdaChange()"
                                               class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        <span class="text-xs text-gray-700">Tidak Ada</span>
                                    </label>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Keterangan Tambahan Step 2 -->
    <div class="mb-6">
        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan Catatan Kelengkapan</label>
        <textarea name="keterangan" id="keterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Catatan tambahan mengenai kondisi fisik unit...">{{ old('keterangan') }}</textarea>
    </div>

    <!-- Step 2 Navigation -->
    <div class="flex justify-between mt-8">
        <button type="button" @click="goToStep(1)" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200 font-medium">
            &larr; Kembali ke Informasi BASTK
        </button>
        <button type="button" @click="goToStep(3)" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
            Lanjut: Dokumentasi Foto &rarr;
        </button>
    </div>
</div>
