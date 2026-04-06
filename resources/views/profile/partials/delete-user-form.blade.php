@php
    $firstAdmin = \App\Models\User::where('role', 'admin')->orderBy('created_at')->first();
    $isFirstAdmin = auth()->user()->id === $firstAdmin->id;
@endphp

@if(session('status'))
    <div
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 5000)"
        class="p-4 mb-4 text-sm text-blue-800 bg-blue-100 rounded-lg dark:bg-blue-900 dark:text-blue-200"
    >
        {{ session('status') }}
    </div>
@endif

<section class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">

    <header>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please download any important information before proceeding.') }}
        </p>
    </header>

    <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
    @if($isFirstAdmin)
        <div class="p-4 mt-5 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-200">
            Oops! You cannot delete your account. You are the super admin, the brightest among the stars. 🌟
        </div>
    @else
        <x-danger-button
            x-data
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="normal-case text-sm mt-5 whitespace-nowrap inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition shadow font-semibold"
        >
            {{ __('Delete Account') }}
        </x-danger-button>
    @endif
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post"
              action="{{ route('profile.destroy') }}"
              class="p-6 space-y-6 bg-white dark:bg-gray-800 rounded-lg">

            @csrf
            @method('delete')

            <header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('Confirm Account Deletion') }}
                </h2>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    {{ __('Please enter your password to permanently delete your account. This action cannot be undone.') }}
                </p>
            </header>

            <div>
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-2 block w-full
                           bg-white dark:bg-gray-700
                           text-gray-900 dark:text-gray-200
                           border-gray-300 dark:border-gray-600
                           focus:ring-red-500 focus:border-red-500"
                    placeholder="{{ __('Enter your password') }}"
                />

                <x-input-error
                    :messages="$errors->userDeletion->get('password')"
                    class="mt-2 text-red-600 dark:text-red-400"
                />
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                <x-secondary-button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500"
                >
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button>
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>

        </form>
    </x-modal>

</section>
