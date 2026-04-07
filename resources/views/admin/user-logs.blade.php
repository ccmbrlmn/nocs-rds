<x-app-layout>
    <div class="page-wrapper flex flex-col h-screen">

        <div class="header-container flex items-center justify-between p-3 mt-8 mb-6"
             style="margin-left:5rem; margin-right:5rem;">

            <a href="{{ route('admin.users') }}"
               class="px-4 py-2 rounded-xl text-sm font-medium transition 
                      bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 
                      hover:bg-blue-100 dark:hover:bg-gray-600 shadow-sm flex items-center gap-2">

                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Back
            </a>

            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
                    {{ $user->name }}'s Logs
                </h1>
            </div>

        </div>

        <div class="request-history-list rounded-xl shadow overflow-hidden"
             style="margin-left:4.5rem; margin-right:5rem;">

            <div class="head bg-blue-100 dark:bg-blue-900 px-4 py-2 flex justify-between text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-t-xl">
                <div class="w-1/6 text-center">Request No.</div>
                <div class="w-2/6 text-center">Request Name</div>
                <div class="w-1/6 text-center">Action</div>
                <div class="w-2/6 text-center">Date</div>
            </div>

            <div class="request-history-wrapper max-h-[60vh] overflow-y-auto">

                @forelse($logs as $log)
                    <div class="block hover:bg-blue-50 transition border-b border-gray-200">
                        <div class="flex justify-between items-center px-4 py-3 text-sm dark:text-gray-200 bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700">

                            <div class="w-1/6 text-center">
                                #{{ $log->id }}
                            </div>
                            
                            <div class="w-2/6 text-center">
                                {{ $log->request->event_name ?? '-' }}
                            </div>

                            <div class="w-1/6 text-center">
                                @php
                                    $action = $log->action ?? null;
                                @endphp

                                @if($action)
                                    @switch($action)
                                        @case('request_created')
                                            <span class="text-blue-600">Created</span>
                                        @break

                                        @case('request_edited')
                                            <span class="text-yellow-600">Edited</span>
                                        @break

                                        @case('request_accepted')
                                            <span class="text-green-600">Accepted</span>
                                        @break

                                        @case('request_declined')
                                            <span class="text-red-600">Declined</span>
                                        @break

                                        @case('request_cancelled')
                                            <span class="text-gray-600">Cancelled</span>
                                        @break

                                        @default
                                            <span class="text-gray-400">-</span>
                                    @endswitch
                                @else
                                    {{-- fallback logic for old logs --}}
                                    @if($log->action === 'request_edited')
                                        <span class="text-yellow-600">Edited</span>
                                    @elseif($log->handled_by === $user->id)
                                        @if($log->status === 'Active' || $log->status === 'Closed')
                                            <span class="text-green-600">Accepted</span>
                                        @elseif($log->status === 'Declined')
                                            <span class="text-red-600">Declined</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    @elseif($log->requested_by === $user->id)
                                        <span class="text-blue-600">Created</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                @endif
                            </div>

                            <div class="w-2/6 text-center">
                                {{ \Carbon\Carbon::parse($log->updated_at)->timezone('Asia/Manila')->format('M d, Y H:i') }}
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-gray-500 text-sm">
                        No logs available.
                    </div>
                @endforelse

            </div>
        </div>

    </div>
</x-app-layout>

<style>
    .material-symbols-outlined { font-size: 22px; }
    .request-history-wrapper::-webkit-scrollbar { width: 6px; }
    .request-history-wrapper::-webkit-scrollbar-track { background: transparent; }
    .request-history-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .request-history-wrapper::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
