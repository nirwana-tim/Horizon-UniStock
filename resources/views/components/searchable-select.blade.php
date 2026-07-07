@props([
    'name',
    'options' => [],          // Array of ['value' => ..., 'label' => ..., 'group' => null]
    'value' => null,
    'placeholder' => 'Search and select...',
    'required' => false,
    'searchPlaceholder' => 'Type to search...',
])

<div x-data="{
    open: false,
    search: '',
    selectedValue: '{{ $value }}',
    selectedLabel: '',
    highlightedIndex: -1,
    get filteredOptions() {
        const q = this.search.toLowerCase();
        const items = {{ json_encode($options) }}.filter(opt => {
            return opt.label.toLowerCase().includes(q) || (opt.group && opt.group.toLowerCase().includes(q));
        });
        return items;
    },
    select(value, label) {
        this.selectedValue = value;
        this.selectedLabel = label;
        this.open = false;
        this.search = '';
        this.highlightedIndex = -1;
        this.$refs.hiddenInput.value = value;
        this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
    },
    init() {
        const items = {{ json_encode($options) }};
        const found = items.find(o => o.value == this.selectedValue);
        if (found) this.selectedLabel = found.label;
    },
    handleKeydown(e) {
        if (e.key === 'Escape') { this.open = false; return; }
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            this.highlightedIndex = Math.min(this.highlightedIndex + 1, this.filteredOptions.length - 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            this.highlightedIndex = Math.max(this.highlightedIndex - 1, 0);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (this.highlightedIndex >= 0 && this.filteredOptions[this.highlightedIndex]) {
                const opt = this.filteredOptions[this.highlightedIndex];
                this.select(opt.value, opt.label);
            }
        }
    }
}" x-init="init()" class="relative" {{ $attributes->merge(['class' => '']) }}>

    <input type="hidden" name="{{ $name }}" x-ref="hiddenInput" :value="selectedValue" @if($required) required @endif>

    <div @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
         class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm cursor-pointer bg-white flex items-center justify-between px-3 py-2"
         :class="open ? 'ring-2 ring-primary-500 border-primary-500' : ''">
        <span x-text="selectedLabel || '{{ addslashes($placeholder) }}'" :class="selectedLabel ? 'text-gray-900' : 'text-gray-400'"></span>
        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </div>

    <div x-show="open" x-cloak @click.outside="open = false"
         class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-hidden">
        <div class="sticky top-0 bg-white p-2 border-b border-gray-100">
            <input x-ref="searchInput" x-model="search" @keydown="handleKeydown($event)"
                   type="text" placeholder="{{ addslashes($searchPlaceholder) }}"
                   class="w-full px-3 py-1.5 text-sm border border-gray-200 rounded-md focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none" />
        </div>
        <ul class="overflow-y-auto max-h-48 py-1">
            <template x-for="(opt, index) in filteredOptions" :key="opt.value">
                <li @click="select(opt.value, opt.label)"
                    @mouseenter="highlightedIndex = index"
                    class="px-3 py-2 text-sm cursor-pointer flex items-center justify-between"
                    :class="{
                        'bg-primary-50 text-primary-700': highlightedIndex === index,
                        'hover:bg-gray-50 text-gray-900': highlightedIndex !== index
                    }">
                    <span x-text="opt.label"></span>
                    <span x-show="opt.group" x-text="opt.group" class="text-xs text-gray-400 ml-2"></span>
                </li>
            </template>
            <li x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-400 italic text-center">
                Not found
            </li>
        </ul>
    </div>
</div>
