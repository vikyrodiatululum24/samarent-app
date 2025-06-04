<div x-data="{ showSidebar: false }" class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside 
        x-show="showSidebar" 
        class="w-64 bg-white border-r shadow-md transition-all"
    >
        {{-- <x-filament::layouts.app.sidebar /> --}}
    </aside>

    {{-- Main Content --}}
    <div class="flex-1">
        {{-- Toggle Button --}}
        <div class="p-4">
            <button 
                @click="showSidebar = !showSidebar" 
                class="px-4 py-2 bg-gray-800 text-white rounded"
            >
                Toggle Sidebar
            </button>
        </div>

        <div class="p-4">
            {{ $slot }}
        </div>

        {{-- Custom Footer --}}
        <footer class="text-center text-sm text-gray-500 mt-8 mb-4">
            &copy; {{ date('Y') }} PT. Samana Jaya Propertindo.
        </footer>
    </div>
</div>
