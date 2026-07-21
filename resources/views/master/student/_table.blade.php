@forelse($students as $index => $student)
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $students->firstItem() + $index }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $student->nim }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->name }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->studyProgram->name ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->programLevel->label ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap">
            <x-badge type="info">{{ $student->student_type_label }}</x-badge>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            @if($student->status === 'leave')
                <x-badge type="warning">Cuti</x-badge>
            @elseif($student->status === 'graduated')
                <x-badge type="neutral">Lulus</x-badge>
            @elseif($student->status === 'non_active')
                <x-badge type="danger">Non-Aktif</x-badge>
            @else
                <x-badge type="success">Aktif</x-badge>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            @if($student->user_id)
                <x-badge type="success">Active</x-badge>
            @else
                <x-badge type="warning">Inactive</x-badge>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1">
            <form action="{{ route('students.toggle-status', $student) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" 
                    onclick="return confirm('Ubah status keaktifan mahasiswa {{ $student->name }} ({{ $student->status === 'leave' ? 'Aktifkan Kembali' : 'Set Cuti' }})?')"
                    class="inline-flex items-center justify-center p-1.5 {{ $student->status === 'leave' ? 'text-green-600 hover:bg-green-50' : 'text-amber-600 hover:bg-amber-50' }} rounded-lg transition-colors" 
                    title="{{ $student->status === 'leave' ? 'Aktifkan Kembali' : 'Set Cuti' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            </form>

            <a href="{{ route('students.show', $student) }}" class="inline-flex items-center justify-center p-1.5 text-primary-600 hover:text-primary-800 hover:bg-primary-50 rounded-lg transition-colors" title="Lihat Detail">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </a>
            <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center justify-center p-1.5 text-amber-500 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </a>
            <x-delete-modal
                :route="route('students.destroy', $student)"
                label="Delete Student"
                description="Are you sure you want to delete student {{ $student->name }}? This action cannot be undone."
                :iconOnly="true"
            />
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">No students found.</td>
    </tr>
@endforelse
