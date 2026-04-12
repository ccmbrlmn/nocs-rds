<x-app-layout>
    <div class="page-wrapper flex flex-col h-screen">

        <div class="header-container flex items-center justify-between p-3 mt-8 mb-6" 
             style="margin-left:5rem; margin-right:5rem;">

            <a href="{{ route('admin.created-admins') }}"
               class="px-4 py-2 rounded-xl text-sm font-medium transition 
                      bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 
                      hover:bg-blue-100 dark:hover:bg-gray-600 shadow-sm flex items-center gap-2">

                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Back
            </a>

            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
                    {{ $admin->name }}'s Logs
                </h1>
            </div>

        </div>
        
        <div class="request-history-list rounded-xl shadow overflow-hidden" 
             style="margin-left:4.5rem; margin-right:5rem;">

            <div class="head bg-blue-100 dark:bg-blue-900 px-4 py-2 flex justify-between text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-t-xl">
                <div class="w-1/6 text-center">Request No.</div>
                <div class="w-2/6 text-center">Event</div>
                <div class="w-1/6 text-center">Action</div>
                <div class="w-2/6 text-center">Date</div>
            </div>

            <div class="request-history-wrapper max-h-[60vh] overflow-y-auto">

                @forelse($combinedLogs as $item)
                @php
    $action = $item['action'] ?? 'unknown';
    $target = $item['target_user_name'] ?? null;
@endphp
                    <div class="block hover:bg-blue-50 transition border-b border-gray-200">
                        <div class="flex justify-between items-center px-4 py-3 text-sm dark:text-gray-200 bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700">

                            <!-- ID -->
                            <div class="w-1/6 text-center">
                                #{{ $item['id'] }}
                            </div>

                <div class="w-2/6 text-center">
                    @if($item['type'] === 'user_log')

                        @if($item['action'] === 'user_delete_requested')
                            Requested account deletion

                        @elseif($item['action'] === 'profile_updated')
                            Conducted a profile update

                        @elseif($item['action'] === 'user_approved')
                            @if($target)
                                Approved user registration of {{ $target }}
                            @else
                                Approved user registration
                            @endif

                        @elseif($item['action'] === 'user_declined')
                            @if($target)
                                Declined user registration of {{ $target }}
                            @else
                                Declined user registration
                            @endif

                        @elseif($item['action'] === 'user_updated')
                            @if($target)
                                Edited user information of {{ $target }}
                            @else
                                Edited user information
                            @endif

                        @elseif($item['action'] === 'user_deleted')
                            @if($target)
                                Deleted user account of {{ $target }}
                            @else
                                Deleted a user account
                            @endif

                        @elseif($item['action'] === 'user_restored')
                            @if($target)
                                Restored user account of {{ $target }}
                            @else
                                Restored a user account
                            @endif

                        @else
                            {{ ucfirst(str_replace('_', ' ', $item['action'])) }}

                            @php
                                $desc = $item['description'] ?? null;
                                $data = json_decode($desc, true);
                            @endphp

                            @if(is_array($data))
                                <br>
                                Edited:
                                <br>
                                <span class="text-gray-500 text-xs">
                                    {{ $data['old']['event_name'] ?? 'N/A' }}
                                </span>
                                →
                                <span class="text-green-600 text-xs">
                                    {{ $data['new']['event_name'] ?? 'N/A' }}
                                </span>
                            @elseif($desc)
                                <br>
                                Edited: {{ $desc }}
                            @endif

                        @endif

                    @else
                        @php
                            $event = $item['event_name'] ?? '';
                            $requester = $item['user_name'] ?? '';
                        @endphp

                        @if($item['status'] === 'Active' || $item['status'] === 'Closed')
                            Accepted {{ $event }} for {{ $requester }}
                        @elseif($item['status'] === 'Declined')
                            Declined {{ $event }} for {{ $requester }}
                        @else
                            {{ $event }}
                        @endif

                    @endif
                </div>

                            <div class="w-1/6 text-center">
                                @if($item['type'] === 'user_log')

                                    @if($item['action'] === 'profile_updated')
                                        <span class="text-blue-600">Profile Updated</span>
                                    @elseif($item['action'] === 'user_delete_requested')
                                        <span class="text-red-600">Requested Account Deletion</span>
                                    @elseif($item['action'] === 'user_approved')
                                        <span class="text-green-600">Approved User</span>
                                    @elseif($item['action'] === 'user_declined')
                                        <span class="text-red-600">Declined User</span>
                                    @elseif($item['action'] === 'user_updated')
                                        <span class="text-yellow-600">Edited User</span>
                                    @elseif($item['action'] === 'user_deleted')
                                        <span class="text-red-700">Deleted User</span>
                                    @elseif($item['action'] === 'user_restored')
                                        <span class="text-green-700">Restored User</span>
                                    @elseif($item['action'] === 'admin_created')
                                        <span class="text-blue-700">Created Admin</span>
                                    @elseif($item['action'] === 'account_registered')
                                        <span class="text-green-600">Registered</span>
                                    @else
                                        <span class="text-indigo-600">Performed</span>
                                    @endif

                                @else
                                    @if($item['status'] === 'Active' || $item['status'] === 'Closed')
                                        <span class="text-green-600">Accepted Request</span>
                                    @elseif($item['status'] === 'Declined')
                                        <span class="text-red-600">Declined Request</span>
                                    @else
                                        <span class="text-gray-500">Unknown</span>
                                    @endif
                                @endif
                            </div>

                            <!-- DATE -->
                            <div class="w-2/6 text-center">
                                {{ \Carbon\Carbon::parse(
                                    $item['type'] === 'user_log' 
                                        ? $item['updated_at'] 
                                        : $item['handled_at']
                                )->timezone('Asia/Manila')->format('M d, Y H:i') }}
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
    .sort-select { appearance: none; }
    
    .highlighted-user {
        background-color: #dbeafe;
        border-left: 4px solid #3b82f6;
        transition: all 0.3s ease;
    }

    .dark .highlighted-user {
        background-color: #1e3a8a33;
        border-left: 4px solid #60a5fa;
    }

</style>
