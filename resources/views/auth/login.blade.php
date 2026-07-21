<x-guest-layout>

    {{-- Page title --}}
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Sign In</h2>
        <p class="mt-1 text-sm text-gray-500">Enter your credentials to continue</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email / NIM -->
        <div>
            <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5">
                Email / NIM <span class="text-red-500">*</span>
            </label>
            <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus
                autocomplete="username"                 placeholder="Email or student NIM"
                class="w-full px-3 py-2.5 h-11 text-sm
                          bg-gray-100 border border-gray-200 rounded-lg
                          text-gray-800 placeholder-gray-400
                          focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100
                          transition-colors duration-150
                          @error('email') border-red-400 focus:border-red-400 focus:ring-red-100 @enderror">
            <p class="mt-1.5 text-xs text-gray-400">
                Student: use NIM &bull; Staff/Admin: use email
            </p>
            @error('email')
                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">
                Password <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input id="password" :type="show ? 'text' : 'password'" name="password" required
                    autocomplete="current-password"                     placeholder="Enter password"
                    class="w-full px-3 py-2.5 h-11 pr-10 text-sm
                              bg-gray-100 border border-gray-200 rounded-lg
                              text-gray-800 placeholder-gray-400
                              focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100
                              transition-colors duration-150
                              @error('password') border-red-400 focus:border-red-400 focus:ring-red-100 @enderror">
                <button type="button" @click="show = !show"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- CAPTCHA -->
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                Verifikasi <span class="text-red-500">*</span>
            </label>
            <div class="flex items-center gap-3 mb-2">
                <img src="{{ captcha_src('math') }}" alt="captcha" id="captcha-img"
                     class="rounded-lg border border-gray-200 h-11">
                <button type="button" onclick="document.getElementById('captcha-img').src='{{ captcha_src('math') }}&'+Math.random()"
                        class="text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0"
                        title="Reload CAPTCHA">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
            <input type="text" name="captcha" placeholder="Hasil penjumlahan"
                   class="w-full px-3 py-2.5 h-11 text-sm bg-gray-100 border border-gray-200 rounded-lg text-gray-800 placeholder-gray-400 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition-colors duration-150 @error('captcha') border-red-400 focus:border-red-400 focus:ring-red-100 @enderror">
            @error('captcha')
                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                    class="w-4 h-4 text-primary-700 border-gray-300 rounded focus:ring-primary-500">
                <span class="text-sm text-gray-600">Remember me</span>
            </label>
        </div>

        <!-- Submit -->
        <button type="submit" id="btn-login"
            class="w-full h-11 bg-primary-700 text-white text-sm font-semibold rounded-lg
                       hover:bg-primary-800 active:bg-primary-900
                       focus:outline-none focus:ring-2 focus:ring-primary-300
                       transition-colors duration-150 mt-2">
            Sign In
        </button>

        <!-- Lupa Password -->
        <div class="text-center pt-2">
            <p class="text-sm text-gray-500">
                Forgot password?
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-primary-700 font-medium hover:text-primary-800">Staff/Admin</a>
                @endif
                @if (Route::has('password.request') && Route::has('password.student.forgot'))
                    <span class="text-gray-300 mx-1">|</span>
                @endif
                @if (Route::has('password.student.forgot'))
                    <a href="{{ route('password.student.forgot') }}"
                        class="text-primary-700 font-medium hover:text-primary-800">Student</a>
                @endif
            </p>
        </div>

    </form>

</x-guest-layout>
