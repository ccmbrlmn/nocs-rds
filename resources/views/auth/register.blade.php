<x-guest-layout>
    <div class="relative min-h-screen flex items-center justify-center">
        
    <a href="{{ url()->previous() }}" 
        class="absolute top-4 left-4 flex items-center p-2 z-50 bg-white rounded-full  hover:bg-gray-100">
        <span class="material-symbols-outlined text-gray-600">
            arrow_back_ios
        </span> 
    </a>

        <img src="{{ asset('assets/images/reg-bg.png') }}" 
             class="absolute bottom-0 left-0 w-100 h-auto ">


        <form method="POST" action="{{ route('register') }}" class="form-container bg-white p-6 rounded-lg relative z-10">
            @csrf

            
            <div class="flex justify-center mb-4">
                <img src="{{ asset('assets/images/reg-loho.png') }}" class="h-auto w-25" alt="Registration Logo">
            </div>


            <div class="text-center w-full">
                <h1>Create an Organization Account</h1>
                <p class="text-gray-600 mb-4"></p>
            </div>
            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name of the Organization')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
            

                <x-primary-button style="background-color: #0575E6; color: white; width: 100%;">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
