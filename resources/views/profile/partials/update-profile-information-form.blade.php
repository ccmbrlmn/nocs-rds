<section class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">

    <header>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            @if(auth()->user()->role === 'admin')
                {{ __('Admin Profile Information') }}
            @else
                {{ __('User Profile Information') }}
            @endif
        </h2>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form
        method="post"
        action="{{ route('profile.update') }}"
        class="space-y-6"
        x-data="{ dirty: false }"
        @keydown.enter.prevent
        @input="dirty = true"
    >
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />

            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-2 block w-full
                       bg-white dark:bg-gray-700
                       text-gray-900 dark:text-gray-200
                       border-gray-300 dark:border-gray-600
                       focus:ring-indigo-500 focus:border-indigo-500"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
            />

            <x-input-error class="mt-2 text-red-600 dark:text-red-400" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-2 block w-full
                       bg-white dark:bg-gray-700
                       text-gray-900 dark:text-gray-200
                       border-gray-300 dark:border-gray-600
                       focus:ring-indigo-500 focus:border-indigo-500"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
            />

            <x-input-error class="mt-2 text-red-600 dark:text-red-400" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 space-y-2">
                    <p class="text-sm text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button
                            form="send-verification"
                            class="ml-1 underline text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300"
                        >
                            {{ __('Resend verification email') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">

            <div>
                @if (session('status') === 'profile-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition.opacity
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm font-medium text-green-600 dark:text-green-400"
                    >
                        {{ __('Saved successfully.') }}
                    </p>
                @endif
            </div>

            <button
                type="submit"
                class="inline-flex items-center px-6 py-2.5
                       bg-indigo-600 dark:bg-blue-500
                       border border-transparent rounded-lg
                       font-semibold text-xs text-white uppercase tracking-widest
                       hover:bg-indigo-700 dark:hover:bg-blue-900
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                       disabled:opacity-40 disabled:cursor-not-allowed transition"
                x-bind:disabled="!dirty"
            >
                {{ __('Save Changes') }}
            </button>

        </div>

    </form>
