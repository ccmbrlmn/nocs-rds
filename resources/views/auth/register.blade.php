<x-guest-layout>
    <div class="relative min-h-screen bg-white dark:bg-gray-900">

        <a href="{{ url()->previous() }}"
           class="absolute top-4 left-4 px-4 py-2 rounded-xl text-sm font-medium transition
                  bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200
                  hover:bg-blue-100 dark:hover:bg-gray-600 shadow-sm flex items-center gap-2 z-20">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Back
        </a>

        <img src="{{ asset('assets/images/reg-bg.png') }}"
             class="absolute bottom-0 left-0 w-100 h-auto opacity-100 dark:opacity-30">

        <div class="flex items-center justify-center min-h-screen">
            <form method="POST" action="{{ route('register') }}"
                  class="form-container bg-white dark:bg-gray-800 p-6 rounded-lg relative z-10 w-full max-w-md shadow-lg">

                @csrf

                <div class="flex justify-center mb-4">
                    <img src="{{ asset('assets/images/reg-loho.png') }}" class="h-auto w-25" alt="Registration Logo">
                </div>

                <div class="text-center w-full">
                    <h1 class="text-gray-800 dark:text-gray-100">Create an Account</h1>
                    <p class="text-gray-600 dark:text-gray-300 mb-4"></p>
                </div>

                <div>
                    <x-input-label for="name" :value="__('Full Name')" class="dark:text-gray-200" />
                    <x-text-input id="name" class="block mt-1 w-full dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" class="dark:text-gray-200" />
                    <x-text-input id="email" class="block mt-1 w-full dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Please use your <strong>gbox account</strong></p>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="office" :value="__('Office')" class="dark:text-gray-200" />
                    <x-text-input
                        id="office"
                        class="block mt-1 w-full dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                        type="text"
                        name="office"
                        :value="old('office')"
                        required
                        placeholder="Enter your office"
                    />
                    <x-input-error :messages="$errors->get('office')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" class="dark:text-gray-200" />
                    <x-text-input id="password" class="block mt-1 w-full dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="dark:text-gray-200" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button style="background-color: #0575E6; color: white; width: 100%;">
                        {{ __('Register') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
