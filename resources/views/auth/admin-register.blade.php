<x-guest-layout>
    <div class="relative min-h-screen flex items-center justify-center">
    
        <a href="{{ url()->previous() }}" 
           class="absolute top-4 left-4 flex items-center p-2 z-50 bg-white rounded-full hover:bg-gray-100">
            <span class="material-symbols-outlined text-gray-600">
                arrow_back_ios
            </span>
        </a>

        <img src="{{ asset('assets/images/reg-bg.png') }}" 
             class="absolute bottom-0 left-0 w-100 h-auto">

        <form method="POST" action="{{ url('/admin/register') }}"
              class="form-container bg-white p-6 rounded-lg relative z-10">
            @csrf

            <div class="flex justify-center mb-4">
                <img src="{{ asset('assets/images/reg-loho.png') }}" 
                     class="h-auto w-25" alt="Admin Registration Logo">
            </div>

            <div class="text-center w-full">
                <h1>Register Admin Account</h1>
                <p class="text-gray-600 mb-4">
                    Restricted access — admin only
                </p>
            </div>

            <div>
                <x-input-label for="name" value="Admin Name" />
                <x-text-input id="name" class="block mt-1 w-full"
                    type="text" name="name" required autofocus />
            </div>

            <div class="mt-4">
                <x-input-label for="email" value="Admin Email" />
                <x-text-input id="email" class="block mt-1 w-full"
                    type="email" name="email" required />
            </div>

            <div class="mt-4">
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" class="block mt-1 w-full"
                    type="password" name="password" required />
            </div>

            <div class="mt-4">
                <x-input-label for="password_confirmation" value="Confirm Password" />
                <x-text-input id="password_confirmation"
                    class="block mt-1 w-full"
                    type="password" name="password_confirmation" required />
            </div>

            <div class="mt-4">
                <x-input-label for="admin_key" value="Admin Secret Key" />
                <x-text-input id="admin_key"
                    class="block mt-1 w-full"
                    type="password"
                    name="admin_key"
                    required />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button
                    style="background-color: #0575E6; color: white; width: 100%;">
                    Register Admin
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>

