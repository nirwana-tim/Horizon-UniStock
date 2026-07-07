<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Item Price Details') }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('master-data.item-price.edit', $itemPrice) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 transition">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('master-data.item-price.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Item</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $itemPrice->item->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Item Code</h3>
                            <p class="mt-1 text-sm font-mono text-gray-900">{{ $itemPrice->item->code ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Selling Price</h3>
                            <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($itemPrice->selling_price, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">COGS</h3>
                            <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($itemPrice->hpp, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Effective Date</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $itemPrice->effective_date?->format('d/m/Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Created</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $itemPrice->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>
                    </div>

                    @php
                        $margin = $itemPrice->hpp > 0 ? (($itemPrice->selling_price - $itemPrice->hpp) / $itemPrice->hpp * 100) : 0;
                    @endphp
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-500">Margin</h3>
                        <p class="mt-1 text-sm {{ $margin >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format($itemPrice->selling_price - $itemPrice->hpp, 0, ',', '.') }} ({{ number_format($margin, 1) }}%)
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-200 flex gap-2">
                        <x-delete-modal
                            :route="route('master-data.item-price.destroy', $itemPrice)"
                            label="Delete Item Price"
                            description="Are you sure you want to delete this price data? This action cannot be undone."
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
