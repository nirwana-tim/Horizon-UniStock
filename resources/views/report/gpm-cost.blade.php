<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('GPM / Profit & Loss Report') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('report.gpm-cost') }}" method="GET">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Period</label>
                                <select name="period" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                    <option value="">All Periods</option>
                                    @foreach($periods as $p)
                                        <option value="{{ $p }}" {{ $period === $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:bg-primary-800 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Total Qty Sold</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalQty) }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Total COGS</p>
                            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($total_hpp, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Total Sales</p>
                            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($total_selling, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-4 rounded-lg {{ $total_laba_rugi >= 0 ? 'bg-green-50' : 'bg-red-50' }}">
                            <p class="text-sm text-gray-500">Profit / Loss</p>
                            <p class="text-2xl font-bold {{ $total_laba_rugi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $total_laba_rugi >= 0 ? '+' : '' }}Rp {{ number_format($total_laba_rugi, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">GPM per Category</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Sold</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total COGS</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Profit / Loss</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($gpmByCategory as $index => $cat)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $cat['category'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ number_format($cat['total_qty']) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">Rp {{ number_format($cat['total_hpp'], 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">Rp {{ number_format($cat['total_selling_price'], 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold {{ $cat['laba_rugi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $cat['laba_rugi'] >= 0 ? '+' : '' }}Rp {{ number_format($cat['laba_rugi'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No GPM data yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">GPM Detail per Item</h3>

                    @php $gpmArray = $gpmData->values()->toArray(); @endphp

                    <div x-data="{
                        search: '',
                        items: {{ Js::from($gpmArray) }},
                        get filtered() {
                            if (!this.search.trim()) return this.items;
                            const q = this.search.toLowerCase();
                            return this.items.filter(i =>
                                i.item_name.toLowerCase().includes(q) ||
                                i.category_name.toLowerCase().includes(q) ||
                                i.item_code.toLowerCase().includes(q)
                            );
                        },
                        get totalQty() { return this.filtered.reduce((s, i) => s + Number(i.qty_sold), 0); },
                        get totalHpp() { return this.filtered.reduce((s, i) => s + Number(i.total_hpp), 0); },
                        get totalSell() { return this.filtered.reduce((s, i) => s + Number(i.total_selling_price), 0); },
                        get totalPl() { return this.totalSell - this.totalHpp; },
                        fmt(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); },
                        num(n) { return Number(n).toLocaleString('id-ID'); },
                    }">
                        <div class="mb-4 flex items-center gap-3">
                            <input type="text"
                                   x-model="search"
                                   placeholder="Search items..."
                                   class="w-72 border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            <span class="text-xs text-gray-400" x-text="`${filtered.length} items`"></span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Sold</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">COGS</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Selling Price</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total COGS</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Profit / Loss</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="(item, index) in filtered" :key="item.item_id">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="item.item_name"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="item.category_name"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="num(item.qty_sold)"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="fmt(item.hpp)"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="fmt(item.selling_price)"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="fmt(item.total_hpp)"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="fmt(item.total_selling_price)"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold"
                                                :class="item.laba_rugi >= 0 ? 'text-green-600' : 'text-red-600'"
                                                x-text="(item.laba_rugi >= 0 ? '+' : '') + fmt(item.laba_rugi)"></td>
                                        </tr>
                                    </template>
                                    <tr x-show="filtered.length === 0">
                                        <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">No GPM data yet.</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50" x-show="filtered.length > 0">
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-sm font-semibold text-gray-900">TOTAL</td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right" x-text="num(totalQty)"></td>
                                        <td colspan="2"></td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right" x-text="fmt(totalHpp)"></td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right" x-text="fmt(totalSell)"></td>
                                        <td class="px-6 py-4 text-sm font-bold text-right"
                                            :class="totalPl >= 0 ? 'text-green-600' : 'text-red-600'"
                                            x-text="(totalPl >= 0 ? '+' : '') + fmt(totalPl)"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
