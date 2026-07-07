<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Import Data') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Template Downloads --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Download Template Import</h3>
                    <p class="text-sm text-gray-500 mb-4">Download the Excel template, fill in the data, then upload using the form below.</p>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <a href="{{ route('templates.download', 'mahasiswa') }}" class="inline-flex items-center justify-center px-4 py-2 bg-[#980416] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#7a0311] transition">
                            Student
                        </a>
                        <a href="{{ route('templates.download', 'dp_lunas') }}" class="inline-flex items-center justify-center px-4 py-2 bg-[#980416] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#7a0311] transition">
                            Eligibility / DP Paid
                        </a>
                        <a href="{{ route('templates.download', 'katalog') }}" class="inline-flex items-center justify-center px-4 py-2 bg-[#980416] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#7a0311] transition">
                            Item Catalog
                        </a>
                        <a href="{{ route('templates.download', 'harga') }}" class="inline-flex items-center justify-center px-4 py-2 bg-[#980416] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#7a0311] transition">
                            Item Price
                        </a>
                        <a href="{{ route('templates.download', 'hak_barang') }}" class="inline-flex items-center justify-center px-4 py-2 bg-[#980416] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#7a0311] transition">
                            Item Entitlement
                        </a>
                    </div>
                </div>
            </div>

            {{-- Upload --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            <div>
                                <x-input-label for="import_type" :value="__('Import Type')" />
                                <select name="import_type" id="import_type" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">
                                    <option value="">-- Select Import Type --</option>
                                    <option value="student" {{ old('import_type') === 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="eligibility" {{ old('import_type') === 'eligibility' ? 'selected' : '' }}>Eligibility / DP Paid</option>
                                    <option value="item" {{ old('import_type') === 'item' ? 'selected' : '' }}>Item Catalog</option>
                                    <option value="item_price" {{ old('import_type') === 'item_price' ? 'selected' : '' }}>Item Price</option>
                                    <option value="entitlement" {{ old('import_type') === 'entitlement' ? 'selected' : '' }}>Item Entitlement</option>
                                    <option value="stock_opname" {{ old('import_type') === 'stock_opname' ? 'selected' : '' }}>Stock Opname</option>
                                </select>
                                <x-input-error :messages="$errors->get('import_type')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="file" :value="__('Excel File')" />
                                <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#980416] file:text-white hover:file:bg-[#7a0311]" />
                                <x-input-error :messages="$errors->get('file')" class="mt-2" />
                            </div>

                            <div class="flex items-end">
                                <x-primary-button class="w-full justify-center bg-[#980416] hover:bg-[#7a0311]">
                                    {{ __('Import') }}
                                </x-primary-button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
