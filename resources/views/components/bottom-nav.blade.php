{{--
  Bottom Navigation Component â€” Staff & Student only
  Fixed di bagian bawah layar mobile
--}}
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 h-16" style="padding-bottom: env(safe-area-inset-bottom, 0px)">
    <div class="flex h-full">

        @role('staff')
        {{-- Staff Tabs: Home | Scan QR | Profile --}}
        <a href="{{ route('dashboard') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                  {{ request()->routeIs('dashboard') ? 'text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('dashboard') ? '2.5' : '2' }}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-xs {{ request()->routeIs('dashboard') ? 'font-semibold' : 'font-normal' }}">Home</span>
        </a>

        <a href="{{ route('distribution.scan.index') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                  {{ request()->routeIs('distribution.scan.*') ? 'text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
            {{-- Scan button besar di tengah --}}
            <div class="{{ request()->routeIs('distribution.scan.*') ? 'bg-primary-700' : 'bg-gray-200' }} w-10 h-10 rounded-full flex items-center justify-center -mt-3 shadow-md transition-colors">
                <svg aria-hidden="true" class="w-5 h-5 {{ request()->routeIs('distribution.scan.*') ? 'text-white' : 'text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8H3a2 2 0 00-2 2v8a2 2 0 002 2h14a2 2 0 002-2v-8a2 2 0 00-2-2h-2"/>
                </svg>
            </div>
            <span class="text-xs {{ request()->routeIs('distribution.scan.*') ? 'font-semibold text-primary-700' : 'font-normal text-gray-400' }} -mt-1">Scan</span>
        </a>

        <a href="{{ route('profile.edit') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                  {{ request()->routeIs('profile.*') ? 'text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('profile.*') ? '2.5' : '2' }}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-xs {{ request()->routeIs('profile.*') ? 'font-semibold' : 'font-normal' }}">Account</span>
        </a>
        @endrole

        @role('student')
        {{-- Student Tabs: Home | Sizes | My QR | Schedule | Profile --}}
        <a href="{{ route('dashboard') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                  {{ request()->routeIs('dashboard') ? 'text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('dashboard') ? '2.5' : '2' }}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-xs {{ request()->routeIs('dashboard') ? 'font-semibold' : 'font-normal' }}">Home</span>
        </a>

        <a href="{{ route('student.sizes.index') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                  {{ request()->routeIs('student.sizes.*') ? 'text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('student.sizes.*') ? '2.5' : '2' }}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
            </svg>
            <span class="text-xs {{ request()->routeIs('student.sizes.*') ? 'font-semibold' : 'font-normal' }}">Sizes</span>
        </a>

        <a href="{{ route('student.qr') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                  {{ request()->routeIs('student.qr') ? 'text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
            <div class="{{ request()->routeIs('student.qr') ? 'bg-primary-700' : 'bg-gray-200' }} w-10 h-10 rounded-full flex items-center justify-center -mt-3 shadow-md transition-colors">
                <svg aria-hidden="true" class="w-5 h-5 {{ request()->routeIs('student.qr') ? 'text-white' : 'text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8H3a2 2 0 00-2 2v8a2 2 0 002 2h14a2 2 0 002-2v-8a2 2 0 00-2-2h-2"/>
                </svg>
            </div>
            <span class="text-xs {{ request()->routeIs('student.qr') ? 'font-semibold text-primary-700' : 'font-normal text-gray-400' }} -mt-1">QR</span>
        </a>

        <a href="{{ route('student.items.index') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                  {{ request()->routeIs('student.items.*') ? 'text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('student.items.*') ? '2.5' : '2' }}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
            </svg>
            <span class="text-xs {{ request()->routeIs('student.items.*') ? 'font-semibold' : 'font-normal' }}">Items</span>
        </a>

        <a href="{{ route('profile.edit') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                  {{ request()->routeIs('profile.*') ? 'text-primary-700' : 'text-gray-400 hover:text-gray-600' }}">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('profile.*') ? '2.5' : '2' }}">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-xs {{ request()->routeIs('profile.*') ? 'font-semibold' : 'font-normal' }}">Account</span>
        </a>
        @endrole

    </div>
</nav>
