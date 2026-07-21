<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-page-header title="Detail Tipe Mahasiswa" />

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <dt class="text-sm font-medium text-gray-500">Kode</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold font-mono">{{ $studentType->kode }}</dd>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $studentType->deskripsi }}</dd>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @switch($studentType->status)
                                @case('Freshman')
                                    <x-badge type="info">{{ $studentType->status }}</x-badge>
                                    @break
                                @case('Continuing')
                                    <x-badge type="warning">{{ $studentType->status }}</x-badge>
                                    @break
                                @case('Graduated')
                                    <x-badge type="neutral">{{ $studentType->status }}</x-badge>
                                    @break
                                @default
                                    <x-badge type="primary">{{ $studentType->status }}</x-badge>
                            @endswitch
                        </dd>
                    </div>
                </div>

                <div class="pt-4">
                    <a href="{{ route('master-data.student-type.index') }}" class="border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg px-4 py-2 text-sm font-medium transition">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
