<x-app-layout>
    @if(session('success'))
        <div class="mb-4 p-2 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-2 bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Create New Admin</h2>
    </x-slot>

    <div class="relative min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900">
        <form method="POST" action="{{ route('admin.create.store') }}"
              class="form-container bg-white dark:bg-gray-800 dark:text-gray-200 p-6 rounded-lg relative z-10 w-full max-w-md shadow-lg">
            @csrf

            <div class="text-center w-full mb-6">
                <h1 class="text-2xl font-semibold dark:text-gray-200">Create Admin Account</h1>
                <p class="text-gray-600 dark:text-gray-300">
                    Restricted access — admin only
                </p>
            </div>

            <div class="mb-4">
                <x-input-label for="name" value="Admin Name" class="dark:text-gray-200"/>
                <x-text-input id="name" name="name" required
                        class="block mt-1 w-full dark:bg-gray-900 dark:border-gray-600 dark:text-gray-200 dark:placeholder-gray-400 focus:ring-indigo-500 focus:border-indigo-500" />
            </div>

            <div class="mb-4">
                <x-input-label for="email" value="Admin Email" class="dark:text-gray-200"/>
                <x-text-input id="email" name="email" type="email" required
                        class="block mt-1 w-full dark:bg-gray-900 dark:border-gray-600 dark:text-gray-200 dark:placeholder-gray-400 focus:ring-indigo-500 focus:border-indigo-500" />
            </div>

            <div class="mb-4">
                <x-input-label for="office" value="Office" class="dark:text-gray-200"/>
                <x-text-input id="office" name="email" type="email" required
                    class="block mt-1 w-full dark:bg-gray-900 dark:border-gray-600 dark:text-gray-200 dark:placeholder-gray-400 focus:ring-indigo-500 focus:border-indigo-500"
                    type="text" name="office" value="{{ old('office') }}" required />
                @error('office')
                    <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <x-input-label for="password" value="Password" class="dark:text-gray-200"/>
                <x-text-input id="password" name="password" type="password" required
    class="block mt-1 w-full dark:bg-gray-900 dark:border-gray-600 dark:text-gray-200 dark:placeholder-gray-400 focus:ring-indigo-500 focus:border-indigo-500" />                <p class="text-gray-500 dark:text-gray-300 text-sm mt-1">
                    Password must be at least 8 characters, include uppercase, lowercase, number, and special character.
                </p>
            </div>

            <div class="mb-4">
                <x-input-label for="password_confirmation" value="Confirm Password" class="dark:text-gray-200"/>
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" required
    class="block mt-1 w-full dark:bg-gray-900 dark:border-gray-600 dark:text-gray-200 dark:placeholder-gray-400 focus:ring-indigo-500 focus:border-indigo-500" />            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white">
                    Create Admin
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
