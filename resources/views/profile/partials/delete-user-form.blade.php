<section class="space-y-6">
    <header>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please download any important information before proceeding.') }}
        </p>
    </header>

    <div class="pt-3">
        <x-danger-button
            x-data
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >
            {{ __('Delete Account') }}
        </x-danger-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post"
              action="{{ route('profile.destroy') }}"
              class="p-6 space-y-6">

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
                    class="mt-2 block w-full"
                    placeholder="{{ __('Enter your password') }}"
                />

                <x-input-error
                    :messages="$errors->userDeletion->get('password')"
                    class="mt-2"
                />
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button
                    type="button"
                    x-on:click="$dispatch('close')"
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

