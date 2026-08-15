---
paths:
  - 'app/Exports/**'
---

# Exports

## WithStyles::styles() must return ?array
maatwebsite/excel 4.0's WithStyles interface requires `styles(Worksheet $sheet): ?array`. Any export/template class declaring `styles(): void` fatals on instantiation (breaks all template downloads & report exports). Always declare `: ?array` and end with `return null;` if the method only mutates the sheet directly. BaseExport helpers mutate the sheet; do not build a returned style array.
