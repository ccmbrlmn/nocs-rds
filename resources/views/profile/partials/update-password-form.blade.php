<section class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">

    <header>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Use a strong password to keep your account secure.') }}
        </p>
    </header>

    <form
        method="post"
        action="{{ route('password.update') }}"
        class="space-y-6"
        x-data="passwordChecker()"
        @keydown.enter.prevent
        @input="dirty = true"
    >
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />

            <x-text-input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="mt-2 block w-full
                       bg-white dark:bg-gray-700
                       text-gray-900 dark:text-gray-200
                       border-gray-300 dark:border-gray-600
                       focus:ring-indigo-500 focus:border-indigo-500"
                autocomplete="current-password"
            />

            <x-input-error
                :messages="$errors->updatePassword->get('current_password')"
                class="mt-2 text-red-600 dark:text-red-400"
            />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />

            <x-text-input
                id="update_password_password"
                name="password"
                type="password"
                class="mt-2 block w-full
                       bg-white dark:bg-gray-700
                       text-gray-900 dark:text-gray-200
                       border-gray-300 dark:border-gray-600
                       focus:ring-indigo-500 focus:border-indigo-500"
                autocomplete="new-password"
                x-model="password"
                @input="checkStrength"
            />

            <div
                x-show="password.length > 0"
                x-transition.opacity
                class="mt-3 space-y-1 text-sm"
            >

                <p class="flex items-center gap-2"
                   :class="rules.length ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'">
                    <span class="material-symbols-outlined text-base">check_circle</span>
                    Minimum 8 characters
                </p>

                <p class="flex items-center gap-2"
                   :class="rules.upper ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'">
                    <span class="material-symbols-outlined text-base">check_circle</span>
                    Uppercase letter (A–Z)
                </p>

                <p class="flex items-center gap-2"
                   :class="rules.lower ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'">
                    <span class="material-symbols-outlined text-base">check_circle</span>
                    Lowercase letter (a–z)
                </p>

                <p class="flex items-center gap-2"
                   :class="rules.number ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'">
                    <span class="material-symbols-outlined text-base">check_circle</span>
                    Number (0–9)
                </p>

                <p class="flex items-center gap-2"
                   :class="rules.special ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'">
                    <span class="material-symbols-outlined text-base">check_circle</span>
                    Special character (!@#$%^&*)
                </p>

            </div>

            <x-input-error
                :messages="$errors->updatePassword->get('password')"
                class="mt-2 text-red-600 dark:text-red-400"
            />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />

            <x-text-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="mt-2 block w-full
                       bg-white dark:bg-gray-700
                       text-gray-900 dark:text-gray-200
                       border-gray-300 dark:border-gray-600
                       focus:ring-indigo-500 focus:border-indigo-500"
                autocomplete="new-password"
            />

            <x-input-error
                :messages="$errors->updatePassword->get('password_confirmation')"
                class="mt-2 text-red-600 dark:text-red-400"
            />
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">

            <div>
                @if (session('status') === 'password-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition.opacity
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm font-medium text-green-600 dark:text-green-400"
                    >
                        {{ __('Password updated successfully.') }}
                    </p>
                @endif
            </div>

            <button
                type="submit"
                class="inline-flex items-center px-6 py-2.5
                       bg-indigo-600 dark:bg-blue-900
                       border border-transparent rounded-lg
                       font-semibold text-xs text-white uppercase tracking-widest
                       hover:bg-indigo-700 dark:hover:bg-indigo-400
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                       disabled:opacity-40 disabled:cursor-not-allowed transition"
                :disabled="!dirty || !valid"
            >
                {{ __('Save Changes') }}
            </button>

        </div>

    </form>
</section>
