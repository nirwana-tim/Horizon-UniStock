<x-app-layout>

    <x-page-header title="Edit Akun" subtitle="Perbarui data dan akses akun.">
        <x-slot name="actions">
            <a href="{{ route('admin.user.index') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-lg text-sm font-medium transition-colors">
                Kembali
            </a>
        </x-slot>
    </x-page-header>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            @if ($errors->any())
                <x-alert type="error" class="mb-5">Periksa kembali isian di bawah.</x-alert>
            @endif

            <form action="{{ route('admin.user.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <x-input-label for="name" :value="__('Nama Lengkap')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name', $user->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                            :value="old('email', $user->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="role" :value="__('Role')" />
                        <select id="role" name="role"
                            class="mt-1 block w-full bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition-colors">
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" {{ $user->hasRole($role) ? 'selected' : '' }}>
                                    {{ $role === 'admin' ? 'Admin (Finance)' : 'Staff' }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="password" :value="__('Password (opsional)')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                                autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>

                    <p class="text-xs text-gray-500">
                        Kosongkan password jika tidak ingin mengubahnya. Jika diisi, pengguna wajib mengganti password saat login berikutnya.
                    </p>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <x-primary-button>Simpan Perubahan</x-primary-button>
                    <a href="{{ route('admin.user.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                        Batal
                    </a>
                </div>
            </form>

            @if ($user->id !== auth()->id())
                <div class="mt-6 pt-5 border-t border-gray-100">
                    <form method="POST" action="{{ route('admin.user.active', $user) }}" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="active" value="{{ $user->is_active ? '0' : '1' }}">
                        @if ($user->is_active)
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white hover:bg-red-700 rounded-lg text-sm font-medium transition-colors">
                                Nonaktifkan Akun
                            </button>
                        @else
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white hover:bg-green-700 rounded-lg text-sm font-medium transition-colors">
                                Aktifkan Akun
                            </button>
                        @endif
                    </form>
                </div>
            @else
                <p class="mt-6 pt-5 border-t border-gray-100 text-xs text-gray-500">
                    Anda tidak dapat menonaktifkan akun sendiri.
                </p>
            @endif
        </div>
    </div>

</x-app-layout>