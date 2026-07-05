<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Distribusi Barang') }}</h2>
            <a href="{{ route('distribution.scan.index') }}" class="inline-flex items-center px-4 py-2 border border-primary-500 text-primary-700 hover:bg-primary-50 rounded-lg text-sm font-medium transition ease-in-out duration-150">
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Mahasiswa</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nama</p>
                            <p class="font-medium text-gray-900">{{ $student->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">NIM</p>
                            <p class="font-medium text-gray-900">{{ $student->nim }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Program Studi</p>
                            <p class="font-medium text-gray-900">{{ $student->studyProgram->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Level / Angkatan</p>
                            <p class="font-medium text-gray-900">{{ $student->programLevel->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Jenis Mahasiswa</p>
                            <p class="font-medium text-gray-900">{{ ucfirst($student->student_type) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email Kampus</p>
                            <p class="font-medium text-gray-900">{{ $student->email_kampus ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(!$activeSchedule)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-gray-500">Tidak ada jadwal distribusi aktif untuk hari ini.</p>
                    </div>
                </div>
            @elseif($eligibility && !$eligibility->is_eligible)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-medium text-red-800">Mahasiswa Belum Memenuhi Syarat</h3>
                        </div>
                        <div class="ml-12">
                            <p class="text-sm text-gray-600 mb-3">
                                Mahasiswa ini <span class="font-semibold text-red-600">belum dapat menerima barang</span> karena belum menyelesaikan pembayaran.
                            </p>
                            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-red-800">Status Pembayaran:</span>
                                    <span class="ml-2 text-sm text-red-600">{{ $eligibility->payment_status ?? 'Belum Diketahui' }}</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mb-4">
                                Mohon arahkan mahasiswa untuk menyelesaikan pembayaran terlebih dahulu. Setelah pembayaran lunas, data akan diperbarui oleh admin.
                            </p>
                            <a href="{{ route('distribution.scan.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-primary-500 text-primary-700 hover:bg-primary-50 rounded-lg text-sm font-medium transition ease-in-out duration-150">
                                {{ __('Kembali ke Scan') }}
                            </a>
                        </div>
                    </div>
                </div>
            @elseif(!$eligibility)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-medium text-yellow-800">Data Kelayakan Belum Tersedia</h3>
                        </div>
                        <div class="ml-12">
                            <p class="text-sm text-gray-600 mb-3">
                                Data kelayakan distribusi untuk mahasiswa ini belum tersedia di sistem. Mohon pastikan data telah diimport oleh admin.
                            </p>
                            <a href="{{ route('distribution.scan.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-primary-500 text-primary-700 hover:bg-primary-50 rounded-lg text-sm font-medium transition ease-in-out duration-150">
                                {{ __('Kembali ke Scan') }}
                            </a>
                        </div>
                    </div>
                </div>
            @elseif(!$entitlement)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-gray-500">Mahasiswa ini tidak memiliki hak barang untuk tahap distribusi saat ini.</p>
                    </div>
                </div>
            @else
                <form action="{{ route('distribution.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <input type="hidden" name="schedule_id" value="{{ $activeSchedule->id }}">

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Item yang Berhak Diterima</h3>
                                <div class="text-sm text-gray-500">
                                    {{ $activeSchedule->stage->name ?? '-' }} | {{ $activeSchedule->date->format('d M Y') }}
                                </div>
                            </div>

                            @if($scheduleItems->isEmpty())
                                <p class="text-gray-500">Tidak ada item untuk jadwal ini.</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Centang
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Item
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Kode
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Ukuran Input
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Ukuran Aktual
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Qty
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Stok
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($scheduleItems as $index => $item)
                                                @php
                                                    $sizeInfo = $studentSizes[$item->id] ?? null;
                                                    $expectedSize = $sizeInfo['size'] ?? '-';
                                                    $availableStock = $stockInfo[$item->id][$expectedSize] ?? 0;
                                                @endphp
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <input type="checkbox"
                                                            name="items[{{ $index }}][item_id]"
                                                            value="{{ $item->id }}"
                                                            class="item-check rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                                            data-index="{{ $index }}">
                                                        <input type="hidden" name="items[{{ $index }}][expected_size]" value="{{ $expectedSize }}">
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-500">{{ $item->code }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">{{ $expectedSize }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <select name="items[{{ $index }}][actual_size]"
                                                            class="block w-24 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm item-size"
                                                            data-index="{{ $index }}">
                                                            @if(strpos($expectedSize, '3') === 0 || in_array((int)$expectedSize, range(38, 46)))
                                                                @foreach(range(38, 46) as $size)
                                                                    <option value="{{ $size }}" {{ $expectedSize == $size ? 'selected' : '' }}>{{ $size }}</option>
                                                                @endforeach
                                                            @else
                                                                @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $size)
                                                                    <option value="{{ $size }}" {{ $expectedSize == $size ? 'selected' : '' }}>{{ $size }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <input type="number" name="items[{{ $index }}][quantity]"
                                                            value="1" min="1" max="10"
                                                            class="block w-20 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm item-qty"
                                                            data-index="{{ $index }}">
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @php
                                                            $stockQty = $stockInfo[$item->id][$expectedSize] ?? 0;
                                                        @endphp
                                                        <span class="text-sm {{ $stockQty > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                            {{ $stockQty }} pcs
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    @error('items')
                        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-300 text-red-700 rounded-md">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="flex items-center gap-4">
                        <button type="submit" id="submit-btn"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                            {{ __('Konfirmasi Distribusi') }}
                        </button>
                            <a href="{{ route('distribution.scan.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-primary-500 text-primary-700 hover:bg-primary-50 rounded-lg text-sm font-medium transition ease-in-out duration-150">
                            {{ __('Batal') }}
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.item-check');
            const submitBtn = document.getElementById('submit-btn');

            function updateSubmitState() {
                const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                submitBtn.disabled = !anyChecked;
            }

            checkboxes.forEach(function (cb) {
                cb.addEventListener('change', function () {
                    const index = this.dataset.index;
                    const sizeSelect = document.querySelector(`.item-size[data-index="${index}"]`);
                    const qtyInput = document.querySelector(`.item-qty[data-index="${index}"]`);

                    if (sizeSelect) {
                        sizeSelect.disabled = !this.checked;
                    }
                    if (qtyInput) {
                        qtyInput.disabled = !this.checked;
                    }

                    updateSubmitState();
                });

                const index = cb.dataset.index;
                const sizeSelect = document.querySelector(`.item-size[data-index="${index}"]`);
                const qtyInput = document.querySelector(`.item-qty[data-index="${index}"]`);

                if (sizeSelect) sizeSelect.disabled = true;
                if (qtyInput) qtyInput.disabled = true;
            });

            updateSubmitState();
        });
    </script>
    @endpush
</x-app-layout>
