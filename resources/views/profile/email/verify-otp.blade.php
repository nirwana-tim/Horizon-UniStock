<x-app-layout>
    <div class="mb-5">
        <h2 class="text-lg font-bold text-gray-800">Verifikasi Email Pribadi</h2>
        <p class="text-xs text-gray-500 mt-0.5">Masukkan kode OTP yang dikirim ke email baru Anda</p>
    </div>

    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        @if(!$email)
            <p class="text-sm text-gray-500">Sesi tidak valid. Silakan mulai ulang.</p>
            <a href="{{ route('profile.email.change') }}" class="mt-3 inline-flex text-sm text-primary-700 hover:underline">Mulai Ulang</a>
        @else
            <div class="mb-4 text-sm text-gray-600">
                Kode OTP telah dikirim ke <strong>{{ $email }}</strong>
            </div>

            <form method="POST" action="{{ route('profile.email.verify-otp') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="code" :value="__('Kode OTP')" />
                    <x-text-input id="code" name="code" type="text" class="mt-1 block w-full text-center text-2xl tracking-[0.5em]" maxlength="6" required autofocus placeholder="000000" />
                    <x-input-error class="mt-2" :messages="$errors->get('code')" />
                </div>

                <div class="flex items-center gap-3">
                    <x-primary-button>Verifikasi</x-primary-button>
                    <a href="{{ route('profile.email.change') }}" class="text-sm text-gray-600 hover:text-gray-900">Kirim Ulang OTP</a>
                </div>
            </form>
        @endif
    </div>
</x-app-layout>
