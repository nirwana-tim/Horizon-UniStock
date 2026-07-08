# Plan: Group Search Item by base_code in Stock Receive Create

## Files to Change

### 1. `app/Http/Controllers/Master/StockReceiveController.php`

**Replace `searchItems()` — group by base_code:**
```php
public function searchItems(Request $request): JsonResponse
{
    $query = Item::whereNotNull('base_code')
        ->select('base_code')
        ->distinct();

    if ($q = $request->input('q')) {
        $query->where(function ($qry) use ($q) {
            $qry->where('name', 'like', "%{$q}%")
                ->orWhere('base_code', 'like', "%{$q}%");
        });
    }

    $items = $query->orderBy('base_code')->limit(20)->get()->map(function ($row) {
        $rep = Item::where('base_code', $row->base_code)->first();
        return [
            'id' => $row->base_code,
            'label' => ($rep->name ?? '?') . ' (' . $row->base_code . ')',
        ];
    });

    return response()->json($items);
}
```

**Add `variantsByBaseCode()` method:**
```php
public function variantsByBaseCode(string $baseCode): JsonResponse
{
    $items = Item::with('variants')->where('base_code', $baseCode)->get();

    $variants = collect();
    foreach ($items as $item) {
        foreach ($item->variants as $variant) {
            $variants->push([
                'id' => $variant->id,
                'item_id' => $item->id,
                'label' => $variant->size_label . ' (' . $variant->sku . ')',
            ]);
        }
    }

    return response()->json($variants->sortBy('label')->values());
}
```

### 2. `routes/web.php` (inventory group)

Add new route BEFORE the resource:
```php
Route::get('stock-receive/variants-by-base-code/{baseCode}', [StockReceiveController::class, 'variantsByBaseCode'])
    ->name('stock-receive.variants-by-base-code')
    ->where('baseCode', '.*');
```

### 3. `resources/views/inventory/stock-receive/create.blade.php`

**Change `variantUrlBase`:**
```javascript
// Before:
variantUrlBase: '{{ url('inventory/stock-receive/variants-by-item') }}',

// After:
variantUrlBase: '{{ url('inventory/stock-receive/variants-by-base-code') }}',
```

**Change `selectItem()` — load variants by base_code, not item_id:**
```javascript
selectItem(item) {
    this.newItem.item_label = item.label;
    this.newItem.item_label_display = item.label;
    this.newItem.item_id = '';
    this.itemSearch = '';
    this.itemSearchResults = [];
    this.itemOpen = false;
    this.newItem.variant_id = '';
    this.variantOptions = [];

    axios.get(this.variantUrlBase + '/' + encodeURIComponent(item.id))
        .then(res => { this.variantOptions = res.data; });
},
```

**Change `addItem()` — get `item_id` from selected variant:**
```javascript
addItem() {
    if (!this.newItem.item_label_display || !this.newItem.variant_id) {
        alert('Please select an item and variant first.');
        return;
    }

    const varOpt = this.variantOptions.find(o => o.id == this.newItem.variant_id);

    if (!varOpt) {
        alert('Selected variant not found.');
        return;
    }

    this.items.push({
        item_id: varOpt.item_id,
        item_label: this.newItem.item_label,
        variant_id: this.newItem.variant_id,
        variant_label: varOpt.label,
        quantity: this.newItem.quantity,
        unit_price: this.newItem.unit_price,
        hpp: this.newItem.hpp
    });

    this.newItem = {
        item_id: '', item_label: '', item_label_display: '',
        variant_id: '', variant_label: '',
        quantity: 1, unit_price: 0, hpp: 0
    };
    this.itemSearch = '';
    this.itemSearchResults = [];
    this.variantOptions = [];
    this.showModal = false;
},
```

## Alur Baru
```
Search Item: "Kemeja" → 1 hasil: "Kemeja (KTM-U-KTM-01)"
  ↓ Pilih
Select Size Variant: "S (KTM-U-KTM-01-S)", "M (KTM-U-KTM-01-M)", "L (KTM-U-KTM-01-L)"
  ↓ Pilih ukuran → otomatis dapet item_id spesifik
Add to List → simpan item_id + variant_id
```
