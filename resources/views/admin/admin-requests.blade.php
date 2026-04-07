<x-app-layout>
    <div class="page-wrapper flex flex-col h-screen">
    
    <div class="header-container rounded-2xl mb-3 mx-3 mt-3">
        <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Page Title -->
            <div class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
                User Requests
            </div>
        @include('layouts.header')
        </div>
    </div>

        @php
            $statusColors = config('status');
        @endphp
        
        @include('layouts.filter', [
            'routeName' => 'admin.requests',

            'statuses' => [
                'All' => null,
                'Open' => 'Open',
                'Active' => 'Active',
                'Closed' => 'Closed',
                'Declined' => 'Declined',
            ],

            'dateFilters' => [
                null => 'All Time',
                '30_days' => '30 Days',
                '7_days' => '7 Days',
                '24_hours' => '24 Hours'
            ],

            'exportPdf' => 'admin.requests.pdf',
            'exportCsv' => 'admin.requests.csv'
        ])

        <div class="request-history-list rounded-xl shadow overflow-hidden mx-10">
            <div class="head bg-blue-100 dark:bg-blue-900 px-4 py-2 flex justify-between text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-t-xl">
                <div class="w-1/6 text-center">Request No.</div>
                <div class="w-1/6 text-center">Requester</div>
                <div class="w-1/6 text-center">Event</div>
                <div class="w-1/6 text-center">Date</div>
                <div class="w-1/6 text-center">Purpose</div>
                <div class="w-1/6 text-center">Status</div>
            </div>

            <div class="request-history-wrapper max-h-[60vh] overflow-y-auto">
            
            
@forelse($requests as $request)
    @php
        $user = $request->user;
        $userClass = ($user && $user->trashed()) ? 'text-red-600 italic' : 'text-gray-800 dark:text-gray-200';
        $status = $request->computed_status;
    @endphp
    <a href="{{ route('admin.request-details', $request->id) }}" 
       class="block text-gray-200 bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700">
        <div class="flex justify-between items-center px-4 py-3 text-sm">
            <div class="w-1/6 text-center text-gray-600 dark:text-gray-300">#{{ $request->id }}</div>
            <div class="w-1/6 flex justify-center"><span class="{{ $userClass }}">{{ $user->name ?? '—' }} @if($user && $user->trashed()) (Deleted) @endif</span></div>
            <div class="w-1/6 text-center text-gray-600 dark:text-gray-300">{{ $request->event_name ?? '-' }}</div>
            <div class="w-1/6 text-center text-gray-600 dark:text-gray-300">{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}</div>
            <div class="w-1/6 text-center text-gray-600 dark:text-gray-300">
                @if($request->purpose === 'Others' && $request->other_purpose)
                    Others ({{ $request->other_purpose }})
                @else
                    {{ $request->purpose }}
                @endif
            </div>
            <div class="w-1/6 flex justify-center">
                @php
                    $statusConfig = config('status')[$status] ?? null;

                    $statusClass = $statusConfig
                        ? $statusConfig['bg'] . ' ' . $statusConfig['text']
                        : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-200';
                @endphp

                <span class="px-3 py-1 rounded-xl text-sm font-medium {{ $statusClass }}">
                    {{ $status }}
                </span>
            </div>
        </div>
    </a>
@empty
    <div class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
        @php
            $filter = request()->query('status');
        @endphp

        @if(in_array($filter, ['Open', 'Active', 'Closed', 'Declined']))
            No {{ strtolower($filter) }} requests yet.
        @else
            No requests yet.
        @endif
    </div>
@endforelse
            </div>
        </div>

    </div>
</x-app-layout>

<style>
    [x-cloak] {
        display: none !important;
    }
    .material-symbols-outlined { font-size: 28px; vertical-align: middle; }
    .request-history-wrapper::-webkit-scrollbar { width: 6px; }
    .request-history-wrapper::-webkit-scrollbar-track { background: transparent; }
    .request-history-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .request-history-wrapper::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .sort-select { appearance: none; }
</style>
