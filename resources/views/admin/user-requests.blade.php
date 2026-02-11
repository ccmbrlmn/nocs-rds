<x-app-layout>
    <div class="page-wrapper flex flex-col h-screen">
        {{-- Header --}}
        <div class="header-container flex items-center gap-5 text-white font-medium p-2 mt-8 mb-3">
            <div class="header">
                <h1 class="flex items-center gap-2 text-3xl">
                    <span class="material-symbols-outlined text-2xl">description</span> 
                    Requests
                </h1>
            </div>
        </div>
        
        @php
            $statusColors = config('status');
        @endphp

        {{-- Filters --}}
        <div class="filter-container flex items-center space-x-4 mb-2">
            <div class="filter-tab">
                <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400 bg-white px-1 py-1 rounded-md">
                    <li class="me-2">
                        <a href="{{ route('user.requests') }}" 
                           class="inline-block px-3 py-2 rounded-lg {{ request('status') ? 'hover:bg-gray-100' : 'bg-blue-600 text-white' }}">
                           All
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="{{ route('user.requests', ['status' => 'Open']) }}" 
                           class="inline-block px-3 py-2 rounded-lg {{ request('status') == 'Open' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                           Open
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="{{ route('user.requests', ['status' => 'Active']) }}" 
                           class="inline-block px-3 py-2 rounded-lg {{ request('status') == 'Active' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                           Active
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="{{ route('user.requests', ['status' => 'Closed']) }}" 
                           class="inline-block px-3 py-2 rounded-lg {{ request('status') == 'Closed' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                           Closed
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="{{ route('user.requests', ['status' => 'Declined']) }}" 
                           class="inline-block px-3 py-2 rounded-lg {{ request('status') == 'Declined' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                           Declined
                        </a>
                    </li>
                </ul>
            </div>

            <div class="filter-tab">
                <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400 bg-white px-1 py-1 rounded-md">
                    <li class="me-2">
                        <a href="{{ route('user.requests', array_merge(request()->query(), ['date_filter' => null])) }}" 
                           class="inline-block px-3 py-2 rounded-lg {{ request('date_filter') ? 'hover:bg-gray-100' : 'bg-blue-600 text-white' }}">
                           All Time
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="{{ route('user.requests', array_merge(request()->query(), ['date_filter' => '30_days'])) }}" 
                           class="inline-block px-3 py-2 rounded-lg {{ request('date_filter') == '30_days' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                           30 Days
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="{{ route('user.requests', array_merge(request()->query(), ['date_filter' => '7_days'])) }}" 
                           class="inline-block px-3 py-2 rounded-lg {{ request('date_filter') == '7_days' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                           7 Days
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="{{ route('user.requests', array_merge(request()->query(), ['date_filter' => '24_hours'])) }}" 
                           class="inline-block px-3 py-2 rounded-lg {{ request('date_filter') == '24_hours' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                           24 Hours
                        </a>
                    </li>
                </ul>
            </div>

            <div class="calendar-tab bg-white px-3 py-2 flex items-center space-x-3 rounded-md">
                <form action="{{ route('user.requests') }}" method="GET">
                    <input type="date" name="specific_date" value="{{ request('specific_date') }}" 
       class="px-2 py-1 text-sm text-gray-600 bg-transparent focus:outline-none focus:ring-0 border-0"
       onchange="this.form.submit()">
                    @foreach(request()->except('specific_date') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </form>
            </div>
        </div>

        {{-- Request history --}}
        <div class="request-history-list p-3 rounded-tr-lg rounded-tl-lg flex-1 relative">
            <div class="head bg-blue-100 p-3 rounded-tr-lg rounded-tl-lg">
                <div class="text">
                    <div class="row flex justify-between items-center space-x-4">
                        <div class="col w-1/6"><p class="pt-2 font-semibold text-center">Request No.</p></div>
                        <div class="col w-2/6"><p class="pt-2 font-semibold text-center">Event</p></div>
                        <div class="col w-1/6"><p class="pt-2 font-semibold text-center">Date</p></div>
                        <div class="col w-1/6"><p class="pt-2 font-semibold text-center">Request</p></div>
                        <div class="col w-1/6"><p class="pt-2 font-semibold text-center">Status</p></div>
                    </div>
                </div>      
            </div>

{{-- Scroll container now outside, on right --}}
<div class="request-history-wrapper absolute top-0 right-0 bottom-0 left-0 overflow-y-auto">
    @foreach ($requests as $request)
    <a href="{{ route('request-details.show', $request->id) }}" class="request-row block bg-white hover:bg-blue-50 border border-gray-200 transition duration-200">
        <div class="row flex justify-between items-center space-x-4 p-2 cursor-pointer">
            <!-- Use $loop->iteration for Request No. -->
            <div class="col w-1/6"><p class="text-gray-600 text-center">#{{ $loop->iteration }}</p></div>
            <div class="col w-2/6 justify-center flex">
                <p class="text-gray-600 text-center">{{ $request->event_name }}</p>
            </div>
            <div class="col w-1/6"><p class="text-gray-600 text-center">{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}</p></div>
            <div class="col w-1/6"><p class="text-gray-600 text-center">{{ $request->purpose }}</p></div>

            <div class="col w-1/6 flex justify-center">
                @php
                    $status = $request->computed_status;
                    $statusClasses = [
                        'Open' => 'bg-yellow-200 text-yellow-800',
                        'Active' => 'bg-blue-200 text-blue-800',
                        'Closed' => 'bg-green-200 text-green-800',
                        'Declined' => 'bg-red-200 text-red-800',
                    ];
                @endphp

                <span class="px-2 py-1 rounded-full text-xs {{ $statusClasses[$status] ?? 'bg-gray-200 text-gray-600' }}">
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
</style>

