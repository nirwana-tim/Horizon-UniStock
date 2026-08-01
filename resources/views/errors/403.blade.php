<x-app-layout>
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-primary-700">403</h1>
            <p class="mt-4 text-lg text-gray-600">{{ $message ?? 'Akses ditolak' }}</p>
            <a href="{{ route('dashboard') }}" class="mt-6 inline-block px-4 py-2 bg-primary-700 text-white rounded-lg hover:bg-primary-800">Kembali ke Dashboard</a>
        </div>
    </div>
</x-app-layout>
