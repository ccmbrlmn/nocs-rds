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

            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
                {{ $user->name }}'s Activity Logs
            </h1>

        </div>

        <div class="request-history-list rounded-xl shadow overflow-hidden"
             style="margin-left:4.5rem; margin-right:5rem;">

            <div class="head bg-blue-100 dark:bg-blue-900 px-4 py-2 flex justify-between text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-t-xl">
                <div class="w-1/6 text-center">ID</div>
                <div class="w-2/6 text-center">Event</div>
                <div class="w-1/6 text-center">Action</div>
                <div class="w-2/6 text-center">Date</div>
            </div>

            <div class="request-history-wrapper max-h-[60vh] overflow-y-auto">

                @forelse($logs as $log)
                    @php
                        $action = $log->action;
                        $desc = $log->description;
                        $data = is_array($desc)
                            ? $desc
                            : json_decode($desc, true);
                    @endphp

                    <div class="block hover:bg-blue-50 transition border-b border-gray-200">

                        <div class="flex justify-between items-center px-4 py-3 text-sm dark:text-gray-200 bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700">

                            {{-- ID --}}
                            <div class="w-1/6 text-center">
                                #{{ $log->id }}
                            </div>

                            {{-- EVENT --}}
                            <div class="w-2/6 text-left text-xs space-y-1">

                                @switch($action)

                                    {{-- ================= USER REQUEST ACTIONS ================= --}}

                                    @case('request_created')
                                        <div class="font-semibold text-blue-700">Created Request</div>
                                        <div class="text-gray-500 mt-1 space-y-1">{{ $data['event_name'] ?? 'Unknown' }}</div>
                                    @break

                                    @case('request_edited')
                                        <div class="font-semibold text-yellow-600">Updated Request:</div>

                                        @if(is_array($data))
                                            <div class="text-gray-500 mt-1 space-y-1">
                                                @foreach($data as $field => $change)
                                                    <div>
                                                        <span class="font-medium">
                                                            {{ ucfirst(str_replace('_', ' ', $field)) }}:
                                                        </span>

                                                        <span class="text-red-500">
                                                            {{ is_array($change['old'] ?? null) ? json_encode($change['old']) : ($change['old'] ?? '-') }}
                                                        </span>

                                                        →

                                                        <span class="text-green-600">
                                                            {{ is_array($change['new'] ?? null) ? json_encode($change['new']) : ($change['new'] ?? '-') }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-gray-500">No changes detected</div>
                                        @endif
                                    @break

@case('return_requested')
    <div class="font-semibold text-orange-600">
        Requested Return
    </div>

    <div class="text-gray-500 mt-1 space-y-1">

        {{-- EVENT NAME (CONTEXT) --}}
        <div>
            <span class="font-medium">Event:</span>
            {{ $log->request->event_name ?? ($data['event_name'] ?? 'Unknown Event') }}
        </div>
        
        @if($log->request?->personnel_name)
            <div>
                <span class="font-medium">Assigned Personnel:</span>
                {{ $log->request->personnel_name }}
            </div>
        @elseif($log->request?->handler)
            <div>
                <span class="font-medium">Handled By:</span>
                {{ $log->request->handler->name }}
            </div>
        @endif

        {{-- ASSETS --}}
        <div>
            <span class="font-medium">Assets assigned:</span>

            @if($log->request && $log->request->assetTransactions->count())
                <ul class="list-disc ml-5 mt-1">
                    @foreach($log->request->assetTransactions as $tx)
                        @if($tx->asset)
                            <li>
                                {{ $tx->asset->asset_name ?? $tx->asset->name ?? 'Unnamed Asset' }}
                            </li>
                        @endif
                    @endforeach
                </ul>
            @else
                <span class="text-gray-400">No assets assigned</span>
            @endif
        </div>

    </div>
@break

                                    {{-- ================= SYSTEM / ADMIN RESPONSE (USER VIEW) ================= --}}

                                    @case('request_approved')
                                        <div class="font-semibold text-red-600">Request Approved</div>
                                        <div class="text-gray-500 mt-1 space-y-1">{{ $log->request->event_name ?? ($data['event_name'] ?? 'Unknown Event') }}</div>
                                        
                                    @break

                                    @case('request_cancelled_admin')
                                        <div class="font-semibold text-red-600">Request Cancelled</div>
                                        <div class="text-gray-500 mt-1 space-y-1">{{ $log->request->event_name ?? ($data['event_name'] ?? 'Unknown Event') }}</div>
                                    @break

                                    @case('return_accepted')
                                        <div class="font-semibold text-orange-600">
                                            Return request accepted
                                        </div>

                                        <div class="text-gray-500 mt-1 space-y-1">

                                            {{-- EVENT NAME (CONTEXT) --}}
                                            <div>
                                                <span class="font-medium">Event:</span>
                                                {{ $log->request->event_name ?? ($data['event_name'] ?? 'Unknown Event') }}
                                            </div>

                                            {{-- ASSETS --}}
                                            <div>
                                                <span class="font-medium">Assets assigned:</span>

                                                @if($log->request && $log->request->assetTransactions->count())
                                                    <ul class="list-disc ml-5 mt-1">
                                                        @foreach($log->request->assetTransactions as $tx)
                                                            @if($tx->asset)
                                                                <li>
                                                                    {{ $tx->asset->asset_name ?? $tx->asset->name ?? 'Unnamed Asset' }}
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-gray-400">No assets assigned</span>
                                                @endif
                                            </div>

                                            @if($log->request?->personnel_name)
                                                <div>
                                                    <span class="font-medium">Assigned Retrieval Personnel:</span>
                                                    {{ $log->request->personnel_name }}
                                                </div>
                                            @endif

                                        </div>
                                    @break

                                    @case('assets_retrieved')
                                        <div class="font-semibold text-orange-600">
                                            Retrieved Asset
                                        </div>

                                        <div class="text-gray-500 mt-1 space-y-1">

                                            {{-- EVENT NAME (CONTEXT) --}}
                                            <div>
                                                <span class="font-medium">Event:</span>
                                                {{ $log->request->event_name ?? ($data['event_name'] ?? 'Unknown Event') }}
                                            </div>

                                            {{-- ASSETS --}}
                                            <div>
                                                <span class="font-medium">Assets assigned:</span>

                                                @if($log->request && $log->request->assetTransactions->count())
                                                    <ul class="list-disc ml-5 mt-1">
                                                        @foreach($log->request->assetTransactions as $tx)
                                                            @if($tx->asset)
                                                                <li>
                                                                    {{ $tx->asset->asset_name ?? $tx->asset->name ?? 'Unnamed Asset' }}
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-gray-400">No assets assigned</span>
                                                @endif
                                            </div>

                                        </div>
                                    @break


                                    {{-- ================= USER PROFILE ACTIONS ================= --}}

                                    @case('profile_updated')
                                        <div class="font-semibold text-blue-600">Profile Updated</div>

                                        @if(is_array($data))
                                            <div class="text-gray-500 mt-1 space-y-1">
                                                @foreach($data as $field => $change)
                                                    <div>
                                                        <span class="font-medium">
                                                            {{ ucfirst(str_replace('_', ' ', $field)) }}:
                                                        </span>

                                                        <span class="text-red-500">
                                                            {{ is_array($change['old'] ?? null) ? json_encode($change['old']) : ($change['old'] ?? '-') }}
                                                        </span>

                                                        →

                                                        <span class="text-green-600">
                                                            {{ is_array($change['new'] ?? null) ? json_encode($change['new']) : ($change['new'] ?? '-') }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-gray-500">
                                                Your profile information was updated
                                            </div>
                                        @endif
                                    @break

                                    @case('user_created')
                                        <div class="font-semibold text-green-600">Account Created</div>
                                        <div class="text-gray-500">
                                            Welcome to the system
                                        </div>
                                    @break
                                    
                                    @case('user_approved')
                                        <div class="font-semibold text-green-700">Account Approved</div>
                                        <div class="text-gray-500">
                                            Your account has been approved by admin
                                        </div>
                                    @break
                                    
                                    @case('user_updated')
                                        <div class="font-semibold text-blue-600 mb-1">
                                            Admin edited your account
                                        </div>

                                        @php
                                            $decoded = json_decode($desc, true);
                                        @endphp

                                        <div class="text-gray-500 mb-1">
                                            {{ $item['target_user_name'] ?? 'User' }}
                                        </div>

                                        @if(!empty($decoded) && is_array($decoded))
                                            <div class="space-y-1 text-sm">
                                                @foreach($decoded as $field => $change)

                                                    @php
                                                        $old = $change['old'] ?? '-';
                                                        $new = $change['new'] ?? '-';

                                                        if (is_array($old)) $old = implode(', ', $old);
                                                        if (is_array($new)) $new = implode(', ', $new);

                                                        $old = $old ?: '-';
                                                        $new = $new ?: '-';
                                                    @endphp

                                                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-300">

                                                        <span class="font-medium min-w-[120px]">
                                                            {{ ucfirst(str_replace('_', ' ', $field)) }}:
                                                        </span>

                                                        <span class="text-red-500 line-through">
                                                            {{ $old }}
                                                        </span>

                                                        <span class="text-gray-400">→</span>

                                                        <span class="text-green-600 font-medium">
                                                            {{ $new }}
                                                        </span>

                                                    </div>

                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-gray-400 italic text-sm">
                                                No changes recorded
                                            </div>
                                        @endif
                                    @break
                                    
                                    @case('user_deleted')
                                            <div class="font-semibold text-red-700">
                                                Account Deleted
                                            </div>
                                            <div class="text-gray-500">
                                                Admin deleted your account
                                            </div>
                                    @break
                                    
                                    @case('user_restored')
                                            <div class="font-semibold text-green-700">
                                                Account Restored
                                            </div>
                                            <div class="text-gray-500">
                                                Admin restored your account
                                            </div>
                                    @break
                                        

                                    @case('user_delete_requested')
                                        <div class="font-semibold text-red-500">Account Deletion Requested</div>
                                        <div class="text-gray-500">
                                            You requested account deletion
                                        </div>
                                    @break

                                    {{-- DEFAULT --}}
                                    @default
                                        <div class="text-gray-500">
                                            Activity recorded
                                        </div>

                                @endswitch

                            </div>

                            {{-- ACTION BADGE --}}
                            <div class="w-1/6 text-center">

                                @switch($action)

                                    @case('request_created')
                                        <span class="text-blue-600">Created Request</span>
                                    @break

                                    @case('request_edited')
                                        <span class="text-yellow-600">Updated Request</span>
                                    @break

                                    @case('return_requested')
                                        <span class="text-orange-600">Requested Return</span>
                                    @break

                                    @case('request_approved')
                                        <span class="text-green-600">Approved Request</span>
                                    @break

                                    @case('request_cancelled_admin')
                                        <span class="text-red-600">Cancelled Request</span>
                                    @break

                                    @case('return_accepted')
                                        <span class="text-blue-600">Requested Return Approved</span>
                                    @break

                                    @case('assets_retrieved')
                                        <span class="text-indigo-600">Transaction Completed</span>
                                    @break

                                    @case('profile_updated')
                                        <span class="text-blue-600">Updated Account</span>
                                    @break

                                    @case('user_created')
                                        <span class="text-green-600">Created Account</span>
                                    @break
                                    
                                    @case('user_approved')
                                        <span class="text-green-700">Approved Account</span>
                                    @break
                                    
                                    @case('user_updated')
                                        <span class="text-green-700">Updated Account</span>
                                    @break
                                    
                                                                        
                                    @case('user_deleted')
                                        <span class="text-green-700">Deleted Account</span>
                                    @break
                                    
                                    @case('user_restored')
                                        <span class="text-green-700">Restored Account</span>
                                    @break

                                    @case('user_delete_requested')
                                        <span class="text-red-600">Deletion Request</span>
                                    @break

                                    @default
                                        <span class="text-gray-400">-</span>

                                @endswitch

                            </div>

                            {{-- DATE --}}
                            <div class="w-2/6 text-center text-xs">
                                {{ \Carbon\Carbon::parse($log->created_at)
                                    ->timezone('Asia/Manila')
                                    ->format('M d, Y H:i') }}
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
