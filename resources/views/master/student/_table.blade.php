@forelse($students as $index => $student)
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $students->firstItem() + $index }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $student->nim }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->name }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->studyProgram->name ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->programLevel->name ?? '-' }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $student->student_type === 'freshman' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst($student->student_type) }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            @if($student->user_id)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Inactive</span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1.5">
            <a href="{{ route('students.show', $student) }}" class="inline-flex items-center px-2.5 py-1 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">View</a>
            <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center px-2.5 py-1 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">Edit</a>
            <x-delete-modal
                :route="route('students.destroy', $student)"
                label="Delete Student"
                description="Are you sure you want to delete student {{ $student->name }}? This action cannot be undone."
            />
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No student data found.</td>
    </tr>
@endforelse

