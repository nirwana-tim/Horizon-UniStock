<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Enter the 6-digit OTP code sent to <strong>{{ $email }}</strong>
    </div>

    <x-alert type="success">{{ session('success') }}</x-alert>

    <form method="POST" action="{{ route('student.email.verify-otp') }}">
        @csrf

        <div>
            <x-input-label for="code" :value="__('OTP Code')" />
            <x-text-input id="code" class="block mt-1 w-full text-center text-2xl tracking-[0.5em]" type="text" name="code" maxlength="6" required autofocus placeholder="000000" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md" href="{{ route('dashboard') }}">
                Skip
            </a>

            <x-primary-button class="ms-3">
                {{ __('Verify') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
