<x-app-layout>

    <x-page-header title="Students">
        <x-slot name="actions">
            <a href="{{ route('templates.download', 'mahasiswa') }}" class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 rounded-md text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Template
            </a>
            <button type="button" @click="$dispatch('open-modal', 'import-student')" class="inline-flex items-center gap-1.5 px-4 py-2 border border-primary-500 text-primary-700 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-primary-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Import Student
            </button>
            <a href="{{ route('students.promote.form') }}" class="inline-flex items-center px-4 py-2 border border-primary-500 text-primary-700 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-primary-50 transition">
                Promote
            </a>
            <a href="{{ route('students.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 transition">
                {{ __('Add Student') }}
            </a>
        </x-slot>
    </x-page-header>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="serverTable('{{ route('students.index') }}')">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="mb-4 space-y-3">
                                <div class="flex items-center gap-3">
                                    <input type="text"
                                           x-model="search"
                                           @input.debounce.300ms="page=1; fetchData()"
                                           placeholder="Search..."
                                           class="w-72 border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                    <div class="flex items-center gap-2 ml-auto">
                                        <label class="text-xs text-gray-500">Per page:</label>
                                        <select x-model="perPage" @change="page=1; fetchData()"
                                            class="border-gray-300 rounded-md shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500">
                                            <option value="10">10</option>
                                            <option value="20" selected>20</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        <a href="{{ route('students.export', request()->only(['q'])) }}"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                                            Export
                                        </a>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <select x-model="studyProgramId" @change="page=1; fetchData()"
                                        class="w-56 border-gray-300 rounded-md shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500">
                                        <option value="">All Study Programs</option>
                                        @foreach($studyPrograms as $program)
                                            <option value="{{ $program->id }}">{{ $program->faculty->code }} - {{ $program->name }}</option>
                                        @endforeach
                                    </select>
                                    <select x-model="generationId" @change="page=1; fetchData()"
                                        class="w-56 border-gray-300 rounded-md shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500">
                                        <option value="">All Levels</option>
                                        @foreach($generations as $level)
                                            <option value="{{ $level->id }}">{{ $level->label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Study Program</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generation</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Level</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Kuliah</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody x-html="tableHtml" class="bg-white divide-y divide-gray-200">
                                        @include('master.student._table')
                                    </tbody>
                                </table>
                                <div x-html="paginationHtml" class="mt-4">
                                    @component('components.alpine-pagination', ['paginator' => $students])@endcomponent
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

    <x-import-modal
        name="import-student"
        type="student"
        template-type="mahasiswa"
        title="Import Data Mahasiswa"
        description="Upload file Excel data mahasiswa untuk di-import ke sistem."
    />

</x-app-layout>
