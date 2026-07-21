<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @hasanyrole(['super_admin', 'admin'])
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ __('Master Data') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('master-data.faculty.index')" :active="request()->routeIs('master-data.faculty.*')">
                                {{ __('Faculty') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('master-data.study-program.index')" :active="request()->routeIs('master-data.study-program.*')">
                                {{ __('Study Program') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('master-data.program-level.index')" :active="request()->routeIs('master-data.program-level.*')">
                                {{ __('Program Level') }}
                            </x-dropdown-link>
                            <hr class="my-1 border-gray-200">
                            <x-dropdown-link :href="route('master-data.item-category.index')" :active="request()->routeIs('master-data.item-category.*')">
                                {{ __('Item Category') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('master-data.item-type.index')" :active="request()->routeIs('master-data.item-type.*')">
                                {{ __('Item Type') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('master-data.item-department.index')" :active="request()->routeIs('master-data.item-department.*')">
                                {{ __('Item Department') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('master-data.item-size.index')" :active="request()->routeIs('master-data.item-size.*')">
                                {{ __('Item Size') }}
                            </x-dropdown-link>
                            <hr class="my-1 border-gray-200">
                            <x-dropdown-link :href="route('master-data.item.index')" :active="request()->routeIs('master-data.item.*')">
                                {{ __('Item / SKU') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('master-data.vendor.index')" :active="request()->routeIs('master-data.vendor.*')">
                                {{ __('Vendor') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('distribution.distribution-schedule.index')" :active="request()->routeIs('distribution.distribution-schedule.*')">
                                {{ __('Distribution Schedule') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('distribution.entitlement.index')" :active="request()->routeIs('distribution.entitlement.*')">
                                {{ __('Entitlement') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('inventory.stock-receive.index')" :active="request()->routeIs('inventory.stock-receive.*')">
                                {{ __('Stock Receive') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                    <x-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')">
                        {{ __('Generate Account') }}
                    </x-nav-link>
                    <x-nav-link :href="route('distribution.size-monitor.index')" :active="request()->routeIs('distribution.size-monitor.*')">
                        {{ __('Size Monitor') }}
                    </x-nav-link>
                    <x-nav-link :href="route('distribution.size-events.index')" :active="request()->routeIs('distribution.size-events.*')">
                        {{ __('Event Ukuran') }}
                    </x-nav-link>
                    <x-nav-link :href="route('import.index')" :active="request()->routeIs('import.*')">
                        {{ __('Import') }}
                    </x-nav-link>
                    <x-nav-link :href="route('inventory.stock-opname.index')" :active="request()->routeIs('inventory.stock-opname.*')">
                        {{ __('Stock Opname') }}
                    </x-nav-link>
                    <x-nav-link :href="route('report.gpm-cost')" :active="request()->routeIs('report.gpm-cost')">
                        {{ __('GPM') }}
                    </x-nav-link>
                    <x-nav-link :href="route('report.index')" :active="request()->routeIs('report.*')">
                        {{ __('Reports') }}
                    </x-nav-link>
                    @endhasanyrole

                    @hasanyrole(['staff', 'admin'])
                    <x-nav-link :href="route('distribution.scan.index')" :active="request()->routeIs('distribution.scan.*')">
                        {{ __('Scan & Distribution') }}
                    </x-nav-link>
                    @endhasanyrole

                    @role('student')
                    <x-nav-link :href="route('student.sizes.index')" :active="request()->routeIs('student.*')">
                        {{ __('Input Size') }}
                    </x-nav-link>
                    <x-nav-link :href="route('student.qr')" :active="request()->routeIs('student.qr')">
                        {{ __('My QR') }}
                    </x-nav-link>
                    @endrole
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Role Badge -->
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 me-3">
                    {{ ucfirst(Auth::user()->getRoleNames()->first() ?? 'user') }}
                </span>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

                    @hasanyrole(['super_admin', 'admin'])
                    <x-responsive-nav-link :href="route('master-data.faculty.index')" :active="request()->routeIs('master-data.faculty.*')">
                        {{ __('Faculty') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('master-data.study-program.index')" :active="request()->routeIs('master-data.study-program.*')">
                        {{ __('Study Program') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('master-data.item-category.index')" :active="request()->routeIs('master-data.item-category.*')">
                        {{ __('Item Category') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('master-data.item-type.index')" :active="request()->routeIs('master-data.item-type.*')">
                        {{ __('Item Type') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('master-data.item-department.index')" :active="request()->routeIs('master-data.item-department.*')">
                        {{ __('Item Department') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('master-data.item-size.index')" :active="request()->routeIs('master-data.item-size.*')">
                        {{ __('Item Size') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('master-data.item.index')" :active="request()->routeIs('master-data.item.*')">
                        {{ __('Item / SKU') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('master-data.vendor.index')" :active="request()->routeIs('master-data.vendor.*')">
                        {{ __('Vendor') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('master-data.student-type.index')" :active="request()->routeIs('master-data.student-type.*')">
                        {{ __('Student Type') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('distribution.distribution-schedule.index')" :active="request()->routeIs('distribution.distribution-schedule.*')">
                        {{ __('Distribution Schedule') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('distribution.entitlement.index')" :active="request()->routeIs('distribution.entitlement.*')">
                        {{ __('Entitlement') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('inventory.stock-receive.index')" :active="request()->routeIs('inventory.stock-receive.*')">
                        {{ __('Stock Receive') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')">
                        {{ __('Generate Account') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('distribution.size-monitor.index')" :active="request()->routeIs('distribution.size-monitor.*')">
                        {{ __('Size Monitor') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('import.index')" :active="request()->routeIs('import.*')">
                        {{ __('Import') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('inventory.stock-opname.index')" :active="request()->routeIs('inventory.stock-opname.*')">
                        {{ __('Stock Opname') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('report.gpm-cost')" :active="request()->routeIs('report.gpm-cost')">
                        {{ __('GPM') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('report.index')" :active="request()->routeIs('report.*')">
                        {{ __('Reports') }}
                    </x-responsive-nav-link>
                    @endhasanyrole

            @hasanyrole(['staff', 'admin'])
            <x-responsive-nav-link :href="route('distribution.scan.index')" :active="request()->routeIs('distribution.scan.*')">
                {{ __('Scan & Distribution') }}
            </x-responsive-nav-link>
            @endhasanyrole

            @role('student')
            <x-responsive-nav-link :href="route('student.sizes.index')" :active="request()->routeIs('student.*')">
                {{ __('Input Size') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('student.qr')" :active="request()->routeIs('student.qr')">
                {{ __('My QR') }}
            </x-responsive-nav-link>
            @endrole
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
