{{--
  Page Header Component
  Props:
    - $title: string — Judul halaman (H1)
    - $subtitle: string (optional) — Deskripsi singkat
  Slots:
    - $actions (optional) — Tombol / action di sebelah kanan
    - $breadcrumb (optional) — Override breadcrumb
--}}
@props(['title', 'subtitle' => null])

<div class="mb-6">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            @isset($breadcrumb)
                <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-1">
                    {{ $breadcrumb }}
                </nav>
            @endisset

            <h1 class="text-2xl font-bold text-gray-800 leading-tight">{{ $title }}</h1>

            @if($subtitle)
                <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex items-center gap-2 flex-shrink-0 mt-1">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
