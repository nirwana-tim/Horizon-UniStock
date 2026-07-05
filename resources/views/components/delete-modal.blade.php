@props([
    'route',
    'label' => 'Hapus Data',
    'description' => 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
])

<div x-data="{ open: false }">
    {{-- Trigger --}}
    <button type="button" @click="open = true"
        class="inline-flex items-center px-2.5 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
        {{ __('Hapus') }}
    </button>

    {{-- Modal --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">

        {{-- Overlay --}}
        <div @click="open = false" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

        {{-- Panel --}}
        <div class="relative bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4"
             @click.outside="open = false">
            {{-- Icon --}}
            <div class="mx-auto w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>

            {{-- Text --}}
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">{{ $label }}</h3>
            <p class="text-sm text-gray-500 text-center mb-6">{{ $description }}</p>

            {{-- Actions --}}
            <div class="flex gap-3 justify-center">
                <form action="{{ $route }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Ya, Hapus') }}
                    </button>
                </form>
                <button type="button" @click="open = false"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Batal') }}
                </button>
            </div>
        </div>
    </div>
</div>