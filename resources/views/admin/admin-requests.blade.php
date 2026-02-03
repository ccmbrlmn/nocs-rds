<x-app-layout>
    <div class="header-container flex items-center gap-5 text-white font-medium p-2 mt-8 mb-3">
        <div class="header">
            <h1 class="flex items-center gap-2 text-3xl">
                <span class="material-symbols-outlined text-2xl">description</span> 
                User Requests
            </h1>
        </div>
    </div>

<div class="filter-container flex items-center space-x-4">

    {{-- STATUS FILTER --}}
    <div class="flex items-center space-x-4">
        <div class="filter-tab">
            <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 bg-white px-1 py-1 rounded-md">
                @foreach(['All' => null, 'Open' => 'Open', 'Active' => 'Active', 'Closed' => 'Closed', 'Declined' => 'Declined'] as $label => $status)
                    <li class="me-2">
                        <a href="{{ route('admin.requests', array_merge(request()->query(), ['status' => $status])) }}"
                           class="inline-block px-3 py-2 rounded-lg {{ request('status') == $status || ($status === null && !request('status')) ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

{{-- DATE FILTER --}}
<div class="flex items-center space-x-4">


    <div class="filter-tab">
        <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 bg-white px-1 py-1 rounded-md">
            <li class="me-2">
                <a href="{{ route('admin.requests', array_merge(request()->query(), ['date_filter' => null])) }}"
                   class="inline-block px-3 py-2 rounded-lg {{ request('date_filter') ? 'hover:bg-gray-100' : 'bg-blue-600 text-white' }}">
                    All Time
                </a>
            </li>
            <li class="me-2">
                <a href="{{ route('admin.requests', array_merge(request()->query(), ['date_filter' => '30_days'])) }}"
                   class="inline-block px-3 py-2 rounded-lg {{ request('date_filter') == '30_days' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                    30 Days
                </a>
            </li>
            <li class="me-2">
                <a href="{{ route('admin.requests', array_merge(request()->query(), ['date_filter' => '7_days'])) }}"
                   class="inline-block px-3 py-2 rounded-lg {{ request('date_filter') == '7_days' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                    7 Days
                </a>
            </li>
            <li class="me-2">
                <a href="{{ route('admin.requests', array_merge(request()->query(), ['date_filter' => '24_hours'])) }}"
                   class="inline-block px-3 py-2 rounded-lg {{ request('date_filter') == '24_hours' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                    24 Hours
                </a>
            </li>
        </ul>
    </div>

</div>


    {{-- DATE PICKER --}}
<div class="calendar-tab bg-white px-3 py-2 flex items-center space-x-3 rounded-md">

        <form action="{{ route('admin.requests') }}" method="GET">
            <input type="date"
                   name="specific_date"
                   value="{{ request('specific_date') }}"
                   class="px-2 py-1 text-sm text-gray-600 bg-transparent focus:outline-none border-0">

            @foreach(request()->except('specific_date') as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
        </form>
    </div>

</div>


    {{-- TABLE --}}
    <div class="request-history-list p-3 rounded-tr-lg rounded-tl-lg">
        <div class="head bg-blue-100 p-3 rounded-tr-lg rounded-tl-lg">
            <div class="row flex justify-between items-center space-x-4">
                <div class="col w-1/6 text-center font-semibold">Request No.</div>
                <div class="col w-1/6 text-center font-semibold">Requester</div>
                <div class="col w-1/6 text-center font-semibold">Event</div>
                <div class="col w-1/6 text-center font-semibold">Date</div>
                <div class="col w-1/6 text-center font-semibold">Purpose</div>
                <div class="col w-1/6 text-center font-semibold">Status</div>
            </div>
        </div>

        @php $statusColors = config('status'); @endphp

<div class="request-history-wrapper">
    @foreach($requests as $request)
        <div class="request-row bg-white hover:bg-blue-50 border border-gray-200 transition duration-200 cursor-pointer"
             onclick="window.location='{{ route('admin.request-details', $request->id) }}'">
            <div class="row flex justify-between items-center space-x-4 p-2">

                <div class="col w-1/6 text-center text-gray-600">#{{ $request->id }}</div>

                <div class="col w-1/6 justify-center flex">
                    {{ $request->user->name }}
                </div>

                <div class="col w-1/6 text-center text-gray-600">
                    {{ $request->event_name ?? '-' }}
                </div>

                <div class="col w-1/6 text-center text-gray-600">
                    {{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}
                </div>

                <div class="col w-1/6 text-center text-gray-600">
                    {{ $request->purpose }}
                </div>

                {{-- STATUS --}}
                <div class="col w-1/6 flex justify-center">
                    @php
                        $statusConfig = $statusColors[$request->status] ?? $statusColors['Open'];
                        $label = match($request->status) {
                            'Active' => 'Active',
                            'Declined' => 'Declined',
                            default => $request->status,
                        };
                    @endphp

                <span class="px-3 py-1 rounded-full font-semibold text-sm flex justify-center items-center {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                    {{ $label }}
                </span>

                </div>

            </div>
        </div>
    @endforeach
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

    .filter-container {
        margin-left: 5rem;
        margin-right: 5rem;
        margin-bottom: 0.5rem;
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

</style>

