<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Enter your NIM. We will send a password reset link to your campus email.
    </div>

    <form method="POST" action="{{ route('password.student.send-reset') }}">
        @csrf

        <div>
            <x-input-label for="nim" :value="__('NIM')" />
            <x-text-input id="nim" class="block mt-1 w-full" type="text" name="nim" :value="old('nim')" required autofocus placeholder="Enter NIM" />
            <x-input-error :messages="$errors->get('nim')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md" href="{{ route('login') }}">
                Back to Login
            </a>

            <x-primary-button class="ms-3">
                {{ __('Send Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
