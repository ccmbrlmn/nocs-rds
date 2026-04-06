<x-app-layout>
    <!-- HEADER: Greeting + Actions -->
    <div class="header-container rounded-2xl mb-3 mx-3 mt-3">
        <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Page Title -->
            <div class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
                User List
            </div>

            <!-- Include header actions (Dark Mode, Notifications, Profile) -->
        @include('layouts.header')
        </div>
    </div>
    
    @php
    $highlightUserId = request()->query('highlight');
@endphp


        @php
            $statusColors = config('status');
        @endphp

        @include('layouts.filter', [
            'routeName' => 'admin.users',

            'statuses' => [
                'All' => 'all',
                'Pending' => 'pending',
                'Active' => 'active',
                'Deleted' => 'deleted'
            ],

            'dateFilters' => [
                null => 'All Time',
                '30_days' => '30 Days',
                '7_days' => '7 Days',
                '24_hours' => '24 Hours'
            ],

            'exportPdf' => 'admin.users.pdf',
            'exportCsv' => 'admin.users.csv'
        ])
    
<div class="request-history-list rounded-xl shadow overflow-hidden mx-10">

    <!-- Table Header -->
    <div class="head bg-blue-100 dark:bg-blue-900 px-4 py-2 flex text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-t-xl">
        <div class="w-1/12 flex justify-center items-center">ID</div>
        <div class="w-3/12 flex justify-center items-center">Name</div>
        <div class="w-3/12 flex justify-center items-center">Email</div>
        <div class="w-2/12 flex justify-center items-center">Office</div>
        <div class="w-2/12 flex justify-center items-center">Created</div>
        <div class="w-1/12 flex justify-center items-center">Status</div>
        <div class="w-2/12 flex justify-center items-center">Actions</div>
    </div>

    <!-- Table Body -->
    <div class="request-history-wrapper divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($users as $user)
            @php
        $isHighlighted = $highlightUserId == $user->id;
    @endphp
            <div class="request-row {{ $isHighlighted ? 'highlighted-user' : '' }} bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700 transition cursor-pointer"
                 onclick="{{ $isHighlighted ? 'void(0)' : "window.location='".route('admin.users.logs',$user->id)."'" }}'">

                <div class="row flex px-6 py-3 text-sm">

                    <div class="w-1/12 flex justify-center items-center text-gray-600 dark:text-gray-300">
                        {{ $user->id }}
                    </div>

                    <div class="w-3/12 flex justify-center items-center text-gray-800 dark:text-gray-200">
                        {{ $user->name }}
                    </div>

                    <div class="w-3/12 flex justify-center items-center text-gray-600 dark:text-gray-300">
                        {{ $user->email }}
                    </div>

                    <div class="w-2/12 flex justify-center items-center text-gray-600 dark:text-gray-300">
                        {{ $user->office ?? '-' }}
                    </div>

                    <div class="w-2/12 flex justify-center items-center text-gray-600 dark:text-gray-300">
                        {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                    </div>

                    <div class="w-1/12 flex justify-center items-center">
                        @if($user->deleted_at)
                            <span class="text-gray-600 dark:text-gray-300 px-3 py-1 rounded-full text-xs font-medium">Deleted</span>
                        @elseif(!$user->is_approved)
                            <span class="text-gray-600 dark:text-gray-300 px-3 py-1 rounded-full text-xs font-medium">Pending</span>
                        @else
                            <span class="text-gray-600 dark:text-gray-300 px-3 py-1 rounded-full text-xs font-medium">Active</span>
                        @endif
                    </div>

                    <div class="w-2/12 flex justify-center items-center gap-2" onclick="event.stopPropagation();">
                        @if($user->deleted_at)
                            <a href="#"
                               onclick="if(confirm('This user is deleted. Restore first before editing. Restore now?')) { this.nextElementSibling.submit(); } return false;"
                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md font-semibold">Edit</a>

                            <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="hidden">@csrf</form>

                            <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" onsubmit="return confirm('Restore this user?');">
                                @csrf
                                <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md font-semibold">Restore</button>
                            </form>

                        @elseif(!$user->is_approved)
                            <form action="{{ route('admin.users.approve',$user->id) }}" method="POST">
                                @csrf
                                <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md font-semibold">Approve</button>
                            </form>
                            <form action="{{ route('admin.users.destroy',$user->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md font-semibold">Decline</button>
                            </form>
                        @else
                            <a href="{{ route('admin.users.edit',$user->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md font-semibold">Edit</a>
                            <form action="{{ route('admin.users.destroy',$user->id) }}" method="POST" onsubmit="return confirm('Delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md font-semibold">Delete</button>
                            </form>
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div class="px-6 py-6 text-center text-gray-500">No registered users yet.</div>
        @endforelse
    </div>
</div>

    </div>
</div>
</x-app-layout>

<style>
.material-symbols-outlined {
    font-size: 28px;
    vertical-align: middle;
}


.header-container {
    margin-left: 1.5rem;
    margin-right: 1.5rem;
}

.highlighted-user {
    background-color: #ffe58f; /* light yellow */
    transition: background-color 0.3s;
}

</style>

