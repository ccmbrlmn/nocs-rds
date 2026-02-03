<x-app-layout>
    <div class="header-container flex items-center gap-5 text-white font-medium p-2 mt-8 mb-3">
        <div class="header">
            <h1 class="flex items-center gap-2 text-3xl">
                <span class="material-symbols-outlined text-2xl">description</span> 
                Requests
            </h1>
        </div>
    </div>

    <div class="filter-container flex items-center space-x-4">
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

    <div class="filter-container flex items-center space-x-4"> 
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
       class="px-2 py-1 text-sm text-gray-600 bg-transparent focus:outline-none focus:ring-0 border-0">

                
                @foreach(request()->except('specific_date') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
            </form>
        </div>
    </div>

</div>


    <div class="request-history-list p-3 rounded-tr-lg rounded-tl-lg">
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

        @php
            $statusColors = [
                'Open' => 'text-green-600 bg-green-100', 
                'Active' => 'text-yellow-600 bg-yellow-100', 
                'Closed' => 'text-gray-600 bg-gray-200', 
                'Declined' => 'text-red-600 bg-red-100', 
            ];
        @endphp
        
    <div class="request-history-wrapper">
    @foreach ($requests as $request)
    <div class="request-row bg-white hover:bg-blue-50 border border-gray-200 transition duration-200">
        <div class="row flex justify-between items-center space-x-4 p-2">
            <div class="col w-1/6"><p class="text-gray-600 text-center">#{{ $request->id }}</p></div>
            <div class="col w-2/6 justify-center flex">
                <a href="{{ route('request-details.show', $request->id) }}" class="text-gray-600 text-center">
                    {{ $request->event_name }}
                </a>
            </div>
            <div class="col w-1/6"><p class="text-gray-600 text-center">{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}</p></div>
            <div class="col w-1/6"><p class="text-gray-600 text-center">{{ $request->purpose }}</p></div>
            <div class="col w-1/6">
            <span class="px-3 py-1 rounded-full font-semibold flex justify-center items-center 
            {{ $statusColors[$request->status] ?? 'text-gray-600 bg-gray-100' }}">

                    {{ $request->status }}
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
        height: calc(100vh - 300px);
    }
    
    .request-history-wrapper {
        flex: 1;
        overflow-y: auto;
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
</style>
