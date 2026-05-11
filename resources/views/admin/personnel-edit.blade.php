<x-app-layout>
    <div class="p-6">

        <h1 class="text-xl font-bold mb-4">Edit Personnel</h1>

        <form method="POST" action="{{ route('admin.personnel.update', $personnel->id) }}">
            @csrf
            @method('PUT')

            <input type="text" name="name" value="{{ $personnel->name }}" class="border p-2 w-full mb-2">

            <input type="email" name="email" value="{{ $personnel->email }}" class="border p-2 w-full mb-2">

            <input type="text" name="office" value="{{ $personnel->office }}" class="border p-2 w-full mb-2">

            <input type="text" name="department" value="{{ $personnel->department }}" class="border p-2 w-full mb-2">

            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Update
            </button>

        </form>

    </div>
</x-app-layout>
