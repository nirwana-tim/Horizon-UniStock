<x-app-layout>

    <x-page-header title="Kelola Akun" subtitle="Buat dan kelola akun admin (Finance) dan staff.">
        <x-slot name="actions">
            <a href="{{ route('admin.user.create') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary-700 text-white hover:bg-primary-800 rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Akun
            </a>
        </x-slot>
    </x-page-header>

    <div x-data="serverTable('{{ route('admin.user.index') }}')">

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex items-center gap-2 text-xs font-medium text-gray-700 flex-wrap">
                    <span>Filter:</span>
                    <button type="button"
                        @click="role=''; status=''; search=''; page=1; fetchData()"
                        class="px-3 py-1 rounded-full cursor-pointer"
                        :class="role === '' && status === '' ? 'bg-primary-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                        Semua
                    </button>
                    <button type="button"
                        @click="role='admin'; status=''; search=''; page=1; fetchData()"
                        class="px-3 py-1 rounded-full cursor-pointer"
                        :class="role === 'admin' ? 'bg-primary-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                        Admin
                    </button>
                    <button type="button"
                        @click="role='staff'; status=''; search=''; page=1; fetchData()"
                        class="px-3 py-1 rounded-full cursor-pointer"
                        :class="role === 'staff' ? 'bg-primary-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                        Staff
                    </button>
                    <span class="text-gray-300">|</span>
                    <button type="button"
                        @click="status='active'; role=''; search=''; page=1; fetchData()"
                        class="px-3 py-1 rounded-full cursor-pointer"
                        :class="status === 'active' ? 'bg-primary-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                        Aktif
                    </button>
                    <button type="button"
                        @click="status='inactive'; role=''; search=''; page=1; fetchData()"
                        class="px-3 py-1 rounded-full cursor-pointer"
                        :class="status === 'inactive' ? 'bg-primary-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                        Nonaktif
                    </button>
                </div>
            </div>

            <div class="p-5">
                <div class="mb-4">
                    <input type="text"
                           x-model="search"
                           @input.debounce.300ms="page=1; fetchData()"
                           placeholder="Cari nama atau email..."
                           class="w-full sm:w-80 border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody x-html="tableHtml" class="bg-white divide-y divide-gray-200">
                            @include('system.users._table')
                        </tbody>
                    </table>
                    <div x-html="paginationHtml" class="mt-4">
                        @component('components.alpine-pagination', ['paginator' => $users])@endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>