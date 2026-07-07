<x-guest-layout>
    <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-md">
        <p class="text-sm text-amber-700">
            <strong>Warning:</strong> You must change your password before accessing the system.
        </p>
    </div>

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.change.store') }}">
        @csrf

        <div>
            <x-input-label for="current_password" :value="__('Current Password')" />
            <x-text-input id="current_password" class="block mt-1 w-full" type="password" name="current_password" required autofocus />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('New Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Save New Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
