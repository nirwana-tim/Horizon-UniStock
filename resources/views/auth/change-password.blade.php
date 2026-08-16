<x-guest-layout>
    <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-md">
        <p class="text-sm text-amber-700">
            <strong>Warning:</strong> You must change your password before accessing the system.
        </p>
    </div>

    <x-alert type="error">{{ session('error') }}</x-alert>

    <form method="POST" action="{{ route('password.change.store') }}" class="space-y-4">
        @csrf

        <div x-data="{ show: false }">
            <label for="current_password" class="block font-label-md text-label-md text-secondary/70 mb-1.5">
                Current Password <span class="text-error">*</span>
            </label>
            <div class="relative">
                <input id="current_password" :type="show ? 'text' : 'password'" name="current_password" required autofocus
                    placeholder="Masukkan password saat ini"
                    class="w-full h-12 px-4 pr-12 rounded-xl bg-surface-container-low border border-black/[0.06] font-body-md text-on-surface placeholder-secondary/40 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors @error('current_password') border-error focus:border-error focus:ring-error @enderror">
                <button type="button" @click="show = !show"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary/40 hover:text-secondary transition-colors">
                    <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.879L21 21"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div x-data="{ show: false }">
            <label for="password" class="block font-label-md text-label-md text-secondary/70 mb-1.5">
                New Password <span class="text-error">*</span>
            </label>
            <div class="relative">
                <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password"
                    placeholder="Masukkan password baru"
                    class="w-full h-12 px-4 pr-12 rounded-xl bg-surface-container-low border border-black/[0.06] font-body-md text-on-surface placeholder-secondary/40 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors @error('password') border-error focus:border-error focus:ring-error @enderror">
                <button type="button" @click="show = !show"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary/40 hover:text-secondary transition-colors">
                    <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.879L21 21"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div x-data="{ show: false }">
            <label for="password_confirmation" class="block font-label-md text-label-md text-secondary/70 mb-1.5">
                Confirm New Password <span class="text-error">*</span>
            </label>
            <div class="relative">
                <input id="password_confirmation" :type="show ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                    placeholder="Ulangi password baru"
                    class="w-full h-12 px-4 pr-12 rounded-xl bg-surface-container-low border border-black/[0.06] font-body-md text-on-surface placeholder-secondary/40 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors @error('password_confirmation') border-error focus:border-error focus:ring-error @enderror">
                <button type="button" @click="show = !show"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary/40 hover:text-secondary transition-colors">
                    <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.879L21 21"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Save New Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
