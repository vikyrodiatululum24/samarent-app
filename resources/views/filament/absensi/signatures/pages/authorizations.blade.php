<div class="space-y-6">

    @foreach ($getRecord()->rule_signatures as $rule)
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden">

            {{-- Header Section --}}
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                    {{ $rule->rules }}
                </h3>
            </div>

            {{-- List Signature --}}
            <div class="p-4">
                <div class="grid gap-4 md:grid-cols-3">
                    @forelse ($rule->signatures as $signature)
                        <div
                            class="rounded-lg border p-4 {{ $signature->is_active ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700 dark:bg-gray-800' }}">
                            <div class="grid grid-cols-2">
                                <div>
                                    <div class="flex gap-2 items-center font-medium text-gray-900 dark:text-gray-100">
                                        <span class="{{ $signature->is_active ? 'text-green-500' : 'text-red-500' }}">
                                            {{ $signature->is_active ? '✓' : '✗' }}
                                        </span>
                                        {{ $signature->nama }}
                                    </div>

                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $signature->jabatan }}
                                    </div>

                                </div>
                                @if ($signature->ttd)
                                    <div class="h-24">
                                        <img src="{{ asset('storage/' . $signature->ttd) }}"
                                            alt="{{ $signature->nama }}" class="object-cover h-full w-auto">
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Belum ada data.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    @endforeach
</div>
