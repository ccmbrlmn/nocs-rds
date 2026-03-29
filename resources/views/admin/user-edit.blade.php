<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (localStorage.theme === 'dark' || 
               (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
    </script>

    <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 p-4">
        <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 rounded shadow-md w-full max-w-lg p-6">
            <h1 class="text-2xl font-semibold mb-6 text-center text-gray-900 dark:text-gray-200">Edit User</h1>

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block font-medium mb-1">Name</label>
                    <input type="text" name="name" id="name"
                           value="{{ old('name', $user->name) }}"
                           class="w-full border rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('name')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block font-medium mb-1">Email</label>
                    <input type="email" name="email" id="email"
                           value="{{ old('email', $user->email) }}"
                           class="w-full border rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('email')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <a href="{{ route('admin.users') }}"
                       class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                       Cancel
                    </a>

                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 dark:hover:bg-blue-500 transition">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
