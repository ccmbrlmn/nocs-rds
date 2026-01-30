<x-guest-layout>
   <div class = "min-h-screen">
   <div class="flex w-full h-full">
       <div class="left-column relative">
            <img src="{{ asset('assets/images/bg-login.png') }}" alt="Background Image" class="image">
            <img src="{{ asset('assets/images/system-logo.png') }}" alt="System Login" class="centered-image">
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <form method="POST" action="{{ route('login') }}" class="auth-container">
                @csrf

                <h1>Hello Again!</h1>
                <p class="text-gray-600 mb-4">Welcome Back!</p>

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                
                <div class="flex justify-between items-center mt-2 text-sm">
                    @if (Route::has('password.request'))
                        <a class="underline text-gray-600 hover:text-gray-900"
                           href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    @if (Route::has('register'))
                        <a class="underline text-gray-600 hover:text-gray-900"
                           href="{{ route('register') }}">
                            {{ __('Register') }}
                        </a>
                    @endif
                </div>

                
                <div class ="mt-4 w-full">
                <x-primary-button style="background-color: #0575E6; color: white; width: 100%;">
                    {{ __('Log in') }}
                </x-primary-button>




                </div>

            </form>
        </div>
    </div>
   </div>

 
</x-guest-layout>
