{{--
  Bottom Navigation Component — Staff & Student only
  Fixed di bagian bawah layar mobile
--}}
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 h-16">
    <div class="flex h-full">

        @role('staff')
        {{-- Staff Tabs: Beranda | Scan QR | Profil --}}
        <a href="{{ route('dashboard') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                  {{ request()->routeIs('dashboard') ? 'text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('dashboard') ? '2.5' : '2' }}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-xs {{ request()->routeIs('dashboard') ? 'font-semibold' : 'font-normal' }}">Beranda</span>
        </a>

        <a href="{{ route('staff.scan.index') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                  {{ request()->routeIs('staff.*') ? 'text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
            {{-- Scan button besar di tengah --}}
            <div class="{{ request()->routeIs('staff.*') ? 'bg-primary-700' : 'bg-gray-200' }} w-10 h-10 rounded-full flex items-center justify-center -mt-3 shadow-md transition-colors">
                <svg class="w-5 h-5 {{ request()->routeIs('staff.*') ? 'text-white' : 'text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8H3a2 2 0 00-2 2v8a2 2 0 002 2h14a2 2 0 002-2v-8a2 2 0 00-2-2h-2"/>
                </svg>
            </div>
            <span class="text-xs {{ request()->routeIs('staff.*') ? 'font-semibold text-primary-700' : 'font-normal text-gray-400' }} -mt-1">Scan</span>
        </a>

        <a href="{{ route('profile.edit') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                  {{ request()->routeIs('profile.*') ? 'text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('profile.*') ? '2.5' : '2' }}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-xs {{ request()->routeIs('profile.*') ? 'font-semibold' : 'font-normal' }}">Akun</span>
        </a>
        @endrole

        @role('student')
        {{-- Student Tabs: Beranda | Ukuran | QR Saya | Jadwal | Profil --}}
        <a href="{{ route('dashboard') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                  {{ request()->routeIs('dashboard') ? 'text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('dashboard') ? '2.5' : '2' }}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-xs {{ request()->routeIs('dashboard') ? 'font-semibold' : 'font-normal' }}">Beranda</span>
        </a>

        <a href="{{ route('student.sizes.index') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                  {{ request()->routeIs('student.sizes.*') ? 'text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('student.sizes.*') ? '2.5' : '2' }}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
            </svg>
            <span class="text-xs {{ request()->routeIs('student.sizes.*') ? 'font-semibold' : 'font-normal' }}">Ukuran</span>
        </a>

        <a href="{{ route('student.qr') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                  {{ request()->routeIs('student.qr') ? 'text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
            <div class="{{ request()->routeIs('student.qr') ? 'bg-primary-700' : 'bg-gray-200' }} w-10 h-10 rounded-full flex items-center justify-center -mt-3 shadow-md transition-colors">
                <svg class="w-5 h-5 {{ request()->routeIs('student.qr') ? 'text-white' : 'text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8H3a2 2 0 00-2 2v8a2 2 0 002 2h14a2 2 0 002-2v-8a2 2 0 00-2-2h-2"/>
                </svg>
            </div>
            <span class="text-xs {{ request()->routeIs('student.qr') ? 'font-semibold text-primary-700' : 'font-normal text-gray-400' }} -mt-1">QR</span>
        </a>

        <a href="{{ route('master.distribution-schedule.index') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                  {{ request()->routeIs('master.distribution-schedule.*') ? 'text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('master.distribution-schedule.*') ? '2.5' : '2' }}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-xs {{ request()->routeIs('master.distribution-schedule.*') ? 'font-semibold' : 'font-normal' }}">Jadwal</span>
        </a>

        <a href="{{ route('profile.edit') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                  {{ request()->routeIs('profile.*') ? 'text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('profile.*') ? '2.5' : '2' }}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-xs {{ request()->routeIs('profile.*') ? 'font-semibold' : 'font-normal' }}">Akun</span>
        </a>
        @endrole

    </div>
</nav>
