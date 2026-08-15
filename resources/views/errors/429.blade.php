<x-app-layout>
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-amber-500">429</h1>
            <p class="mt-4 text-lg text-gray-600">Terlalu banyak permintaan</p>
            <p class="mt-2 text-sm text-gray-500">Anda melakukan terlalu banyak permintaan dalam waktu singkat. Silakan tunggu beberapa saat sebelum mencoba lagi.</p>
            <a href="{{ route('dashboard') }}" class="mt-6 inline-block px-4 py-2 bg-primary-700 text-white rounded-lg hover:bg-primary-800">Kembali ke Dashboard</a>
        </div>
    </div>
</x-app-layout>
