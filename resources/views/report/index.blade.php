<x-app-layout>
    <x-page-header title="Report" subtitle="Download laporan distribusi, stok, dan keuangan">
        <x-slot name="breadcrumb">
            <span class="text-gray-800 font-medium">Report</span>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Distribusi --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Distribution</h3>
            </div>
            <p class="text-sm text-gray-500 mb-4">Item distribution recap per period.</p>
            <form action="{{ route('report.distribution') }}" method="GET">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Period</label>
                    <select name="period" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <option value="">All</option>
                        @foreach($periods as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-primary-700 text-white hover:bg-primary-800 rounded-lg px-4 py-2 text-sm font-medium inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Excel
                </button>
            </form>
        </div>

        {{-- Distribution Recap --}}
        <div class="bg-white rounded-xl border border-primary-200 shadow-sm p-5">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Rekap Pembagian</h3>
            </div>
            <p class="text-sm text-gray-500 mb-4">Distribution recap: eligible, received, and remaining per study program.</p>
            <a href="{{ route('report.distribution-recap') }}" class="w-full bg-primary-700 text-white hover:bg-primary-800 rounded-lg px-4 py-2 text-sm font-medium inline-flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View Recap
            </a>
        </div>

        {{-- Stok Inventaris --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Stock Inventory</h3>
            </div>
            <p class="text-sm text-gray-500 mb-4">Current stock, total in & out.</p>
            <form action="{{ route('report.stock') }}" method="GET">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->code }}">{{ $cat->label }} ({{ $cat->code }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white hover:bg-green-700 rounded-lg px-4 py-2 text-sm font-medium inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Excel
                </button>
            </form>
        </div>

        {{-- Stock Opname --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Stock Opname</h3>
            </div>
            <p class="text-sm text-gray-500 mb-4">Opname result: system stock vs physical count difference.</p>
            <form action="{{ route('report.stock-opname') }}" method="GET">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Opname Period</label>
                    <select name="stock_opname_id" required class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Select Period</option>
                        @foreach($stockOpnames as $id => $period)
                            <option value="{{ $id }}">{{ $period }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-purple-600 text-white hover:bg-purple-700 rounded-lg px-4 py-2 text-sm font-medium inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Excel
                </button>
            </form>
        </div>

        {{-- GPM --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">GPM / Gross Profit</h3>
            </div>
            <p class="text-sm text-gray-500 mb-4">Gross profit per item with margin color coding.</p>
            <form action="{{ route('report.gpm') }}" method="GET">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Period</label>
                    <select name="period" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-yellow-500 sm:text-sm">
                        <option value="">All</option>
                        @foreach($periods as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-primary-700 text-white hover:bg-primary-800 rounded-lg px-4 py-2 text-sm font-medium inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Excel
                </button>
            </form>
        </div>

        {{-- Kartu Stok --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v2m0 4v2m-4-6v2m0 4v2M4 6h2m4 0h2m4 0h2M4 18h16a2 2 0 002-2V8a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Stock Card</h3>
            </div>
            <p class="text-sm text-gray-500 mb-4">Stock movement history (IN/OUT) per item.</p>
            <form action="{{ route('report.stock-card') }}" method="GET">
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Item Code</label>
                    <select name="item_code" required class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Select Item</option>
                        @foreach($items as $code)
                            <option value="{{ $code }}">{{ $code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700">From</label>
                        <input type="date" name="start_date" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">To</label>
                        <input type="date" name="end_date" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    </div>
                </div>
                <button type="submit" class="w-full bg-primary-700 text-white hover:bg-primary-800 rounded-lg px-4 py-2 text-sm font-medium inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Excel
                </button>
            </form>
        </div>

        {{-- Susut Stok --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Stock Loss</h3>
            </div>
            <p class="text-sm text-gray-500 mb-4">Loss/gain recap from stock opname per category.</p>
            <form action="{{ route('report.loss') }}" method="GET">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Period</label>
                    <select name="period" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <option value="">All</option>
                        @foreach($periods as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-red-600 text-white hover:bg-red-700 rounded-lg px-4 py-2 text-sm font-medium inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Excel
                </button>
            </form>
        </div>

        {{-- Inventory (Legacy) --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                    </svg>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Inventory (Summary)</h3>
            </div>
            <p class="text-sm text-gray-500 mb-4">All-in-one stock summary.</p>
            <form action="{{ route('report.inventory') }}" method="GET">
                <button type="submit" class="w-full bg-teal-600 text-white hover:bg-teal-700 rounded-lg px-4 py-2 text-sm font-medium inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Excel
                </button>
            </form>
        </div>

        {{-- Rekap Ukuran --}}
        <div class="bg-white rounded-xl border border-primary-200 shadow-sm p-5">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Size Recap</h3>
            </div>
            <p class="text-sm text-gray-500 mb-4">Student size recap (for vendor ordering input).</p>
            <form action="{{ route('report.size-recap') }}" method="GET">
                <div class="grid grid-cols-1 gap-2 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Generation</label>
                        <select name="generation_id" class="block w-full border-gray-300 rounded-lg shadow-sm sm:text-sm">
                            <option value="">All Generations</option>
                            @foreach($generations as $level)
                                <option value="{{ $level->id }}">{{ $level->label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Study Program</label>
                        <select name="study_program_id" class="block w-full border-gray-300 rounded-lg shadow-sm sm:text-sm">
                            <option value="">All Study Programs</option>
                            @foreach($studyPrograms as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full bg-primary-700 text-white hover:bg-primary-800 rounded-lg px-4 py-2 text-sm font-medium inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Excel
                 </button>
            </form>
        </div>

    </div>
</x-app-layout>