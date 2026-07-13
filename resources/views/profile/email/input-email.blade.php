<x-app-layout>
    <div class="mb-5">
        <h2 class="text-lg font-bold text-gray-800">Ubah Email Pribadi</h2>
        <p class="text-xs text-gray-500 mt-0.5">Masukkan alamat email pribadi baru</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <form method="POST" action="{{ route('profile.email.send-otp') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="email_pribadi" :value="__('Email Pribadi Baru')" />
                <p class="text-xs text-gray-500 mb-1">Kode OTP akan dikirim ke email ini untuk verifikasi.</p>
                <x-text-input id="email_pribadi" name="email_pribadi" type="email" class="mt-1 block w-full" :value="old('email_pribadi')" required autocomplete="email" placeholder="nama@example.com" />
                <x-input-error class="mt-2" :messages="$errors->get('email_pribadi')" />
            </div>

            <div class="flex items-center gap-3">
                <x-primary-button>Kirim OTP</x-primary-button>
                <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
