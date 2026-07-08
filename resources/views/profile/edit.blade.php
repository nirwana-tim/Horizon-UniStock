<x-app-layout>
    <div class="mb-5">
        <h2 class="text-lg font-bold text-gray-800">Profil</h2>
        <p class="text-xs text-gray-500 mt-0.5">Kelola informasi akun kamu</p>
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            @include('profile.partials.update-password-form')
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
