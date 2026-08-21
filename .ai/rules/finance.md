---
paths:
  - 'app/Http/Controllers/Finance/SizeChangeEventController.php'
---

# Finance

## Baju/sepatu size flags
Item sizes are tagged via `is_baju` / `is_sepatu` boolean columns on the `item_sizes` table. Toggle from Master Data → Item Size (checkboxes). SizeChangeEventController queries `ItemSize::where('is_baju', true)` for chip options — never read config or category pivot directly.
