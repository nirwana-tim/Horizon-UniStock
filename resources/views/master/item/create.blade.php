<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Add New Item') }}</h2>
            <a href="{{ route('master-data.item.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('master-data.item.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                                <select name="category_id" id="category_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-gray-500 sm:text-sm">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->label }} ({{ $cat->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700">Gender <span class="text-red-500">*</span></label>
                                <select name="gender" id="gender" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-gray-500 sm:text-sm">
                                    <option value="">-- Select Gender --</option>
                                    <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Male</option>
                                    <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Female</option>
                                    <option value="U" {{ old('gender') == 'U' ? 'selected' : '' }}>Unisex</option>
                                </select>
                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="type_id" class="block text-sm font-medium text-gray-700">Type <span class="text-red-500">*</span></label>
                                <select name="type_id" id="type_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-gray-500 sm:text-sm">
<option value="">-- Select Type --</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->code }} - {{ $type->label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700">Department <span class="text-red-500">*</span></label>
                                <select name="department_id" id="department_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-gray-500 sm:text-sm">
                                    <option value="">-- Select Department --</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->code }} - {{ $dept->label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sizes <span class="text-red-500">*</span></label>
                                <div id="size_checkboxes" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    {{-- populated by JS --}}
                                </div>
                                @error('size_ids')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="unit" class="block text-sm font-medium text-gray-700">Unit</label>
                                <input type="text" name="unit" id="unit" value="{{ old('unit', 'pcs') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-gray-500 sm:text-sm">
                                @error('unit')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="selling_price" class="block text-sm font-medium text-gray-700">Selling Price</label>
                                <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', 0) }}" min="0" step="100"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-gray-500 sm:text-sm">
                                @error('selling_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="hpp" class="block text-sm font-medium text-gray-700">COGS</label>
                                <input type="number" name="hpp" id="hpp" value="{{ old('hpp', 0) }}" min="0" step="100"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-gray-500 sm:text-sm">
                                @error('hpp')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <div class="mt-8 flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:bg-primary-800 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Save Item') }}
                            </button>
                            <a href="{{ route('master-data.item.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const checkboxContainer = document.getElementById('size_checkboxes');
        const typeSelect = document.getElementById('type_id');
        const categorySelect = document.getElementById('category_id');

        function renderSizes(sizes) {
            checkboxContainer.innerHTML = '';
            if (!sizes.length) {
                checkboxContainer.innerHTML = '<p class="text-sm text-gray-400 italic col-span-full">No sizes available for this category</p>';
                return;
            }
            sizes.forEach(s => {
                const label = document.createElement('label');
                label.className = 'flex items-center gap-2 p-2.5 border border-gray-200 rounded-lg cursor-pointer hover:border-primary-300 hover:bg-primary-50 transition-colors has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50';
                const input = document.createElement('input');
                input.type = 'checkbox';
                input.name = 'size_ids[]';
                input.value = s.id;
                input.className = 'rounded border-gray-300 text-primary-600 focus:ring-primary-500';
                const span = document.createElement('span');
                span.className = 'text-sm text-gray-700';
                span.textContent = s.code + ' - ' + (s.label || s.name);
                label.appendChild(input);
                label.appendChild(span);
                checkboxContainer.appendChild(label);
            });
        }

        function loadSizesAndTypes(categoryId) {
            if (!categoryId) {
                renderSizes([]);
                return;
            }
            axios.get('{{ route("master-data.item.sizes-types-by-category") }}', {
                params: { category_id: categoryId }
            }).then(res => {
                renderSizes(res.data.sizes);
            }).catch(err => {
                console.error('Load sizes error:', err);
            });
        }

        categorySelect.addEventListener('change', function () {
            loadSizesAndTypes(this.value);
        });

        if (categorySelect.value) {
            loadSizesAndTypes(categorySelect.value);
        }
    </script>
</x-app-layout>
