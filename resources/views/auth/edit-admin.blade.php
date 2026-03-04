<x-app-layout>
    <div class="p-6 bg-white rounded shadow-md max-w-lg mx-auto mt-10">
        <h1 class="text-2xl font-semibold mb-4">Edit Admin</h1>

        <form action="{{ route('admin.update', $admin->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block font-medium">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}"
                       class="w-full border rounded px-3 py-2">
                @error('name')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block font-medium">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}"
                       class="w-full border rounded px-3 py-2">
                @error('email')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</x-app-layout>

<style>
    .material-symbols-outlined {
        font-size: 28px;
        vertical-align: middle;
    }

    .request-history-list {
        margin-left: 1.5rem;
        margin-right: 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .header-container {
        margin-left: 1.5rem;
        margin-right: 1.5rem;
    }

    .request-history-wrapper {
        flex: 1;
        height: 400px;

    }

    .request-history-wrapper::-webkit-scrollbar {
        width: 8px;
    }

    .request-history-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .request-history-wrapper::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .request-history-wrapper::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .request-row {
        transition: background-color 0.2s;
    }

</style>
