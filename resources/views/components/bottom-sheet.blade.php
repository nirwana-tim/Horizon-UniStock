{{--
  Bottom Sheet Component — Mobile-first modal from bottom
  Props:
    - $id: string (optional) — unique ID for the sheet
  Usage:
    <x-bottom-sheet id="size-baju">
      <h3>Ukuran Baju</h3>
      {{-- content --}}
    </x-bottom-sheet>

    Trigger:
    <button @click="$dispatch('open-bottom-sheet', { id: 'size-baju' })">Open</button>
--}}
@props(['id' => 'bottom-sheet-' . md5(microtime(true))])

<div x-data="{
        id: '{{ $id }}',
        open: false,
        content: ''
     }"
     x-on:open-bottom-sheet.window="if ($event.detail.id === id) { open = true; content = $event.detail.content || ''; $nextTick(() => $dispatch('bottom-sheet-opened', { id: id })); }"
     x-on:close-bottom-sheet.window="if ($event.detail.id === id) { open = false; $dispatch('bottom-sheet-closed', { id: id }); }"
     x-on:keydown.escape.window="if (open) { open = false; $dispatch('bottom-sheet-closed', { id: id }); }"
     x-cloak>

    {{-- Overlay --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/30 z-[100]"
         @click="open = false; $dispatch('bottom-sheet-closed', { id: id });">
    </div>

    {{-- Panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed bottom-0 left-0 right-0 z-[101] bg-white rounded-t-2xl max-h-[85vh] flex flex-col">

        {{-- Handle --}}
        <div class="sticky top-0 bg-white rounded-t-2xl z-10 px-5 pt-3 pb-2">
            <div class="w-10 h-1 bg-black/20 rounded-full mx-auto mb-2"></div>
        </div>

        {{-- Content --}}
        <div class="overflow-y-auto overscroll-contain px-5 pb-8 flex-1">
            {{ $slot }}
        </div>
    </div>
</div>
