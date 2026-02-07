<x-app-layout>
    <div class="header-container flex items-center gap-5 text-white font-medium p-2 mt-8 mb-3">
        <div class="header flex justify-between items-center w-full">
            <h1 class="flex items-center gap-2 text-3xl">
                <span class="material-symbols-outlined text-2xl">group</span>
                Registered Users
            </h1>
        </div>
    </div>

    <div class="request-history-list p-3 rounded-tr-lg rounded-tl-lg">
        <div class="head bg-green-100 p-3 rounded-tr-lg rounded-tl-lg">
            <div class="row flex justify-between items-center space-x-4">
                <div class="col w-1/6 text-center font-semibold">ID</div>
                <div class="col w-2/6 text-center font-semibold">Name</div>
                <div class="col w-2/6 text-center font-semibold">Email</div>
                <div class="col w-1/6 text-center font-semibold">Created At</div>
            </div>
        </div>

        <div class="request-history-wrapper">
            @forelse($users as $user)
                <div class="request-row bg-white hover:bg-green-50 border border-gray-200 transition duration-200 cursor-pointer"
                     onclick="window.location='{{ route('admin.users.logs', $user->id) }}'">
                    <div class="row flex justify-between items-center space-x-4 p-2">
                        <div class="col w-1/6 text-center text-gray-600">{{ $user->id }}</div>
                        <div class="col w-2/6 text-center text-gray-600">{{ $user->name }}</div>
                        <div class="col w-2/6 text-center text-gray-600">{{ $user->email }}</div>
                        <div class="col w-1/6 text-center text-gray-600">
                            {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-gray-200 p-3 text-center text-gray-500">
                    No registered users yet.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
