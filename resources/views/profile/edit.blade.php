

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    {{-- Page Header --}}
    <div class="header-container flex items-center gap-5 text-white font-medium p-2 mt-8 mb-3">
        <div class="header">
            <h1 class="flex items-center gap-2 text-3xl">
                <span class="material-symbols-outlined text-2xl">person</span>
                @if(auth()->user()->role === 'admin')
                    Admin Profile Settings
                @else
                    User Profile Settings
                @endif
            </h1>
        </div>
    </div>

    <div class="py-8">
        <div class="profile-wrapper space-y-6">

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
<style>
.header-container {
    margin-left: 5rem;
    margin-right: 5rem;
}

.profile-wrapper {
    margin-left: 5rem;
    margin-right: 5rem;
}

.material-symbols-outlined {
    font-size: 28px;
    vertical-align: middle;
}
</style>

