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
        <div class="head bg-blue-100 p-3 rounded-tr-lg rounded-tl-lg">
            <div class="row flex justify-between items-center space-x-4">
                <div class="col w-1/6 text-center font-semibold">ID</div>
                <div class="col w-2/6 text-center font-semibold">Name</div>
                <div class="col w-2/6 text-center font-semibold">Email</div>
                <div class="col w-1/6 text-center font-semibold">Created At</div>
                <div class="col w-1/6 text-center font-semibold">Status</div>
                <div class="col w-1/6 text-center font-semibold">Actions</div>
            </div>
        </div>

        <div class="request-history-wrapper">
            @forelse($users as $user)
                {{-- changed hover:bg-green-50 → hover:bg-blue-50 --}}
                <div class="request-row bg-white hover:bg-blue-50 border border-gray-200 transition duration-200">
                    <div class="row flex justify-between items-center space-x-4 p-2">
                        <div class="col w-1/6 text-center text-gray-600">{{ $user->id }}</div>
                        <div class="col w-2/6 text-center text-gray-600">{{ $user->name }}</div>
                        <div class="col w-2/6 text-center text-gray-600">{{ $user->email }}</div>
                        <div class="col w-1/6 text-center text-gray-600">
                            {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                        </div>
                        
                        <div class="col w-1/6 text-center">
    @if($user->is_approved)
        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">
            Approved
        </span>
    @else
        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs">
            Pending
        </span>
    @endif
</div>
                        
<div class="col w-1/6 text-center flex justify-center gap-2 flex-wrap"
     onclick="event.stopPropagation();">
    @if(Auth::user()->role === 'admin' && $user->role === 'user')

        @if(!$user->is_approved)
            {{-- Approve/Decline --}}
            <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm whitespace-nowrap">
                    Approve
                </button>
            </form>

            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to decline/delete this user?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm whitespace-nowrap">
                    Decline
                </button>
            </form>
        @else
            {{-- Approved: Show Edit and Delete --}}
            <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm whitespace-nowrap">
                Edit
            </a>

            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm whitespace-nowrap">
                    Delete
                </button>
            </form>
        @endif

@endif
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
</style>

