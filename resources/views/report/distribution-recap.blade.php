<x-app-layout>
    <x-page-header title="Rekap Pembagian" />

    <div class="mb-5">
        <form method="GET" action="{{ route('report.distribution-recap') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Periode</label>
                <select name="period" class="border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Periode</option>
                    @foreach($periods as $p)
                        <option value="{{ $p }}" @selected($period === $p)>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            @if(isset($studyPrograms))
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Program Studi</label>
                <select name="study_program_id" class="border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Prodi</option>
                    @foreach($studyPrograms as $prodi)
                        <option value="{{ $prodi->id }}" @selected(($studyProgramId ?? '') == $prodi->id)>{{ $prodi->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <button type="submit" class="bg-primary-700 text-white hover:bg-primary-800 rounded-lg px-4 py-2 text-sm font-medium">
                Tampilkan
            </button>
        </form>
    </div>

    @if($data->isEmpty())
        <x-empty-state title="Belum ada data" description="Belum ada data distribusi yang tersedia." />
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <x-stat-card title="Total Eligible" value="{{ number_format($data->sum('total_eligible')) }}" color="primary" />
            <x-stat-card title="Sudah Menerima" value="{{ number_format($data->sum('total_received')) }}" color="success" />
            <x-stat-card title="Belum Menerima" value="{{ number_format($data->sum('not_received')) }}" color="danger" />
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program Studi</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Eligible</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sudah Menerima</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Belum Menerima</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Progress</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($data as $index => $row)
                        @php
                            $progress = $row->total_eligible > 0 ? round(($row->total_received / $row->total_eligible) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $row->study_program_name }}</td>
                            <td class="px-4 py-3 text-sm text-center font-semibold text-gray-700">{{ number_format($row->total_eligible) }}</td>
                            <td class="px-4 py-3 text-sm text-center">
                                @if($row->total_received > 0)
                                    <x-badge type="success">{{ number_format($row->total_received) }}</x-badge>
                                @else
                                    <span class="text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                @if($row->not_received > 0)
                                    <x-badge type="danger">{{ number_format($row->not_received) }}</x-badge>
                                @else
                                    <x-badge type="success">0</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="bg-primary-700 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 font-medium">{{ $progress }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-app-layout>