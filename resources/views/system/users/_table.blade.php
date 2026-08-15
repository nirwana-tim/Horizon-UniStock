@forelse($users as $user)
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
            {{ $user->name }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
        <td class="px-6 py-4 whitespace-nowrap">
@if ($user->hasRole('super_admin'))
                <x-badge type="danger">Super Admin</x-badge>
            @elseif ($user->hasRole('admin'))
                <x-badge type="primary">Admin Finance</x-badge>
            @elseif ($user->hasRole('staff'))
                <x-badge type="neutral">Staff</x-badge>
            @else
                <x-badge type="neutral">{{ ucfirst($user->roles->first()->name ?? 'Mahasiswa') }}</x-badge>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            @if ($user->is_active)
                <x-badge type="success">Aktif</x-badge>
            @else
                <x-badge type="neutral">Nonaktif</x-badge>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-1">
            <a href="{{ route('admin.user.edit', $user) }}"
                class="inline-flex items-center justify-center p-1.5 text-primary-600 hover:text-primary-800 hover:bg-primary-50 rounded-lg transition-colors"
                title="Edit Akun">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            @if ($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.user.active', $user) }}" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="active" value="{{ $user->is_active ? '0' : '1' }}">
                    <button type="submit"
                        class="inline-flex items-center justify-center p-1.5 {{ $user->is_active ? 'text-red-600 hover:text-red-800 hover:bg-red-50' : 'text-green-600 hover:text-green-800 hover:bg-green-50' }} rounded-lg transition-colors"
                        title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                        @if ($user->is_active)
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        @endif
                    </button>
                </form>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-6 py-8">
            <x-empty-state title="Belum ada akun" description="Buat akun admin (Finance) atau staff untuk mulai." />
        </td>
    </tr>
@endforelse