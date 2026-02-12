<x-app-layout>
    <div class="page-wrapper flex flex-col h-screen">

        <div class="header-container flex items-center gap-5 text-white font-medium p-2 mt-8 mb-3">
            <div class="header">
                <h1 class="flex items-center gap-2 text-3xl">
                    <span class="material-symbols-outlined text-2xl">description</span> 
                    User Requests
                </h1>
            </div>
        </div>

        @php
            $statusColors = config('status');
        @endphp

        <div class="filter-container flex items-center space-x-4 mb-2">
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

            <div class="filter-tab">
                <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 bg-white px-1 py-1 rounded-md">
                    @foreach([null=>'All Time','30_days'=>'30 Days','7_days'=>'7 Days','24_hours'=>'24 Hours'] as $key=>$label)
                    <li class="me-2">
                        <a href="{{ route('admin.requests', array_merge(request()->query(), ['date_filter' => $key])) }}"
                           class="inline-block px-3 py-2 rounded-lg {{ request('date_filter') == $key || (!$key && !request('date_filter')) ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                            {{ $label }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="calendar-tab bg-white px-3 py-2 flex items-center space-x-3 rounded-md">
                <form action="{{ route('admin.requests') }}" method="GET">
                    <input type="date"
                           name="specific_date"
                           value="{{ request('specific_date') }}"
                           class="px-2 py-1 text-sm text-gray-600 bg-transparent focus:outline-none border-0"
                           onchange="this.form.submit()">
                    @foreach(request()->except('specific_date') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </form>
            </div>

            <div class="sort-tab relative">
                <form action="{{ route('admin.requests') }}" method="GET">
                    <select name="sort" onchange="this.form.submit()" class="sort-select">
                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                    @foreach(request()->except('sort') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </form>
            </div>
        </div>

        <div class="request-history-list p-3 rounded-tr-lg rounded-tl-lg flex-1 relative">
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

            <div class="request-history-wrapper absolute top-0 right-0 bottom-0 left-0 overflow-y-auto">
                @foreach($requests as $request)
                <a href="{{ route('admin.request-details', $request->id) }}" class="request-row block bg-white hover:bg-blue-50 border border-gray-200 transition duration-200">
                    <div class="row flex justify-between items-center space-x-4 p-2 cursor-pointer">
                        <div class="col w-1/6 text-center text-gray-600">#{{ $request->id }}</div>
                        <div class="col w-1/6 justify-center flex">{{ $request->user->name }}</div>
                        <div class="col w-1/6 text-center text-gray-600">{{ $request->event_name ?? '-' }}</div>
                        <div class="col w-1/6 text-center text-gray-600">{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}</div>
                        <div class="col w-1/6 text-center text-gray-600">{{ $request->purpose }}</div>

                        @php
                            $status = $request->computed_status;
                            $statusClasses = [
                                'Open' => 'bg-yellow-200 text-yellow-800',
                                'Active' => 'bg-blue-200 text-blue-800',
                                'Closed' => 'bg-green-200 text-green-800',
                                'Declined' => 'bg-red-200 text-red-800',
                            ];
                        @endphp
                        <div class="col w-1/6 flex justify-center">
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
    .material-symbols-outlined { font-size: 28px; vertical-align: middle; }
    .request-history-list { margin-left: 1.5rem; margin-right: 1.5rem; display: flex; flex-direction: column; height: auto; position: relative; }
    .header-container { margin-left: 1.5rem; margin-right: 1.5rem; }
    .filter-container { margin-left: 1.5rem; margin-right: 1.5rem; margin-bottom: 0.5rem; }
    .request-history-wrapper { position: relative; top: auto; bottom: auto; left: auto; right: auto; overflow: visible;  }
    .request-history-wrapper::-webkit-scrollbar { width: 8px; }
    .request-history-wrapper::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    .request-history-wrapper::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
    .request-history-wrapper::-webkit-scrollbar-thumb:hover { background: #555; }
    .sort-tab .sort-select { appearance: none; background-color: white; border: none; font-size: 0.875rem; font-weight: 500; color: #6B7280; text-align: center; border-radius: 0.375rem; cursor: pointer; padding: 0 30px 0 12px; height: 44px; min-width: 160px; }
    .sort-tab .sort-select:focus { outline: none; box-shadow: none; }
</style>

