---
paths:
  - 'app/Services/StudentSizeCategoryService.php,app/Http/Controllers/Finance/SizeChangeEventController.php'
---

# Finance

## Baju/sepatu category mapping is DB-overridable
config/student-size.php holds only DEFAULTS. Super admin/admin override them via System -> Konfigurasi Ukuran, stored as app_settings keys `student-size.baju_category_codes` / `student-size.sepatu_category_codes`. Always read through StudentSizeCategoryService::bajuCategoryCodes()/sepatuCategoryCodes() (DB first, config fallback) — never read config('student-size.*') directly.
