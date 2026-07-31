<!-- Wizard Progress Steps -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center justify-between">
        <!-- Step 1 Indicator -->
        <div class="flex-1 text-center cursor-pointer" @click="goToStep(1)">
            <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center font-bold transition-colors duration-200"
                 :class="step === 1 ? 'bg-blue-600 text-white' : (step > 1 ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-600')">
                <template x-if="step > 1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </template>
                <template x-if="step <= 1">
                    <span>1</span>
                </template>
            </div>
            <p class="text-xs md:text-sm font-semibold mt-2" :class="step === 1 ? 'text-blue-600' : 'text-gray-500'">Informasi BASTK</p>
        </div>

        <div class="w-1/6 border-t-2" :class="step > 1 ? 'border-green-500' : 'border-gray-200'"></div>

        <!-- Step 2 Indicator -->
        <div class="flex-1 text-center cursor-pointer" @click="goToStep(2)">
            <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center font-bold transition-colors duration-200"
                 :class="step === 2 ? 'bg-blue-600 text-white' : (step > 2 ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-600')">
                <template x-if="step > 2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </template>
                <template x-if="step <= 2">
                    <span>2</span>
                </template>
            </div>
            <p class="text-xs md:text-sm font-semibold mt-2" :class="step === 2 ? 'text-blue-600' : 'text-gray-500'">Kelengkapan & Kondisi</p>
        </div>

        <div class="w-1/6 border-t-2" :class="step > 2 ? 'border-green-500' : 'border-gray-200'"></div>

        <!-- Step 3 Indicator -->
        <div class="flex-1 text-center cursor-pointer" @click="goToStep(3)">
            <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center font-bold transition-colors duration-200"
                 :class="step === 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'">
                <span>3</span>
            </div>
            <p class="text-xs md:text-sm font-semibold mt-2" :class="step === 3 ? 'text-blue-600' : 'text-gray-500'">Dokumentasi Foto</p>
        </div>
    </div>
</div>
