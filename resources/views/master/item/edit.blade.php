<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Item') }}</h2>
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
                    <form action="{{ route('master-data.item.update', $item) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Item Code</label>
                                <p class="mt-1 text-sm font-mono text-gray-900">{{ $item->code }}</p>
                                <input type="hidden" name="code" value="{{ $item->code }}">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Item Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $item->name }}</p>
                                <input type="hidden" name="name" value="{{ $item->name }}">
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 font-semibold">Category</label>
                                <select id="category_id_display" disabled
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 sm:text-sm cursor-not-allowed">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->label }} ({{ $cat->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="category_id" id="category_id" value="{{ $item->category_id }}">
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 font-semibold">Gender</label>
                                <select id="gender_display" disabled
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 sm:text-sm cursor-not-allowed">
                                    <option value="">-- Select Gender --</option>
                                    <option value="L" {{ old('gender', $item->gender) == 'L' ? 'selected' : '' }}>Male</option>
                                    <option value="P" {{ old('gender', $item->gender) == 'P' ? 'selected' : '' }}>Female</option>
                                    <option value="U" {{ old('gender', $item->gender) == 'U' ? 'selected' : '' }}>Unisex</option>
                                </select>
                                <input type="hidden" name="gender" id="gender" value="{{ $item->gender }}">
                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="type_id" class="block text-sm font-medium text-gray-700 font-semibold">Type</label>
                                <select id="type_id_display" disabled
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 sm:text-sm cursor-not-allowed">
<option value="">-- Select Type --</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}" {{ old('type_id', $item->type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->code }} - {{ $type->label }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="type_id" id="type_id" value="{{ $item->type_id }}">
                                @error('type_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700 font-semibold">Department</label>
                                <select id="department_id_display" disabled
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 sm:text-sm cursor-not-allowed">
                                    <option value="">-- Select Department --</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id', $item->department_id) == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->code }} - {{ $dept->label }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="department_id" id="department_id" value="{{ $item->department_id }}">
                                @error('department_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <div>
                                <label for="unit" class="block text-sm font-medium text-gray-700">Unit</label>
                                <input type="text" name="unit" id="unit" value="{{ old('unit', $item->unit) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-gray-500 sm:text-sm">
                                @error('unit')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="selling_price" class="block text-sm font-medium text-gray-700">Selling Price</label>
                                <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', $item->selling_price) }}" min="0" step="100"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-gray-500 sm:text-sm">
                                @error('selling_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="hpp" class="block text-sm font-medium text-gray-700">COGS</label>
                                <input type="number" name="hpp" id="hpp" value="{{ old('hpp', $item->hpp) }}" min="0" step="100"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-gray-500 sm:text-sm">
                                @error('hpp')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="size_id" class="block text-sm font-medium text-gray-700 font-semibold">Size</label>
                            <select id="size_id_display" disabled
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 sm:text-sm cursor-not-allowed">
                                <option value="">-- Select Size --</option>
                            </select>
                            <input type="hidden" name="size_id" id="size_id" value="{{ old('size_id', $item->variants->first()?->size_id) }}">
                            @error('size_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-8 flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:bg-primary-800 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Save Changes') }}
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
        const sizeSelect = document.getElementById('size_id_display');
        const typeSelect = document.getElementById('type_id_display');
        const categorySelect = document.getElementById('category_id_display');
        const currentSizeId = {{ old('size_id', $item->variants->first()?->size_id ?? 'null') }};
        const currentTypeId = {{ old('type_id', $item->type_id ?? 'null') }};

        function renderSizes(sizes) {
            sizeSelect.innerHTML = '<option value="">-- Select Size --</option>';
            sizes.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.code + ' - ' + (s.label || s.name);
                if (s.id == currentSizeId) {
                    opt.selected = true;
                }
                sizeSelect.appendChild(opt);
            });
        }

        function renderTypes(types) {
            typeSelect.innerHTML = '<option value="">-- Select Type --</option>';
            types.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = t.code + ' - ' + (t.label || t.name);
                if (t.id == currentTypeId) {
                    opt.selected = true;
                }
                typeSelect.appendChild(opt);
            });
        }

        function loadSizesAndTypes(categoryId) {
            if (!categoryId) return;
            axios.get('{{ route("master-data.item.sizes-types-by-category") }}', {
                params: { category_id: categoryId }
            }).then(res => {
                renderSizes(res.data.sizes);
                renderTypes(res.data.types);
            });
        }

        loadSizesAndTypes(categorySelect.value);
    </script>
</x-app-layout>
