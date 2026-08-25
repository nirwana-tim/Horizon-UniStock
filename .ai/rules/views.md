---
paths:
  - 'resources/views/**/*.blade.php'
---

# Views

## x-data required for $dispatch outside Alpine scope
Any button/element using @click="$dispatch(...)" MUST be inside an x-data scope. If it's outside any existing x-data, add `x-data=""` (empty) to the element. Without x-data, Alpine.js ignores the @click directive entirely — the click fires but $dispatch never runs. Pattern: `<button type="button" x-data="" @click="$dispatch('open-modal', 'name')">`. See master/student/index.blade.php:14 as reference.
