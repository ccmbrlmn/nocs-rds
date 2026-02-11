<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="header-container flex items-center gap-4 py-6 px-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <span class="material-symbols-outlined text-2xl text-blue-500 dark:text-blue-400">person</span>
            @if(auth()->user()->role === 'admin')
                Admin Profile Settings
            @else
                User Profile Settings
            @endif
        </h1>
    </div>

    {{-- Tabs Wrapper --}}
    <div class="max-w-3xl mx-auto mt-8">
        <div x-data="{ tab: 'profile' }"
             class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 md:p-8">

            <!-- Tabs -->
            <nav class="flex gap-4 border-b border-gray-200 dark:border-gray-700 mb-6">
                <button
                    @click="tab = 'profile'"
                    :class="tab === 'profile'
                        ? 'border-b-2 border-blue-500 font-semibold text-blue-500'
                        : 'text-gray-500 dark:text-gray-400'"
                    class="pb-2 px-4 flex items-center gap-1">
                    <span class="material-symbols-outlined text-blue-500 dark:text-blue-400 text-lg">badge</span>
                    Profile
                </button>

                <button
                    @click="tab = 'password'"
                    :class="tab === 'password'
                        ? 'border-b-2 border-red-500 font-semibold text-red-500'
                        : 'text-gray-500 dark:text-gray-400'"
                    class="pb-2 px-4 flex items-center gap-1">
                    <span class="material-symbols-outlined text-red-500 dark:text-red-400 text-lg">lock</span>
                    Password
                </button>

                <button
                    @click="tab = 'delete'"
                    :class="tab === 'delete'
                        ? 'border-b-2 border-gray-600 font-semibold text-gray-600 dark:text-gray-400'
                        : 'text-gray-500 dark:text-gray-400'"
                    class="pb-2 px-4 flex items-center gap-1">
                    <span class="material-symbols-outlined text-gray-600 dark:text-gray-400 text-lg">delete</span>
                    Delete Account
                </button>
            </nav>

            <div class="relative">

                <div x-show="tab === 'profile'" x-cloak>
                    @include('profile.partials.update-profile-information-form')
                </div>

                <div x-show="tab === 'password'" x-cloak>
                    @include('profile.partials.update-password-form')
                </div>

                <div x-show="tab === 'delete'" x-cloak>
                    @include('profile.partials.delete-user-form')
                </div>

            </div>

        </div>
    </div>
</x-app-layout>

<style>
[x-cloak] { display: none !important; }

.header-container {
    margin-left: 1.5rem;
    margin-right: 1.5rem;
}

section {
    padding: 1.5rem;
    background-color: #ffffff;
    border-radius: 0.75rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

@media (max-width: 768px) {
    nav {
        flex-direction: column;
        gap: 2px;
    }

    nav button {
        width: 100%;
        text-align: left;
        padding-left: 1rem;
    }
}
</style>

