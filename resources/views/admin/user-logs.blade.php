<x-app-layout>
    <div class="header-container flex items-center gap-5 text-white font-medium p-2 mt-8 mb-3">
        <div class="header flex justify-between items-center w-full">
            <h1 class="flex items-center gap-2 text-3xl">
                <span class="material-symbols-outlined text-2xl">history</span>
                {{ $user->name }}'s Logs
            </h1>

            <a href="{{ route('admin.users') }}" 
               class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded flex items-center gap-2">
               <span class="material-symbols-outlined">arrow_back</span> Back
            </a>
        </div>
    </div>

    <div class="request-history-list p-3 rounded-tr-lg rounded-tl-lg">
        <div class="head bg-blue-100 p-3 rounded-tr-lg rounded-tl-lg">
            <div class="row flex justify-between items-center space-x-4">
                <div class="col w-1/6 text-center font-semibold">Request No.</div>
                <div class="col w-2/6 text-center font-semibold">Event</div>
                <div class="col w-1/6 text-center font-semibold">Action</div>
                <div class="col w-2/6 text-center font-semibold">Date</div>
            </div>
        </div>

        <div class="request-history-wrapper">
            @forelse($logs as $log)
                <div class="request-row bg-white hover:bg-blue-50 border border-gray-200 transition duration-200">
                    <div class="row flex justify-between items-center space-x-4 p-2">
                        <div class="col w-1/6 text-center text-gray-600">#{{ $log->id }}</div>
                        <div class="col w-2/6 text-center text-gray-600">{{ $log->event_name ?? '-' }}
                        </div>
                        <div class="col w-1/6 text-center text-gray-600">
                            @if($log->handled_by == $user->id)
                                @if($log->status === 'Active')
                                    Accepted the Request
                                @elseif($log->status === 'Declined')
                                    Declined the Request
                                @endif
                            @elseif($log->requested_by == $user->id)
                                Created Request
                            @endif
                        </div>

                        <div class="col w-2/6 text-center text-gray-600">
                            {{ \Carbon\Carbon::parse($log->updated_at)->timezone('Asia/Manila')->format('M d, Y H:i') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-gray-200 p-3 text-center text-gray-500">
                    No logs available.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>

<style>
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

    .request-row {
        transition: background-color 0.2s;
        cursor: default;
    }

    .material-symbols-outlined {
        font-size: 28px;
        vertical-align: middle;
    }
</style>

