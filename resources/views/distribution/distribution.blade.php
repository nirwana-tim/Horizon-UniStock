<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Item Distribution') }}</h2>
            <a href="{{ route('distribution.scan.index') }}" class="inline-flex items-center px-4 py-2 border border-primary-500 text-primary-700 hover:bg-primary-50 rounded-lg text-sm font-medium transition ease-in-out duration-150">
                {{ __('Back') }}
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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Student Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Name</p>
                            <p class="font-medium text-gray-900">{{ $student->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">NIM</p>
                            <p class="font-medium text-gray-900">{{ $student->nim }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Study Program</p>
                            <p class="font-medium text-gray-900">{{ $student->studyProgram->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Level / Batch</p>
                            <p class="font-medium text-gray-900">{{ $student->generation->label ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Student Level</p>
                            <p class="font-medium text-gray-900">{{ $student->student_level_label }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Campus Email</p>
                            <p class="font-medium text-gray-900">{{ $student->email_kampus ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(!$activeSchedule)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-gray-500">No active distribution schedule for today.</p>
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
                            <h3 class="ml-3 text-lg font-medium text-red-800">Student Has Not Met Requirements</h3>
                        </div>
                        <div class="ml-12">
                            <p class="text-sm text-gray-600 mb-3">
                                This student <span class="font-semibold text-red-600">cannot receive goods yet</span> because payment has not been completed.
                            </p>
                            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-red-800">Payment Status:</span>
                                    <span class="ml-2 text-sm text-red-600">{{ $eligibility->payment_status ?? 'Unknown' }}</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mb-4">
                                Please direct the student to complete the payment first. After full payment, the data will be updated by admin.
                            </p>
                            <a href="{{ route('distribution.scan.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-primary-500 text-primary-700 hover:bg-primary-50 rounded-lg text-sm font-medium transition ease-in-out duration-150">
                                {{ __('Back to Scan') }}
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
                            <h3 class="ml-3 text-lg font-medium text-yellow-800">Eligibility Data Not Available</h3>
                        </div>
                        <div class="ml-12">
                            <p class="text-sm text-gray-600 mb-3">
                                Distribution eligibility data for this student is not yet available in the system. Please ensure the data has been imported by admin.
                            </p>
                            <a href="{{ route('distribution.scan.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-primary-500 text-primary-700 hover:bg-primary-50 rounded-lg text-sm font-medium transition ease-in-out duration-150">
                                {{ __('Back to Scan') }}
                            </a>
                        </div>
                    </div>
                </div>
            @elseif(!$student->activeSizeProfile)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-medium text-yellow-800">Student Has Not Entered Sizes</h3>
                        </div>
                        <div class="ml-12">
                            <p class="text-sm text-gray-600 mb-3">
                                This student <span class="font-semibold text-yellow-600">has not filled in their size profile</span> in their account.
                            </p>
                            <p class="text-xs text-gray-500 mb-4">
                                Please direct the student to log in to the system using their NIM and fill in the size input form before picking up items.
                            </p>
                            <a href="{{ route('distribution.scan.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-primary-500 text-primary-700 hover:bg-primary-50 rounded-lg text-sm font-medium transition ease-in-out duration-150">
                                {{ __('Back to Scan') }}
                            </a>
                        </div>
                    </div>
                </div>
            @elseif(!$entitlement)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-gray-500">This student does not have any item entitlement for the current distribution.</p>
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
                                <h3 class="text-lg font-medium text-gray-900">Entitled Items</h3>
                                <div class="text-sm text-gray-500">
                                    {{ $activeSchedule->name }} | {{ $activeSchedule->date?->format('d M Y') ?? '-' }}
                                </div>
                            </div>

                            @if($scheduleItems->isEmpty())
                                <p class="text-gray-500">No items for this schedule.</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Check
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Item
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Code
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Input Size
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Actual Size
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
                                                    $baseCode       = $item->base_code ?? $item->code;
                                                    $sizeInfo       = $studentSizes[$baseCode] ?? null;
                                                    $expectedSize   = $sizeInfo['size'] ?? '-';
                                                    $expectedLabel  = $sizeInfo['size_label'] ?? $expectedSize;
                                                    $availableStock = $stockInfo[$baseCode][$expectedSize] ?? 0;
                                                    $outOfStock     = $availableStock <= 0;
                                                    $takenQty       = $distributedItems[$baseCode] ?? 0;
                                                    $entitledQty    = $entitledQuantities[$baseCode] ?? 0;
                                                    $alreadyTaken   = $entitledQty > 0 && $takenQty >= $entitledQty;
                                                    $isDisabled     = $outOfStock || $alreadyTaken;
                                                @endphp
                                                <tr class="{{ $isDisabled ? 'bg-gray-50 opacity-60' : '' }}">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <input type="checkbox"
                                                            name="items[{{ $index }}][item_id]"
                                                            value="{{ $item->id }}"
                                                            class="item-check rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                                            data-index="{{ $index }}"
                                                            data-stock="{{ $availableStock }}"
                                                            data-item-name="{{ $item->name }}"
                                                            {{ $isDisabled ? 'disabled' : '' }}>
                                                        <input type="hidden" name="items[{{ $index }}][expected_size]" value="{{ $expectedSize }}">
                                                        <input type="hidden" name="items[{{ $index }}][base_code]" value="{{ $baseCode }}">
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                                        @if($alreadyTaken)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700 mt-1">Already Taken</span>
                                                        @elseif($outOfStock)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 mt-1">Out of Stock</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-500">{{ $item->code }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">{{ $expectedLabel }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <select name="items[{{ $index }}][actual_size]"
                                                            class="block w-28 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm item-size"
                                                            data-index="{{ $index }}"
                                                            {{ $isDisabled ? 'disabled' : '' }}>
                                                            @forelse(($variantOptions[$baseCode] ?? $item->variants) as $variant)
                                                                <option value="{{ $variant->size }}" {{ $expectedSize == $variant->size ? 'selected' : '' }}>
                                                                    {{ $variant->size_label }}
                                                                </option>
                                                            @empty
                                                                <option value="{{ $expectedSize }}">{{ $expectedLabel }}</option>
                                                            @endforelse
                                                        </select>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <input type="number" name="items[{{ $index }}][quantity]"
                                                            value="1" min="1" max="{{ max(1, $availableStock) }}"
                                                            class="block w-20 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm item-qty"
                                                            data-index="{{ $index }}"
                                                            {{ $isDisabled ? 'disabled' : '' }}>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @php $stockQty = $availableStock; @endphp
                                                        <span class="text-sm font-medium {{ $stockQty > 0 ? 'text-green-600' : 'text-red-600' }}">
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

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <x-input-label for="notes" :value="__('Distribution Notes (Optional)')" />
                            <textarea id="notes" name="notes" rows="2" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                placeholder="Contoh: Mahasiswa buru-buru, minta ganti ukuran, dll."></textarea>
                            <p class="mt-1 text-xs text-gray-500">Catatan manual ini akan digabungkan dengan log otomatis dari sistem jika terjadi penundaan/kekurangan barang.</p>
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
                            {{ __('Confirm Distribution') }}
                        </button>
                            <a href="{{ route('distribution.scan.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-primary-500 text-primary-700 hover:bg-primary-50 rounded-lg text-sm font-medium transition ease-in-out duration-150">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>

    {{-- Partial Pickup Modal --}}
    <div id="partial-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/40">
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full mx-4 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Stok Tidak Mencukupi</h3>
                    <p class="text-xs text-gray-500">Beberapa barang memiliki stok lebih sedikit dari permintaan.</p>
                </div>
            </div>
            <div class="partial-list bg-gray-50 rounded-lg p-3 mb-4 space-y-1"></div>
            <p class="text-xs text-gray-400 mb-4">Stok akan disesuaikan dengan jumlah yang tersedia. Lanjutkan?</p>
            <div class="flex gap-3">
                <button type="button" id="partial-cancel"
                    class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="button" id="partial-confirm"
                    class="flex-1 px-4 py-2.5 bg-amber-600 text-white rounded-lg text-sm font-medium hover:bg-amber-700 transition-colors">
                    Berikan Sebagian
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.item-check');
            const submitBtn = document.getElementById('submit-btn');
            const form = document.querySelector('form');
            const partialModal = document.getElementById('partial-modal');

            function updateSubmitState() {
                const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                submitBtn.disabled = !anyChecked;
            }

            checkboxes.forEach(function (cb) {
                const index = cb.dataset.index;
                const sizeSelect = document.querySelector(`.item-size[data-index="${index}"]`);
                const qtyInput = document.querySelector(`.item-qty[data-index="${index}"]`);

                cb.addEventListener('change', function () {
                    if (sizeSelect) sizeSelect.disabled = !this.checked;
                    if (qtyInput) qtyInput.disabled = !this.checked;
                    updateSubmitState();
                });

                if (sizeSelect) sizeSelect.disabled = true;
                if (qtyInput) qtyInput.disabled = true;
            });

            updateSubmitState();

            if (form && partialModal) {
                form.addEventListener('submit', function (e) {
                    const understocked = [];

                    checkboxes.forEach(function (cb) {
                        if (!cb.checked) return;
                        const idx = cb.dataset.index;
                        const qty = parseInt(document.querySelector(`.item-qty[data-index="${idx}"]`)?.value || '0', 10);
                        const stock = parseInt(cb.dataset.stock || '0', 10);
                        if (qty > stock) {
                            understocked.push({
                                name: cb.dataset.itemName,
                                qty, stock
                            });
                        }
                    });

                    if (understocked.length > 0) {
                        e.preventDefault();
                        const list = partialModal.querySelector('.partial-list');
                        list.innerHTML = understocked.map(u =>
                            `<div class="flex justify-between text-sm py-1 border-b border-gray-100 last:border-0">
                                <span class="text-gray-700">${u.name}</span>
                                <span class="text-gray-500">minta ${u.qty}, stok ${u.stock}</span>
                            </div>`
                        ).join('');
                        partialModal.classList.remove('hidden');
                        partialModal.classList.add('flex');
                    }
                });

                document.getElementById('partial-cancel')?.addEventListener('click', function () {
                    partialModal.classList.add('hidden');
                    partialModal.classList.remove('flex');
                });

                document.getElementById('partial-confirm')?.addEventListener('click', function () {
                    checkboxes.forEach(function (cb) {
                        if (!cb.checked) return;
                        const idx = cb.dataset.index;
                        const qtyInput = document.querySelector(`.item-qty[data-index="${idx}"]`);
                        const stock = parseInt(cb.dataset.stock || '0', 10);
                        if (parseInt(qtyInput?.value || '0', 10) > stock) {
                            qtyInput.value = Math.max(0, stock);
                        }
                    });
                    partialModal.classList.add('hidden');
                    partialModal.classList.remove('flex');
                    // adjust quantities and submit
                    HTMLFormElement.prototype.submit.call(form);
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
