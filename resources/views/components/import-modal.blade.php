@props([
    'name' => 'import-modal',
    'type',
    'templateType',
    'title' => 'Import Data',
    'description' => 'Download template Excel, isi data sesuai format, lalu upload file di bawah ini.'
])

<x-modal :name="$name" focusable>
    <div class="p-6">
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                {{ $title }}
            </h3>
            <button type="button" x-on:click="$dispatch('close-modal', '{{ $name }}')" class="text-gray-400 hover:text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="mt-4">
            <p class="text-sm text-gray-600 mb-4">{{ $description }}</p>

            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-gray-700 block">Belum punya template?</span>
                    <span class="text-xs text-gray-500">Gunakan template resmi agar format data sesuai.</span>
                </div>
                <a href="{{ route('templates.download', $templateType) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-primary-500 text-primary-700 hover:bg-primary-50 rounded-lg text-xs font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Template
                </a>
            </div>

            <form action="{{ route('import.preview') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="import_type" value="{{ $type }}">

                <div class="space-y-4">
                    <div>
                        <x-input-label for="file_{{ $name }}" :value="__('Pilih File Excel / CSV')" />
                        <input type="file"
                               name="file"
                               id="file_{{ $name }}"
                               accept=".xlsx,.xls,.csv"
                               required
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-700 file:text-white hover:file:bg-primary-800 border border-gray-300 rounded-md shadow-sm" />
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <x-secondary-button type="button" x-on:click="$dispatch('close-modal', '{{ $name }}')">
                            {{ __('Batal') }}
                        </x-secondary-button>

                        <x-primary-button class="bg-primary-700 hover:bg-primary-800">
                            {{ __('Preview & Import') }}
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-modal>
