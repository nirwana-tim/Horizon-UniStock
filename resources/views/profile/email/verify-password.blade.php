<x-app-layout>
    <div class="mb-5">
        <h2 class="text-lg font-bold text-gray-800">Ubah Email Pribadi</h2>
        <p class="text-xs text-gray-500 mt-0.5">Konfirmasi password untuk melanjutkan</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <form method="POST" action="{{ route('profile.email.verify-password') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="password" :value="__('Password Saat Ini')" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="current-password" />
                <x-input-error class="mt-2" :messages="$errors->get('password')" />
            </div>

            <div class="flex items-center gap-3">
                <x-primary-button>Lanjutkan</x-primary-button>
                <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
