<x-app-layout>

    <x-page-header title="Tambah Akun" subtitle="Buat akun admin (Finance) atau staff.">
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

            <form action="{{ route('admin.user.store') }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <div>
                        <x-input-label for="name" :value="__('Nama Lengkap')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name')" placeholder="Mis. Budi Santoso" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                            :value="old('email')" placeholder="nama@perguruan.ac.id" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="role" :value="__('Role')" />
                        <select id="role" name="role"
                            class="mt-1 block w-full bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition-colors">
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" {{ old('role', 'staff') === $role ? 'selected' : '' }}>
                                    {{ $role === 'admin' ? 'Admin (Finance)' : 'Staff' }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <x-password-input id="password" name="password" class="mt-1 block w-full"
                                autocomplete="new-password" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                            <x-password-input id="password_confirmation" name="password_confirmation"
                                class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>

                    <p class="text-xs text-gray-500">
                        Pengguna diwajibkan mengganti password saat login pertama kali.
                    </p>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <x-primary-button>Simpan Akun</x-primary-button>
                    <a href="{{ route('admin.user.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>