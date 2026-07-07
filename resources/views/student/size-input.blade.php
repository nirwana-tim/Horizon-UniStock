<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Size Input') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-300 text-red-700 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(!$canUpdate)
                        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-md">
                            <p class="text-sm text-amber-600">
                                <strong>Note:</strong> You have already changed your size. A maximum of 1 change is allowed.
                            </p>
                        </div>
                    @endif

                    @if($entitlementItems->isEmpty())
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                            <p class="text-sm text-yellow-700">
                                You do not have any item entitlements for this period. Please contact Finance for more information.
                            </p>
                        </div>
                    @else
                        <form action="{{ route('student.sizes.store') }}" method="POST">
                            @csrf

                            <div class="space-y-6">
                                @foreach($entitlementItems as $item)
                                    @php
                                        $currentSize = $existingSizes[$item->id] ?? '';
                                        $sizeItem = $student->activeSizeProfile
                                            ? $student->activeSizeProfile->sizeItems->where('item_id', $item->id)->first()
                                            : null;
                                        $hasChanged = $sizeItem && $sizeItem->change_count >= 1;
                                    @endphp

                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h3 class="text-lg font-medium text-gray-900">{{ $item->name }}</h3>
                                                <p class="text-sm text-gray-500">Code: {{ $item->base_code }}</p>
                                                <p class="text-sm text-gray-500">Unit: {{ $item->unit }}</p>
                                            </div>
                                            <div class="w-48">
@if($hasChanged && !$canUpdate)
    @php
        $currentVariant = $item->variants->firstWhere('size', $currentSize);
        $sizeDisplay = $currentVariant ? $currentVariant->size_label : $currentSize;
    @endphp
    <div class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 sm:text-sm p-2">
        {{ $sizeDisplay }}
    </div>
                                                    <input type="hidden" name="sizes[{{ $item->id }}]" value="{{ $currentSize }}">
                                                    <p class="mt-1 text-xs text-amber-500">Already changed</p>
                                                @else
                                                    <label for="size_{{ $item->id }}" class="block text-sm font-medium text-gray-700">
                                                        Select Size <span class="text-red-500">*</span>
                                                    </label>
                                                    <select name="sizes[{{ $item->id }}]" id="size_{{ $item->id }}" required
                                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                                        <option value="">-- Select Size --</option>
                                                        @forelse($item->variants as $variant)
                                                            <option value="{{ $variant->size }}" {{ $currentSize == $variant->size ? 'selected' : '' }}>
                                                                {{ $variant->size_label }}
                                                            </option>
                                                        @empty
                                                            <option value="">No variants available</option>
                                                        @endforelse
                                                    </select>
                                                @endif
                                                @error("sizes.{$item->id}")
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-8 flex items-center gap-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:bg-primary-800 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Save Size') }}
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
