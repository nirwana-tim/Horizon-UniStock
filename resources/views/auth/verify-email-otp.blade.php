<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Masukkan kode OTP 6 digit yang telah dikirim ke <strong>{{ $email }}</strong>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('student.email.verify-otp') }}">
        @csrf

        <div>
            <x-input-label for="code" :value="__('Kode OTP')" />
            <x-text-input id="code" class="block mt-1 w-full text-center text-2xl tracking-[0.5em]" type="text" name="code" maxlength="6" required autofocus placeholder="000000" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md" href="{{ route('dashboard') }}">
                Lewati
            </a>

            <x-primary-button class="ms-3">
                {{ __('Verifikasi') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
