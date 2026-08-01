{{--
  Bottom Navigation Component - Student only
  Fixed di bagian bawah layar mobile, matching index.html design
--}}
<nav class="fixed bottom-0 w-full z-50 flex items-center h-[72px] bg-white/80 backdrop-blur-xl border-t border-black/[0.04] safe-area-bottom">
    @role('student')
    {{-- Student Tabs: Beranda | Ukuran | QR | Barang | Profil --}}
    <a href="{{ route('dashboard') }}"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'text-primary active-nav' : 'text-black/30' }}"
       {{ request()->routeIs('dashboard') ? 'aria-current="page"' : '' }}>
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('dashboard') ? '1' : '0' }}, 'wght' 400, 'GRAD' 0, 'opsz' 24;">home</span>
        <span class="text-[10px] font-semibold tracking-wide">Beranda</span>
    </a>

    <a href="{{ route('student.sizes.index') }}"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-colors duration-200 {{ request()->routeIs('student.sizes.*') ? 'text-primary active-nav' : 'text-black/30' }}"
       {{ request()->routeIs('student.sizes.*') ? 'aria-current="page"' : '' }}>
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('student.sizes.*') ? '1' : '0' }}, 'wght' 400, 'GRAD' 0, 'opsz' 24;">straighten</span>
        <span class="text-[10px] font-semibold tracking-wide">Ukuran</span>
    </a>

    <a href="{{ route('student.qr') }}"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-colors duration-200 {{ request()->routeIs('student.qr') ? 'text-primary active-nav' : 'text-black/30' }}"
       {{ request()->routeIs('student.qr') ? 'aria-current="page"' : '' }}>
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('student.qr') ? '1' : '0' }}, 'wght' 400, 'GRAD' 0, 'opsz' 24;">qr_code_scanner</span>
        <span class="text-[10px] font-semibold tracking-wide">Scan</span>
    </a>

    <a href="{{ route('student.items.index') }}"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-colors duration-200 {{ request()->routeIs('student.items.*') ? 'text-primary active-nav' : 'text-black/30' }}"
       {{ request()->routeIs('student.items.*') ? 'aria-current="page"' : '' }}>
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('student.items.*') ? '1' : '0' }}, 'wght' 400, 'GRAD' 0, 'opsz' 24;">inventory_2</span>
        <span class="text-[10px] font-semibold tracking-wide">Barang</span>
    </a>

    <a href="{{ route('profile.edit') }}"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-colors duration-200 {{ request()->routeIs('profile.*') ? 'text-primary active-nav' : 'text-black/30' }}"
       {{ request()->routeIs('profile.*') ? 'aria-current="page"' : '' }}>
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('profile.*') ? '1' : '0' }}, 'wght' 400, 'GRAD' 0, 'opsz' 24;">person</span>
        <span class="text-[10px] font-semibold tracking-wide">Profil</span>
    </a>
    @endrole

    @role('staff')
    {{-- Staff Tabs: Beranda | Scan QR | Profil --}}
    <a href="{{ route('dashboard') }}"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'text-primary active-nav' : 'text-black/30' }}"
       {{ request()->routeIs('dashboard') ? 'aria-current="page"' : '' }}>
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('dashboard') ? '1' : '0' }}, 'wght' 400, 'GRAD' 0, 'opsz' 24;">home</span>
        <span class="text-[10px] font-semibold tracking-wide">Beranda</span>
    </a>

    <a href="{{ route('distribution.scan.index') }}"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-colors duration-200 {{ request()->routeIs('distribution.scan.*') ? 'text-primary active-nav' : 'text-black/30' }}"
       {{ request()->routeIs('distribution.scan.*') ? 'aria-current="page"' : '' }}>
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('distribution.scan.*') ? '1' : '0' }}, 'wght' 400, 'GRAD' 0, 'opsz' 24;">qr_code_scanner</span>
        <span class="text-[10px] font-semibold tracking-wide">Scan</span>
    </a>

    <a href="{{ route('profile.edit') }}"
       class="flex-1 flex flex-col items-center justify-center gap-0.5 active:scale-90 transition-colors duration-200 {{ request()->routeIs('profile.*') ? 'text-primary active-nav' : 'text-black/30' }}"
       {{ request()->routeIs('profile.*') ? 'aria-current="page"' : '' }}>
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('profile.*') ? '1' : '0' }}, 'wght' 400, 'GRAD' 0, 'opsz' 24;">person</span>
        <span class="text-[10px] font-semibold tracking-wide">Profil</span>
    </a>
    @endrole
</nav>
