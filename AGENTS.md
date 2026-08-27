# AGENTS.md — Pedoman untuk AI Assistant

> **Full version available at:** [`docs/guides/ai-agents.md`](docs/guides/ai-agents.md)

<!-- context7 -->
Use Context7 MCP to fetch current documentation whenever the user asks about a library, framework, SDK, API, CLI tool, or cloud service — even well-known ones like React, Next.js, Prisma, Express, Tailwind, Django, or Spring Boot. This includes API syntax, configuration, version migration, library-specific debugging, setup instructions, and CLI tool usage. Use even when you think you know the answer — your training data may not reflect recent changes. Prefer this over web search for library docs.

Do not use for: refactoring, writing scripts from scratch, debugging business logic, code review, or general programming concepts.

## Steps

1. Always start with `resolve-library-id` using the library name and what to look up in the library's documentation, unless the user provides an exact library ID in `/org/project` format
2. Pick the best match (ID format: `/org/project`) by: exact name match, description relevance, code snippet count, source reputation (High/Medium preferred), and benchmark score (higher is better). If results don't look right, try alternate names or queries (e.g., "next.js" not "nextjs", or rephrase the question). Use version-specific IDs when the user mentions a version
3. `query-docs` with the selected library ID and what to look up in the library's documentation (not single words), scoped to a single concept. If the question spans multiple distinct concepts (e.g. routing and auth and caching), make a separate `query-docs` call per concept with the same library ID, unless the question is about how the concepts interact — combined queries dilute ranking and return shallow results for each topic
4. Answer using the fetched docs
<!-- context7 -->

## Workflow AI WAJIB

Sebelum mengerjakan **task apa pun**, AI WAJIB mengikuti urutan ini:

1. **Baca docs/** — Semua file relevan di `docs/project/*`, `docs/technical/*`, `docs/guides/*`
2. **Cek kode existing** — Model, Controller, Service, Routes yang sudah ada
3. **Cek dokumentasi online** — Framework/package terkait (laravel.com, docs.laravel-excel.com, spatie.be, dll)
4. **Kerjakan** — Jika sudah jelas dari langkah 1-3, kerjakan dengan Laravel 12 + Blade best practices
5. **Buat baru** — Jika tidak ditemukan di dokumentasi manapun, buat solusi sendiri dengan best practices

## Dokumentasi yang WAJIB Dibaca

| # | File | Keterangan |
|---|------|-----------|
| 1 | `docs/project/overview.md` | Gambaran umum, tujuan, scope MVP, fitur per role |
| 2 | `docs/project/prd.md` | Product Requirements Document |
| 3 | `docs/project/erd.md` | ERD + detail kolom semua tabel |
| 4 | `docs/project/flowchart.md` | Flowchart lengkap semua role |
| 5 | `docs/project/architecture.md` | Arsitektur, service layer, tech stack |
| 6 | `docs/project/security.md` | Security design |
| 7 | `docs/project/item-code.md` | Item code system |
| 8 | `docs/technical/import-export.md` | Template import, export laporan, BaseExport styling |
| 9 | `docs/technical/laravel-blade.md` | Blade template, component, Vite |
| 10 | `docs/technical/breeze.md` | Auth scaffolding |
| 11 | `docs/technical/spatie-permission.md` | Role & permission |
| 12 | `docs/technical/maatwebsite-excel.md` | Export/import Excel |
| 13 | `docs/technical/qr-code.md` | Generate QR Code |
| 14 | `docs/technical/html5-qrcode.md` | Scan QR via kamera |
| 15 | `docs/technical/mail-smtp.md` | SMTP Mail |

## Aturan Kode

- **Laravel 12** style (PHP 8 attributes, Enums, typed properties)
- Logic bisnis di **Service Layer**, bukan Controller
- Migration **idempotent**, Seeder pake `firstOrCreate`
- **Spatie Permission** untuk RBAC
- Format kode barang: `KATEGORI-GENDER-TIPE-VARIANT` (contoh: `UNF-L-SCB-02`); SKU varian = `code-SIZE`
- Password **bcrypt**, validasi pake **Form Request**
- JSON response pake **Resource**
- **Route Model Binding** jika memungkinkan

## Aturan UI / Frontend

> Referensi desain lengkap: [`docs/guides/desain.md`](docs/guides/desain.md)

- **Warna brand: `primary-700` = `#980416` (Maroon)** — jangan gunakan Indigo/Blue sebagai warna utama
- **Font: Inter** (self-hosted via `@fontsource/inter`) — sudah di-load di `app.css`
- **Layout Admin & Super Admin**: Sidebar (`components/sidebar.blade.php`) — desktop only
- **Layout Staff & Student**: Bottom Tab Bar (`components/bottom-nav.blade.php`) — mobile-first
- Flash message: gunakan `<x-alert type="success|error|warning|info">` bukan inline HTML
- Badge status: gunakan `<x-badge type="success|warning|danger|info|neutral|primary">` bukan inline `span`
- Statistik dashboard: gunakan `<x-stat-card title="..." value="..." color="...">` bukan inline HTML
- Judul halaman: gunakan `<x-page-header title="...">` bukan inline `h2`
- Empty state tabel/list: gunakan `<x-empty-state title="..." description="...">` bukan inline HTML
- Card: `bg-white rounded-xl border border-gray-200 shadow-sm p-5`
- Tombol primer: `bg-primary-700 text-white hover:bg-primary-800 rounded-lg px-4 py-2 text-sm font-medium`
- Tombol sekunder (outline): `border border-primary-500 text-primary-700 hover:bg-primary-50 rounded-lg px-4 py-2 text-sm font-medium`
- Tombol bahaya: `bg-red-600 text-white hover:bg-red-700 rounded-lg px-4 py-2 text-sm font-medium`

## Role & Permission

| Role | Permissions | Keterangan |
|------|-------------|-----------|
| `super_admin` | Semua permission | Akses penuh ke seluruh sistem |
| `admin` | `manage-finance`, `manage-distributions` | Import data, entitlement, stock receive, stock opname, GPM, report |
| `staff` | `manage-students` | Scan QR, distribusi barang, validasi stok |
| `student` | (tanpa permission) | Login, input ukuran, lihat jadwal |

## Database Tables (Lengkap)

### Master Data
- `users` — Akun pengguna
- `faculties` — Fakultas
- `study_programs` — Program Studi
- `student_generations` — Generasi
- `student_levels` — Level mahasiswa (`Y1S1` … `graduated`, read-only)
- `students` — Mahasiswa
- `item_categories` — Kategori Barang
- `item_types` — Tipe Barang
- `item_departments` — Departemen Barang
- `item_sizes` — Ukuran Barang
- `category_item_size` — Pivot kategori–ukuran
- `items` — Barang (code = base_code 4 segmen)
- `item_variants` — Varian Ukuran Barang (sku = code-SIZE)
- `item_prices` — Harga Barang Per Periode
- `vendors` — Vendor/Supplier

### Student Process
- `eligibility_records` — Status Kelayakan
- `student_size_profiles` — Profil Ukuran Mahasiswa
- `student_size_items` — Ukuran Per Item
- `student_size_histories` — Riwayat Perubahan Ukuran
- `size_change_events` — Event Perubahan Ukuran
- `size_event_submissions` — Submit Mahasiswa ke Event
- `student_summaries` — Ringkasan statistik mahasiswa (harian)

### Distribution
- `entitlements` — Hak Barang (per student level)
- `entitlement_items` — Detail Hak Barang
- `distribution_schedules` — Jadwal Distribusi
- `dist_schedule_items` — Item Jadwal
- `distribution_transactions` — Transaksi Distribusi
- `distribution_items` — Detail Transaksi

### Inventory
- `stock_receives` — Penerimaan Barang
- `stock_receive_items` — Detail Penerimaan
- `stock_movements` — Pergerakan Stok (IN/OUT)
- `stock_balances` — Saldo Stok
- `stock_batches` — Batch stok FIFO
- `stock_opnames` — Batch Stock Opname
- `stock_opname_items` — Detail Stock Opname
- `stock_opname_adjustments` — Adjustment Journal

### Supporting
- `import_batches` — Log Import
- `email_notifications` — Notifikasi Email
- `otp_codes` — Kode OTP (hash)
- `smtp_settings` — Setting SMTP (dinamis, dari UI)
- `document_sequences` — Sequence nomor dokumen

## Prioritas Pengerjaan

1. Database & Migration → 2. Model & Relationship → 3. Import Service → 4. Master Data CRUD → 5. Student Flow → 6. Staff Flow → 7. Stock Opname → 8. GPM / Cost → 9. Report → 10. Testing

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.5. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `bun run build`, `bun run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.
- Record durable rules with `record-rule` so the next agent or teammate inherits them instead of working them out again. Pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Always use `record-rule`, never your native memory or notes tool — native memory is personal and session-scoped; only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `bun run build` or ask the user to run `bun run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

=== maatwebsite/excel/core rules ===

# Laravel Excel

- Use `maatwebsite/excel` for spreadsheet exports, imports, queued spreadsheet work, CSV handling, and PhpSpreadsheet integration in Laravel applications.
- Prefer explicit export/import classes with package concerns over ad-hoc spreadsheet generation in controllers, jobs, or commands.
- Activate the `laravel-excel` skill when working with `Excel::download()`, `Excel::store()`, `Excel::queue()`, `Excel::raw()`, `Excel::import()`, `Excel::toArray()`, `Excel::toCollection()`, export/import concerns, queued imports/exports, validation, CSV settings, styling, events, formulas, charts, drawings, multiple sheets, mapped cells, macros, config, cache, transactions, temporary files, or `Excel::fake()`.
- For broad docs, all-feature tasks, or missing-feature audits, use the skill's `references/package.md` feature matrix before answering.
- For large datasets, prefer `FromQuery` with queued exports or `WithChunkReading` and `WithBatchInserts` for imports.
- Test spreadsheet behavior with `Excel::fake()` when asserting dispatch/download/store/import intent, and inspect generated files only when cell contents, formatting, sheets, or writer behavior must be proven.

</laravel-boost-guidelines>
