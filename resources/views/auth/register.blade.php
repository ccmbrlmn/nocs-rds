<x-guest-layout>
    <div class="relative min-h-screen flex items-center justify-center">
        
    <a href="{{ url()->previous() }}" 
       class="px-4 py-2 rounded-xl text-sm font-medium transition 
              bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 
              hover:bg-blue-100 dark:hover:bg-gray-600 shadow-sm flex items-center gap-2">

        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Back
    </a>

        <img src="{{ asset('assets/images/reg-bg.png') }}" 
             class="absolute bottom-0 left-0 w-100 h-auto ">
        <form method="POST" action="{{ route('register') }}" class="form-container bg-white p-6 rounded-lg relative z-10">
            @csrf
            <div class="flex justify-center mb-4">
                <img src="{{ asset('assets/images/reg-loho.png') }}" class="h-auto w-25" alt="Registration Logo">
            </div>

            <div class="text-center w-full">
                <h1>Create an Account</h1>
                <p class="text-gray-600 mb-4"></p>
            </div>

            <div>
                <x-input-label for="name" :value="__('Full Name')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <p class="text-gray-500 text-sm mt-1">Please use your <strong>gbox account</strong></p>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
    
<div class="mt-4">
    <x-input-label for="department" :value="__('Department')" />

    <select id="department"
        name="department"
        class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
        required>

        <option value="">Select Department</option>
        <option value="College of Engineering">College of Engineering</option>
        <option value="College of Engineering">NOCS Office</option>
        <option value="College of Business">College of Business</option>
        <option value="College of Arts and Sciences">College of Arts and Sciences</option>
        <option value="Senior High School">Senior High School</option>
        <option value="Junior High School">Junior High School</option>
        <option value="Administration">Administration</option>

    </select>

    <x-input-error :messages="$errors->get('department')" class="mt-2" />
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
