<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard Admin') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Fakultas</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $totalFaculties }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Program Studi</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $totalStudyPrograms }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Total Item</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $totalItems }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Terima Bulan Ini</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $monthlyReceives }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Opname Draft</p>
                    <p class="mt-1 text-3xl font-bold {{ $draftOpnames > 0 ? 'text-yellow-600' : 'text-gray-900' }}">{{ $draftOpnames }}</p>
                </div>
            </div>

            @if($lowStockItems->count())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Peringatan Stok Menipis</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Sisa Stok</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($lowStockItems as $balance)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $balance->item?->name ?? '-' }}</td>
                                    <td class="px-4 py-2 text-sm text-right font-semibold {{ $balance->quantity <= 2 ? 'text-red-600' : 'text-yellow-600' }}">{{ $balance->quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Menu Cepat</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <a href="{{ route('master.faculty.index') }}" class="px-4 py-3 bg-gray-100 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 text-center">Master Data</a>
                        <a href="{{ route('import.index') }}" class="px-4 py-3 bg-gray-100 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 text-center">Import</a>
                        <a href="{{ route('master.stock-receive.index') }}" class="px-4 py-3 bg-gray-100 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 text-center">Stock Receive</a>
                        <a href="{{ route('master.entitlement.index') }}" class="px-4 py-3 bg-gray-100 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 text-center">Entitlement</a>
                        <a href="{{ route('master.distribution-schedule.index') }}" class="px-4 py-3 bg-gray-100 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 text-center">Jadwal</a>
                        <a href="{{ route('master.student-account.index') }}" class="px-4 py-3 bg-gray-100 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 text-center">Generate Akun</a>
                        <a href="{{ route('master.size-monitor.index') }}" class="px-4 py-3 bg-gray-100 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 text-center">Monitor Ukuran</a>
                        <a href="{{ route('admin.stock-opname.index') }}" class="px-4 py-3 bg-gray-100 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 text-center">Stock Opname</a>
                        <a href="{{ route('admin.gpm.index') }}" class="px-4 py-3 bg-gray-100 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 text-center">GPM</a>
                        <a href="{{ route('reports.index') }}" class="px-4 py-3 bg-gray-100 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 text-center">Reports</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
