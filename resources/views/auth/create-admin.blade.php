<x-app-layout>
    @if(session('success'))
        <div class="mb-4 p-2 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-2 bg-red-100 text-red-800 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Create New Admin</h2>
    </x-slot>

    <div class="relative min-h-screen flex items-center justify-center">
        <form method="POST" action="{{ route('admin.create.store') }}"
              class="form-container bg-white p-6 rounded-lg relative z-10 w-full max-w-md">
            @csrf

            <div class="flex justify-center mb-4">
                <img src="{{ asset('assets/images/reg-loho.png') }}" 
                     class="h-auto w-25" alt="Admin Logo">
            </div>

            <div class="text-center w-full mb-6">
                <h1>Create Admin Account</h1>
                <p class="text-gray-600">
                    Restricted access — admin only
                </p>
            </div>

            <div class="mb-4">
                <x-input-label for="name" value="Admin Name" />
                <x-text-input id="name" name="name" required class="block mt-1 w-full" />
            </div>

            <div class="mb-4">
                <x-input-label for="email" value="Admin Email" />
                <x-text-input id="email" name="email" type="email" required class="block mt-1 w-full" />
            </div>

            <div class="mb-4">
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" name="password" type="password" required class="block mt-1 w-full" />
                <p class="text-gray-500 text-sm mt-1">
                    Password must be at least 8 characters, include uppercase, lowercase, number, and special character.
                </p>
            </div>

            <div class="mb-4">
                <x-input-label for="password_confirmation" value="Confirm Password" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" required class="block mt-1 w-full" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button style="background-color: #0575E6; color: white; width: 100%;">
                    Create Admin
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>

