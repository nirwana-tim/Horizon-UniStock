<x-app-layout>
    <div x-data="{
        entitlementHtml: '',
        receivedHtml: '',
        transactionsHtml: '',
        init() {
            axios.get('{{ route('students.entitlement', $student) }}').then(res => { this.entitlementHtml = res.data; });
            axios.get('{{ route('students.received-items', $student) }}').then(res => { this.receivedHtml = res.data; });
            axios.get('{{ route('students.transactions', $student) }}').then(res => { this.transactionsHtml = res.data; });
        }
    }">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Student Details</h2>
            <div class="flex gap-2">
                <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 transition">
                    Edit
                </a>
<a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                                    Back
                                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">NIM</h3>
                            <p class="mt-1 text-sm font-mono text-gray-900">{{ $student->nim }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Name</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $student->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Campus Email</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $student->email_kampus }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Personal Email</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $student->email_pribadi ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Study Program</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $student->studyProgram->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Level / Batch</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $student->programLevel->label ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Type</h3>
                            <div class="mt-1">
                                <x-badge type="info">{{ $student->student_type_label }}</x-badge>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Account Status</h3>
                            <div class="mt-1">
                                @if($student->user_id)
                                    <x-badge type="success">Active</x-badge>
                                    <span class="text-xs text-gray-500 ml-2">{{ $student->user?->email }}</span>
                                @else
                                    <x-badge type="warning">Not Generated</x-badge>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div x-html="entitlementHtml"><div class="mt-8 pt-4 border-t border-gray-200"><p class="text-sm text-gray-400 italic">Loading entitlements...</p></div></div>
                    <div x-html="receivedHtml"><div class="mt-6 pt-4 border-t border-gray-200"><p class="text-sm text-gray-400 italic">Loading received items...</p></div></div>
                    <div x-html="transactionsHtml"><div class="mt-8 pt-4 border-t border-gray-200"><p class="text-sm text-gray-400 italic">Loading distribution history...</p></div></div>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
