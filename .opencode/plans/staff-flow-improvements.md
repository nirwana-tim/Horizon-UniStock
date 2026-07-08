# Plan: Staff Flow Improvements

## Task 1: Migration — 4 Missing Indexes

**File:** `database/migrations/2026_07_08_000001_add_missing_performance_indexes.php`

Content:
```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function addIndexSafely(string $table, string $indexName, array|string $columns): void
    {
        if (!Schema::hasTable($table)) return;
        try {
            Schema::table($table, fn(Blueprint $t) => $t->index((array) $columns, $indexName));
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), '1061 Duplicate key name')) return;
            throw $e;
        }
    }

    public function up(): void
    {
        $this->addIndexSafely('items', 'idx_items_base_code', 'base_code');
        $this->addIndexSafely('students', 'idx_students_user_id', 'user_id');
        $this->addIndexSafely('audit_logs', 'idx_audit_logs_user_id', 'user_id');
        $this->addIndexSafely('item_prices', 'idx_item_prices_effective_date', 'effective_date');
    }

    public function down(): void
    {
        $tables = ['items', 'students', 'audit_logs', 'item_prices'];
        $indexes = [
            'items' => ['idx_items_base_code'],
            'students' => ['idx_students_user_id'],
            'audit_logs' => ['idx_audit_logs_user_id'],
            'item_prices' => ['idx_item_prices_effective_date'],
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($table, $indexes) {
                    foreach ($indexes[$table] as $index) {
                        $t->dropIndex($index);
                    }
                });
            }
        }
    }
};
```

Then run: `php artisan migrate`

---

## Task 2: GPM Report — Alpine.js Client-Side Search

### File: `resources/views/report/gpm-cost.blade.php`

Add Alpine `x-data` wrapper around the GPM Detail per Item table section:

```blade
<div x-data="{ search: '', filteredItems: @json($gpmData->values()->toArray()) }" x-init="filteredItems = @json($gpmData->values()->toArray())">
    <div class="mb-4">
        <input type="text" 
               x-model="search"
               @input="filteredItems = @json($gpmData->values()->toArray()).filter(item => 
                   item.item_name.toLowerCase().includes(search.toLowerCase()) || 
                   item.category_name.toLowerCase().includes(search.toLowerCase()) ||
                   item.item_code.toLowerCase().includes(search.toLowerCase())
               )"
               placeholder="Search items..."
               class="w-72 border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
        <span class="text-xs text-gray-400 ml-2" x-text="`${filteredItems.length} items`"></span>
    </div>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            ... same header ...
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <template x-for="(item, index) in filteredItems" :key="item.item_id">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="item.item_name"></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="item.category_name"></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="item.qty_sold.toLocaleString()"></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="'Rp ' + Number(item.hpp).toLocaleString('id-ID')"></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="'Rp ' + Number(item.selling_price).toLocaleString('id-ID')"></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="'Rp ' + Number(item.total_hpp).toLocaleString('id-ID')"></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="'Rp ' + Number(item.total_selling_price).toLocaleString('id-ID')"></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold"
                        :class="item.laba_rugi >= 0 ? 'text-green-600' : 'text-red-600'"
                        x-text="(item.laba_rugi >= 0 ? '+' : '') + 'Rp ' + Number(item.laba_rugi).toLocaleString('id-ID')">
                    </td>
                </tr>
            </template>
        </tbody>
        <tfoot class="bg-gray-50" x-show="filteredItems.length > 0">
            ... same footer (use JS to compute totals from filteredItems) ...
        </tfoot>
    </table>
</div>
```

The footer total should be computed from `filteredItems` using Alpine getter or computed values.

---

## Task 3: Distribution Scan — Live AJAX NIM Search

### File: `app/Http/Controllers/Staff/ScanController.php`

Add JSON response to `search()` method:

```php
public function search(Request $request): View|RedirectResponse|JsonResponse
{
    $request->validate([
        'query' => 'required|string|max:100',
    ]);

    $student = $this->distributionService->findStudent($request->input('query'));

    if (!$student) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['found' => false, 'message' => 'Mahasiswa tidak ditemukan.']);
        }
        return back()->withErrors(['query' => 'Mahasiswa tidak ditemukan. Pastikan NIM valid.']);
    }

    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'found' => true,
            'redirect' => route('distribution.search') . '?query=' . $student->nim,
        ]);
    }

    return $this->showDistribution($student);
}
```

Add import: `use Illuminate\Http\JsonResponse;`

### File: `resources/views/distribution/scan.blade.php`

Replace the manual search form with Alpine:

```blade
<div x-data="{ query: '', searching: false, error: '' }">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Manual Search (NIM)</h3>
    
    <div>
        <label for="query" class="block text-sm font-medium text-gray-700">Student NIM</label>
        <div class="relative">
            <input type="text" 
                   x-model="query"
                   @input.debounce.300ms="if(query.length >= 3) { searching = true; error = '';
                       axios.post('{{ route('distribution.search') }}', { query, _token: '{{ csrf_token() }}' })
                           .then(r => { if(r.data.found) window.location.href = r.data.redirect; else { error = r.data.message; searching = false; }})
                           .catch(e => { error = 'Terjadi kesalahan.'; searching = false; })
                   }"
                   placeholder="Enter student NIM..."
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
            
            <div x-show="searching" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-5 w-5 text-primary-700" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
        </div>
        <p x-show="error" x-text="error" class="mt-1 text-sm text-red-600"></p>
    </div>
    
    <div class="mt-4">
        <button type="button" 
                @click="if(query) { searching = true; error = '';
                    axios.post('{{ route('distribution.search') }}', { query, _token: '{{ csrf_token() }}' })
                        .then(r => { if(r.data.found) window.location.href = r.data.redirect; else { error = r.data.message; searching = false; }})
                        .catch(e => { error = 'Terjadi kesalahan.'; searching = false; })
                }"
                class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:bg-primary-800 disabled:opacity-50"
                :disabled="searching || !query">
            Search Student
        </button>
    </div>
</div>
```

Keep the QR scanner section unchanged.
