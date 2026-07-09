{{--
  Topbar Component
  Props:
    - $title: string (optional) - judul halaman atau breadcrumb
    - $simple: bool (optional) - mode simple untuk Staff/Student (tanpa toggle sidebar)
--}}
@props(['title' => null, 'simple' => false])

<header x-data="{}"
    class="bg-white border-b border-gray-200 h-14 flex items-center px-4 gap-4 flex-shrink-0 z-10">

    @if (!$simple)
        {{-- Mobile sidebar toggle (desktop hidden via JS) --}}
        <button class="lg:hidden p-1.5 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors"
            @click="$dispatch('toggle-sidebar')">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    @endif

    {{-- Logo mobile (Staff/Student only) --}}
    @if ($simple)
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <div class="w-7 h-7 bg-primary-700 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-900">Horizon <span class="text-primary-700">UniStock</span></span>
        </a>
    @endif

    {{-- Breadcrumb / Title slot --}}
    <div class="flex-1 min-w-0">
        @isset($breadcrumb)
            {{ $breadcrumb }}
        @else
            @if ($title)
                <h1 class="text-sm font-semibold text-gray-700 truncate">{{ $title }}</h1>
            @endif
        @endisset
    </div>

    {{-- Right: User info (desktop only, for simple mode) --}}
    @if ($simple)
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                class="flex items-center gap-2 p-1 rounded-lg hover:bg-gray-50 transition-colors">
                <div
                    class="w-8 h-8 bg-primary-700 rounded-full flex items-center justify-center text-white text-xs font-bold uppercase">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
            </button>

            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                class="absolute right-0 top-full mt-1 w-48 bg-white rounded-lg border border-gray-200 shadow-lg py-1 z-50">
                <div class="px-3 py-2 border-b border-gray-100">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ Auth::user()->getRoleNames()->first() ?? 'user' }}
                    </p>
                </div>
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    @endif

</header>
