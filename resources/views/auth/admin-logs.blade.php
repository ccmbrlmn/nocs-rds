<x-app-layout>
    <div class="page-wrapper flex flex-col h-screen">

        <div class="header-container flex items-center justify-between mt-8 mb-3 px-6">
            <h1 class="flex items-center gap-2 text-3xl font-semibold text-gray-800">
                <span class="material-symbols-outlined text-2xl">history</span>
                {{ $admin->name }}'s Logs
            </h1>

            <a href="{{ route('admin.created-admins') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
                Back
            </a>
        </div>

        <div class="request-history-list flex-1 mx-6 bg-white shadow rounded-lg overflow-y-auto">

            <div class="head bg-blue-100 px-4 py-3 sticky top-0 z-10">
                <div class="flex justify-between items-center">
                    <div class="w-1/6 text-center font-semibold">Request No.</div>
                    <div class="w-2/6 text-center font-semibold">Event</div>
                    <div class="w-1/6 text-center font-semibold">Action</div>
                    <div class="w-2/6 text-center font-semibold">Date</div>
                </div>
            </div>

            @forelse($logs as $log)
                <div class="flex justify-between items-center px-4 py-2 text-sm text-gray-600 border-b border-gray-200 hover:bg-blue-50 cursor-pointer">
                    <div class="w-1/6 text-center font-medium">#{{ $log->id }}</div>
                    <div class="w-2/6 text-center">{{ $log->event_name ?? '-' }}</div>
                    <div class="w-1/6 text-center font-medium">
                        @if($log->handled_by === $admin->id)
                            @if($log->status === 'Active')
                                <span class="text-green-600">Accepted</span>
                            @elseif($log->status === 'Declined')
                                <span class="text-red-600">Declined</span>
                            @else
                                <span class="text-gray-400">Closed</span>
                            @endif
                        @elseif($log->created_by === $admin->id)
                            <span class="text-blue-600">Created</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </div>
                    <div class="w-2/6 text-center">
                        {{ \Carbon\Carbon::parse($log->updated_at)->format('M d, Y H:i') }}
                    </div>
                </div>
            @empty
                <div class="py-6 text-center text-gray-500">
                    No logs available.
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
    height: calc(100vh - 6.5rem);
    overflow-y: auto;
}

.head {
    top: 0;
    position: sticky;
    z-index: 10;
    background: #BFDBFE;
}

.request-history-list::-webkit-scrollbar {
    width: 8px;
}
.request-history-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}
.request-history-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}
.request-history-list::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.request-history-list {
    scrollbar-width: thin;
    scrollbar-color: #888 #f1f1f1;
}
</style>

