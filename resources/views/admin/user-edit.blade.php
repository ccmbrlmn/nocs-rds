<x-app-layout>
    <div class="p-6 bg-white rounded shadow-md max-w-lg mx-auto mt-10">
        <h1 class="text-2xl font-semibold mb-4">Edit User</h1>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block font-medium">Name</label>
                <input type="text" name="name" id="name"
                       value="{{ old('name', $user->name) }}"
                       class="w-full border rounded px-3 py-2">
                @error('name')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block font-medium">Email</label>
                <input type="email" name="email" id="email"
                       value="{{ old('email', $user->email) }}"
                       class="w-full border rounded px-3 py-2">
                @error('email')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.users') }}"
                   class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                   Cancel
                </a>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Update
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
