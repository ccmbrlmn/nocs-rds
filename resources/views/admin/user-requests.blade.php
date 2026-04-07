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
        'routeName' => 'user.requests',

        'statuses' => [
            'All' => null,
            'Open' => 'Open',
            'Active' => 'Active',
            'Closed' => 'Closed',
            'Declined' => 'Declined'
        ],

        'dateFilters' => [
            null => 'All Time',
            '30_days' => '30 Days',
            '7_days' => '7 Days',
            '24_hours' => '24 Hours'
        ],

        'exportPdf' => 'user.requests.pdf',
        'exportCsv' => 'user.requests.csv'
])

<div class="request-history-list rounded-xl shadow overflow-hidden mx-10">
    <div class="head bg-blue-100 dark:bg-blue-900 px-4 py-2 flex justify-between text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-t-xl">
        <div class="w-2/6 text-center">Event</div>
        <div class="w-1/6 text-center">Date</div>
        <div class="w-1/6 text-center">Purpose</div>
        <div class="w-1/6 text-center">Status</div>
    </div>

    <div class="request-history-wrapper max-h-[60vh] overflow-y-auto">

        @foreach ($requests as $request)
        <a href="{{ route('request-details.show', $request->id) }}" 
           class="block bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700">

            <div class="flex justify-between items-center px-4 py-3 text-sm">
                <div class="w-2/6 text-center text-gray-600 dark:text-gray-300">
                    <div class="font-medium">
                        {{ $request->event_name }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $request->representative_name }}
                    </div>
                </div>

                <div class="w-1/6 text-center text-gray-600 dark:text-gray-300">
                    {{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}
                </div>

                <div class="w-1/6 text-center text-gray-600 dark:text-gray-300">
                    @if($request->purpose === 'Others' && $request->other_purpose)
                        {{ $request->other_purpose }}
                    @else
                        {{ $request->purpose }}
                    @endif
                </div>

                <div class="w-1/6 flex justify-center">
                    @php
                        $status = $request->computed_status;
                        $statusClasses = [
                            'Open' => 'bg-amber-100 text-amber-700 dark:bg-amber-700 dark:text-amber-200',
                            'Active' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-700 dark:text-indigo-200',
                            'Closed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-200',
                            'Declined' => 'bg-rose-100 text-rose-700 dark:bg-rose-700 dark:text-rose-200',
                        ];
                    @endphp

                    <span class="px-3 py-1 rounded-xl text-sm font-medium {{ $statusClasses[$status] ?? 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-200' }}">
                        {{ $status }}
                    </span>
                </div>
            </div>
        </a>
    @endforeach
</div>
</div>
</div>
</x-app-layout>

<style>

.header-container {
    margin-left: 1.5rem;
    margin-right: 1.5rem;
}

.request-history-list {
    padding-left: 0;
    padding-right: 0;
}
.material-symbols-outlined {
    font-size: 28px;
    vertical-align: middle;
}

.filter-container {
    margin-left: 1.5rem;
    margin-right: 1.5rem;
    margin-bottom: 0.5rem;
}

.request-history-list {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 300px);
    position: relative;
    margin-left: 1.5rem;
    margin-right: 1.5rem;
    padding-left: 0;
    padding-right: 0;

    overflow: visible;
}

.request-history-wrapper {
    position: relative;
    top: auto;
    bottom: auto;
    left: auto;
    right: auto;
    overflow: visible;
    overflow-y: auto;       
    max-height: 60vh;      
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

.sort-tab .sort-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-color: white;
    border: none;
    font-size: 0.875rem;
    font-weight: 500;
    color: #6B7280;
    text-align: center;
    border-radius: 0.375rem;
    cursor: pointer;
    padding: 0 30px 0 12px;
    height: 44px;
    min-width: 160px;
}

.sort-tab .sort-select:focus {
    outline: none;
    box-shadow: none;
}

.sort-tab .sort-select::-ms-expand {
    display: none;
}

.sort-tab {
    position: relative;
}

.sort-tab::after {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #6B7280;
    font-size: 0.875rem;
}

</style>

