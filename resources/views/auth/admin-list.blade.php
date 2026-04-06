<x-app-layout>
    <div class="header-container rounded-2xl mb-3 mx-3 mt-3">
<div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
        Admins Created
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('admin.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold shadow">
            Add Admin
        </a>
        @include('layouts.header')
    </div>
</div>
</div>

        @php
            $statusColors = config('status');
        @endphp

        @include('layouts.filter', [
            'routeName' => 'admin.created-admins',

            'statuses' => [
                'All' => null,
                'Active' => 'active',
                'Deleted' => 'deleted',
            ],

            'dateFilters' => [
                null => 'All Time',
                '30_days' => '30 Days',
                '7_days' => '7 Days',
                '24_hours' => '24 Hours'
            ],

            'exportPdf' => 'admin.created-admins.pdf',
            'exportCsv' => 'admin.created-admins.csv'
        ])
        
        @php
    $highlightAdminId = request()->query('highlight');
@endphp


<div class="request-history-list rounded-xl shadow overflow-hidden mx-10">
    <div class="head bg-blue-100 dark:bg-blue-900 px-4 py-2 flex justify-between text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-t-xl">
            <div class="w-1/6 text-center">ID</div>
            <div class="w-2/6 text-center">Name</div>
            <div class="w-2/6 text-center">Email</div>
             <div class="w-2/12 text-center">Office</div> 
            <div class="w-1/6 text-center">Created</div>
            <div class="w-1/6 text-center">Status</div>
            <div class="w-1/6 text-center">Actions</div>
    </div>

    <div class="request-history-wrapper divide-y divide-gray-200 dark:divide-gray-700">

        @forelse($admins as $admin)
        
        @php
    $isHighlighted = $highlightAdminId == $admin->id;
@endphp

        <div class="request-row {{ $isHighlighted ? 'highlighted-admin' : '' }} 
            bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700 transition cursor-pointer"
     onclick="window.location='{{ route('admin.logs', $admin->id) }}'">

            <div class="row flex items-center px-6 py-3 text-sm">

                <div class="w-1/6 text-center text-gray-600 dark:text-gray-300">
                    {{ $admin->id }}
                </div>

                <div class="w-2/6 text-center text-gray-700 dark:text-gray-200 font-medium">
                    {{ $admin->name }}
                </div>

                <div class="w-2/6 text-center text-gray-600 dark:text-gray-300">
                    {{ $admin->email }}
                </div>
                
                <div class="w-2/12 text-center text-gray-600 dark:text-gray-300">{{ $admin->office ?? '-' }}</div>

                <div class="w-1/6 text-center text-gray-500 dark:text-gray-400">
                    {{ \Carbon\Carbon::parse($admin->created_at)->format('M d, Y') }}
                </div>
                
                <div class="w-1/6 text-center">
                    @if($admin->deleted_at)
                        <span class="text-gray-600 dark:text-gray-300 font-semibold">Deleted</span>

                    @else
                        <span class="text-gray-600 dark:text-gray-300 font-semibold">Active</span>
                    @endif
                </div>

                <div class="w-1/6 flex justify-center gap-2"
                     onclick="event.stopPropagation();">

@if($admin->deleted_at)
    <a href="#"
       onclick="if(confirm('This admin account is deleted. You must restore it before editing. Do you want to restore this admin now?')) { this.nextElementSibling.submit(); } return false;"
       class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded-md font-semibold shadow">
        Edit
    </a>

    <form action="{{ route('admin.restore', $admin->id) }}"
          method="POST"
          class="hidden">
        @csrf
    </form>
    
    <form action="{{ route('admin.restore', $admin->id) }}"
          method="POST"
          onsubmit="return confirm('Restore this admin account?');">
        @csrf
        <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md font-semibold shadow">
            Restore
        </button>
    </form>
@else
    <a href="{{ route('admin.edit', $admin->id) }}"
       class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded-md font-semibold shadow">
        Edit
    </a>

    <form action="{{ route('admin.destroy', $admin->id) }}"
          method="POST"
          onsubmit="return confirm('Are you sure you want to delete this admin?');">
        @csrf
        @method('DELETE')

        <button type="submit"
                class="bg-rose-600 hover:bg-rose-700 text-white px-3 py-1 rounded-md font-semibold shadow">
            Delete
        </button>
    </form>
@endif

                </div>

            </div>

        </div>

        @empty

        <div class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
            No admins created yet.
        </div>

        @endforelse

    </div>

</div>
</x-app-layout>

<style>
    .material-symbols-outlined { font-size: 28px; vertical-align: middle; }
    .request-history-wrapper::-webkit-scrollbar { width: 6px; }
    .request-history-wrapper::-webkit-scrollbar-track { background: transparent; }
    .request-history-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .request-history-wrapper::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .sort-select { appearance: none; }
    
    .highlighted-admin {
        background-color: #ffe58f;
        transition: background-color 0.3s;
    }

</style>

