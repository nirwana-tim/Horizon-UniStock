<x-guest-layout>
    {{-- Page title --}}
    <div class="mb-6">
        <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-on-surface">Masuk</h2>
        <p class="mt-1 font-body-md text-body-md text-secondary/60">Masukkan kredensial kamu untuk melanjutkan</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        {{-- Email / NIM --}}
        <div>
            <label for="email" class="block font-label-md text-label-md text-secondary/70 mb-1.5">
                Email / NIM <span class="text-error">*</span>
            </label>
            <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus
                autocomplete="username" placeholder="Email atau NIM"
                class="w-full h-12 px-4 rounded-xl bg-surface-container-low border border-black/[0.06] font-body-md text-on-surface placeholder-secondary/40 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors @error('email') border-error focus:border-error focus:ring-error @enderror">
            <p class="mt-1 font-body-md text-[12px] text-secondary/40">Student: pakai NIM &bull; Staff/Admin: pakai email</p>
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
                    <span class="material-symbols-outlined" x-text="show ? 'visibility_off' : 'visibility'">visibility</span>
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
        <div class="text-center pt-2">
            <p class="font-body-md text-body-md text-secondary/50">
                Lupa password?
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-primary font-semibold hover:underline">Staff/Admin</a>
                @endif
                @if (Route::has('password.request') && Route::has('password.student.forgot'))
                    <span class="mx-1">•</span>
                @endif
                @if (Route::has('password.student.forgot'))
                    <a href="{{ route('password.student.forgot') }}" class="text-primary font-semibold hover:underline">Student</a>
                @endif
            </p>
        </div>
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
