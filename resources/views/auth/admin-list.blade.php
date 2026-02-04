<x-app-layout>
    <div class="header-container flex items-center gap-5 text-white font-medium p-2 mt-8 mb-3">
        <div class="header flex justify-between items-center w-full">
            <h1 class="flex items-center gap-2 text-3xl">
                <span class="material-symbols-outlined text-2xl">group</span>
                Admins You Created
            </h1>

            @php
                $firstAdminId = \App\Models\User::where('role', 'admin')->orderBy('id')->first()->id ?? null;
            @endphp

            @if(Auth::id() === $firstAdminId)
                <a href="{{ route('admin.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2">
                   <span class="material-symbols-outlined">person_add</span> Add Admin
                </a>
            @endif
        </div>
    </div>

    <div class="request-history-list p-3 rounded-tr-lg rounded-tl-lg">
        <div class="head bg-blue-100 p-3 rounded-tr-lg rounded-tl-lg">
            <div class="row flex justify-between items-center space-x-4">
                <div class="col w-1/6 text-center font-semibold">ID</div>
                <div class="col w-2/6 text-center font-semibold">Name</div>
                <div class="col w-2/6 text-center font-semibold">Email</div>
                <div class="col w-1/6 text-center font-semibold">Created At</div>
            </div>
        </div>

        <div class="request-history-wrapper">
            @forelse($admins as $admin)
                <div class="request-row bg-white hover:bg-blue-50 border border-gray-200 transition duration-200 cursor-pointer"
     onclick="window.location='{{ route('admin.logs', $admin->id) }}'">
                    <div class="row flex justify-between items-center space-x-4 p-2">
                        <div class="col w-1/6 text-center text-gray-600">{{ $admin->id }}</div>
                        <div class="col w-2/6 text-center text-gray-600">{{ $admin->name }}</div>
                        <div class="col w-2/6 text-center text-gray-600">{{ $admin->email }}</div>
                        <div class="col w-1/6 text-center text-gray-600">
                            {{ \Carbon\Carbon::parse($admin->created_at)->format('M d, Y') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-gray-200 p-3 text-center text-gray-500">
                    No admins created yet.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>

<style>
    .material-symbols-outlined {
        font-size: 28px;
        vertical-align: middle;
    }

    .request-history-list {
        margin-left: 4.5rem;
        margin-right: 5rem;
        display: flex;
        flex-direction: column;
    }

    .header-container {
        margin-left: 5rem;
        margin-right: 5rem;
    }

    .request-history-wrapper {
        flex: 1;
        height: 400px;
        overflow-y: auto;
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

