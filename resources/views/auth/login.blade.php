<x-guest-layout>
    {{-- Page title --}}
    <div class="mb-6">
        <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-on-surface">Masuk</h2>
        <p class="mt-1 font-body-md text-body-md text-secondary/60">Masukkan kredensial kamu untuk melanjutkan</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        {{-- NIM --}}
        <div>
            <label for="email" class="block font-label-md text-label-md text-secondary/70 mb-1.5">
                NIM <span class="text-error">*</span>
            </label>
            <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus
                autocomplete="username" placeholder="Masukkan NIM"
                class="w-full h-12 px-4 rounded-xl bg-surface-container-low border border-black/[0.06] font-body-md text-on-surface placeholder-secondary/40 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors @error('email') border-error focus:border-error focus:ring-error @enderror">
            @error('email')
                <p class="mt-1 text-xs text-error flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">error</span>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div x-data="{ show: false }">
            <label for="password" class="block font-label-md text-label-md text-secondary/70 mb-1.5">
                Password <span class="text-error">*</span>
            </label>
            <div class="relative">
                <input id="password" :type="show ? 'text' : 'password'" name="password" required
                    autocomplete="current-password" placeholder="Masukkan password"
                    class="w-full h-12 px-4 pr-12 rounded-xl bg-surface-container-low border border-black/[0.06] font-body-md text-on-surface placeholder-secondary/40 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors @error('password') border-error focus:border-error focus:ring-error @enderror">
                <button type="button" @click="show = !show"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary/40 hover:text-secondary transition-colors">
                    {{-- Eye icon (show password) --}}
                    <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{-- Eye-off icon (hide password) --}}
                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.879L21 21"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-xs text-error flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">error</span>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- CAPTCHA --}}
        <div>
            <label class="block font-label-md text-label-md text-secondary/70 mb-1.5">
                Verifikasi <span class="text-error">*</span>
            </label>
            <div class="flex items-center gap-3 mb-2">
                <img src="{{ captcha_src('math') }}" alt="captcha" id="captcha-img"
                     class="rounded-xl border border-black/[0.06] h-12">
                <button type="button" onclick="document.getElementById('captcha-img').src='{{ captcha_src('math') }}&'+Math.random()"
                        class="text-secondary/40 hover:text-secondary transition-colors flex-shrink-0"
                        title="Reload CAPTCHA">
                    <span class="material-symbols-outlined text-xl">refresh</span>
                </button>
            </div>
            <input type="text" name="captcha" placeholder="Hasil penjumlahan"
                   class="w-full h-12 px-4 rounded-xl bg-surface-container-low border border-black/[0.06] font-body-md text-on-surface placeholder-secondary/40 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors @error('captcha') border-error focus:border-error focus:ring-error @enderror">
            @error('captcha')
                <p class="mt-1 text-xs text-error flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">error</span>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center gap-2 cursor-pointer">
            <input id="remember_me" type="checkbox" name="remember"
                class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
            <label for="remember_me" class="font-body-md text-body-md text-secondary/70 cursor-pointer">Ingat saya</label>
        </div>

        {{-- Submit --}}
        <button type="submit" id="btn-login"
            class="w-full h-12 bg-primary text-white rounded-full font-headline-sm text-headline-sm shadow-button active:scale-[0.98] transition-transform duration-200 mt-2">
            Masuk
        </button>

        {{-- Lupa Password --}}
        @if (Route::has('password.request'))
            <div class="text-center pt-2">
                <p class="font-body-md text-body-md text-secondary/50">
                    Lupa password?
                    <a href="{{ route('password.request') }}" class="text-primary font-semibold hover:underline">Klik di sini</a>
                </p>
            </div>
        @endif
    </form>

@push('scripts')
<script>
    setInterval(function () {
        document.getElementById('captcha-img').src =
            '{{ captcha_src("math") }}&' + Date.now();
    }, 90000);
</script>
@endpush
</x-guest-layout>
