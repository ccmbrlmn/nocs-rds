<x-app-layout>
    <div 
        class="page-wrapper flex flex-col h-screen"
        x-data="{
            view: new URLSearchParams(window.location.search).get('view') || 'list'
        }"
    >

    <!-- HEADER -->
    <div class="header-container rounded-2xl mb-3 mx-3 mt-3">
        <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
                User Requests
            </div>

            @include('layouts.header')
        </div>
    </div>



    <!-- LIST VIEW -->
    <div x-show="view === 'list'" x-cloak x-data="requestModals()">

        @include('layouts.filter', [
            'routeName' => 'admin.requests',
            'statuses' => [
                'All' => null,
                'Open' => 'Open',
                'Active' => 'Active',
                'Closed' => 'Closed',
                'Declined' => 'Declined',
                'Pending Return' => 'Pending Return',
                'Pending Retrieval' => 'Pending Retrieval'
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

            <!-- HEADER -->
            <div class="head bg-blue-100 dark:bg-blue-900 px-4 py-2 flex justify-between text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-t-xl">
                <div class="w-1/6 text-center">Request No.</div>
                <div class="w-1/6 text-center">Requester</div>
                <div class="w-1/6 text-center">Event</div>
                <div class="w-1/6 text-center">Date</div>
                <div class="w-1/6 text-center">Purpose</div>
                <div class="w-1/6 text-center">Status</div>
                <div class="w-1/6 text-center">Action</div>
            </div>

            <div class="request-history-wrapper max-h-[60vh] overflow-y-auto">

                @forelse($requests as $request)

                    @php
                        $user = $request->user;
                        $userClass = ($user && $user->trashed()) ? 'text-red-600 italic' : '';
                        $status = $request->computed_status;
                    @endphp
                    
                    @php
                        $highlightId = request()->query('highlight');
                        $isHighlighted = $highlightId == $request->id;
                    @endphp

<div
    id="request-{{ $request->id }}"
    class="request-row group {{ $isHighlighted ? 'highlighted-request' : '' }}
           bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700 transition"
>

    <div
    @click="window.location.href='{{ route('admin.request-details', $request->id) }}'"
    class="flex w-full items-center px-4 py-3 text-sm cursor-pointer select-none text-gray-700 dark:text-gray-200"
>

        <div class="w-1/6 text-center">#{{ $request->id }}</div>

        <div class="w-1/6 text-center">
            <span class="{{ $userClass }}">
                {{ $user->name ?? '—' }}
            </span>
        </div>

        <div class="w-1/6 text-center">{{ $request->event_name ?? '-' }}</div>

        <div class="w-1/6 text-center">
            {{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}
        </div>

        <div class="w-1/6 text-center">
            {{ $request->purpose }}
        </div>

        <div class="w-1/6 text-center">
            <span class="px-3 py-1 rounded-xl bg-gray-200 dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200">
                {{ $status }}
            </span>
        </div>

        <!-- ACTION -->
        <div class="w-1/6 flex justify-center gap-2">

            @if($status === 'Open')

                <button
                    @click.stop="openAssignModal({{ $request->id }})"
                    class="text-xs px-3 py-1 rounded-lg bg-green-500 hover:bg-green-600 text-white">
                    Approve
                </button>

                <button
                    @click.stop="openCancelModal({{ $request->id }})"
                    class="text-xs px-3 py-1 rounded-lg bg-gray-500 hover:bg-gray-600 text-white">
                    Cancel
                </button>

            @elseif($status === 'Pending Return')

                <button
                    @click.stop="openReturnModal({{ $request->id }})"
                    class="text-xs px-3 py-1 rounded-lg bg-green-500 hover:bg-green-600 text-white">
                    Accept
                </button>

            @elseif($status === 'Pending Retrieval')

                <form action="{{ route('admin.return.retrieved', $request->id) }}" method="POST">
                    @csrf
                    <button class="text-xs px-3 py-1 rounded-lg bg-blue-500 hover:bg-blue-600 text-white">
                        Retrieved
                    </button>
                </form>

            @else
                <span class="text-gray-400 dark:text-gray-500 text-xs italic">—</span>
            @endif

        </div>

</div>

@empty
    @php
        $status = request('status');
        $message = match($status) {
            'Open' => 'No open requests yet.',
            'Active' => 'No active requests yet.',
            'Closed' => 'No closed requests yet.',
            'Declined' => 'No declined requests yet.',
            'Pending Return' => 'No pending return requests yet.',
            'Pending Retrieval' => 'No pending retrieval requests yet.',
            default => 'No requests yet.',
        };
    @endphp

    <div class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
        {{ $message }}
    </div>
@endempty

            </div>
        </div>

        <!-- RETURN MODAL -->
        <div x-show="returnOpen" x-cloak
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-[400px] shadow-xl">

                <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Assign Personnel (Optional)</h2>

                <input x-model="personnel"
                    class="w-full border rounded-lg px-3 py-2 mb-4 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">

                <form :action="returnAction" method="POST">
                    @csrf
                    <input type="hidden" name="personnel" :value="personnel">

                    <button class="px-3 py-1 bg-green-500 text-white rounded-lg">
                        Confirm
                    </button>
                </form>

            </div>
        </div>

        <!-- APPROVE MODAL -->
        <div x-show="assignOpen" x-cloak
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-[400px] shadow-xl">

                <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Proceed to Asset Assignment</h2>

                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                    You will be redirected to assign assets for this request.
                </p>

                <div class="flex justify-end gap-2">
                    <button @click="assignOpen = false"
                        class="px-3 py-1 bg-gray-400 text-white rounded-lg">
                        Cancel
                    </button>

                    <button
                        @click="goToAssets()"
                        class="px-3 py-1 bg-indigo-600 text-white rounded-lg">
                        Proceed
                    </button>
                </div>

            </div>
        </div>

        <!-- CANCEL MODAL -->
        <div x-show="cancelOpen" x-cloak
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-[400px] shadow-xl">

                <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Cancel Request</h2>

                <textarea x-model="cancelReason"
                    placeholder="Enter cancellation reason..."
                    class="w-full border rounded-lg px-3 py-2 mb-4 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"></textarea>

                <div class="flex justify-end gap-2">
                    <button @click="cancelOpen = false"
                        class="px-3 py-1 bg-gray-400 text-white rounded-lg">
                        Close
                    </button>

                    <form :action="cancelAction" method="POST">
                        @csrf
                        <input type="hidden" name="cancel_reason" :value="cancelReason">

                        <button class="px-3 py-1 bg-red-500 text-white rounded-lg">
                            Confirm Cancel
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
