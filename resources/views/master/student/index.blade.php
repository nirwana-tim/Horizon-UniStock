<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {!! session('success') !!}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-4 px-4 py-3 bg-blue-100 border border-blue-300 text-blue-700 rounded-md">
                    {{ session('info') }}
                </div>
            @endif

            <div x-data="{ activeTab: '{{ request('tab') === 'generate-akun' ? 'generate' : 'data' }}' }">
                {{-- Tab Header --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="flex items-center border-b border-gray-200 px-6 pt-4 gap-2">
                        <button @click="activeTab = 'data'"
                            :class="activeTab === 'data' ? 'border-primary-700 text-primary-700 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2.5 text-sm border-b-2 -mb-px transition-colors">
                            Student Data
                        </button>
                        <button @click="activeTab = 'generate'"
                            :class="activeTab === 'generate' ? 'border-primary-700 text-primary-700 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2.5 text-sm border-b-2 -mb-px transition-colors">
                            Generate Account
                            @if($totalWithoutAccount > 0)
                                <span class="ml-1.5 inline-flex items-center justify-center w-5 h-5 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold">{{ $totalWithoutAccount }}</span>
                            @endif
                        </button>

                        <div class="ml-auto flex gap-2">
                            <template x-if="activeTab === 'data'">
                                <a href="{{ route('students.create') }}" class="inline-flex items-center px-4 py-2 bg-[#980416] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#7a0311] transition">
                                    {{ __('Add Student') }}
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Tab: Student Data --}}
                <div x-show="activeTab === 'data'"
                     x-data="serverTable('{{ route('students.index') }}')">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="mb-4">
                                <input type="text"
                                       x-model="search"
                                       @input.debounce.300ms="page=1; fetchData()"
                                       placeholder="Search..."
                                       class="w-72 border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Study Program</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
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

                {{-- Tab: Generate Account --}}
                <div x-show="activeTab === 'generate'" x-cloak>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            {{-- Stats --}}
                            <div class="flex gap-2 mb-4">
                                <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">{{ $totalWithAccount }} Has Account</span>
                                <span class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">{{ $totalWithoutAccount }} No Account</span>
                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">{{ $totalStudents }} Total</span>
                            </div>

                            @if($totalWithoutAccount > 0)
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-semibold text-gray-700">Students Without Accounts</h3>
                                    <form action="{{ route('students.generateAll') }}" method="POST" onsubmit="return confirm('Generate accounts for all {{ $totalWithoutAccount }} students?')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800">
                                                Generate All
                                        </button>
                                    </form>
                                </div>

                                <form action="{{ route('students.generate') }}" method="POST" id="generate-form">
                                    @csrf
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        <input type="checkbox" id="select-all" class="rounded border-gray-300">
                                                    </th>
                                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Study Program</th>
                                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Campus Email</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($studentsWithoutAccount as $student)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-3 py-4 whitespace-nowrap">
                                                        <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox rounded border-gray-300">
                                                    </td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->nim }}</td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $student->name }}</td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->studyProgram?->name ?? '-' }}</td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->programLevel?->name ?? '-' }}</td>
                                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->email_kampus ?? '-' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-4 flex items-center gap-3">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800">
                                            Generate Selected Accounts
                                        </button>
                                        <span class="text-sm text-gray-500" id="selected-count">0 students selected</span>
                                    </div>
                                </form>

                                <div class="mt-4">
                                    {{ $studentsWithoutAccount->links() }}
                                </div>
                            @else
                                <div class="text-center text-gray-500 py-8">
                                    All students already have accounts.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('select-all')?.addEventListener('change', function() {
            document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = this.checked);
            updateCount();
        });

        document.querySelectorAll('.student-checkbox').forEach(cb => {
            cb.addEventListener('change', updateCount);
        });

        function updateCount() {
            const count = document.querySelectorAll('.student-checkbox:checked').length;
            document.getElementById('selected-count').textContent = count + ' students selected';
        }
    </script>
    @endpush
</x-app-layout>
