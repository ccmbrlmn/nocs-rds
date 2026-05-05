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

            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
                {{ $admin->name }}'s Activity Logs
            </h1>

        </div>

        <div class="request-history-list rounded-xl shadow overflow-hidden"
             style="margin-left:4.5rem; margin-right:5rem;">

            <div class="head bg-blue-100 dark:bg-blue-900 px-4 py-2 flex justify-between text-sm font-semibold text-gray-700 dark:text-gray-200">
                <div class="w-1/6 text-center">ID</div>
                <div class="w-3/6 text-center">Event</div>
                <div class="w-1/6 text-center">Action</div>
                <div class="w-1/6 text-center">Date</div>
            </div>

            <div class="request-history-wrapper max-h-[60vh] overflow-y-auto">

                @forelse($combinedLogs as $item)

                    @php
                        $action = $item['action'] ?? 'unknown';
                        $type = $item['type'] ?? 'user_log';
                        $desc = $item['description'] ?? null;
                    @endphp

                    <div class="border-b border-gray-200 dark:border-gray-700">

                        <div class="flex justify-between items-center px-4 py-3 text-sm bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700">

                            {{-- ID --}}
                            <div class="w-1/6 text-center">
                                #{{ $item['id'] }}
                            </div>

                            {{-- EVENT (FULL DETAILS) --}}
                            <div class="w-3/6 text-left text-xs space-y-1">

                                {{-- ================= USER LOGS ================= --}}
                                @if($type === 'user_log')

                                    @switch($action)

                                        {{-- REQUEST ACTIONS --}}
                                        @case('request_created')
                                            <div class="font-semibold text-gray-800 dark:text-gray-200">
                                                Created Request
                                            </div>
                                            <div class="text-gray-500">
                                                {{ $desc }}
                                            </div>
                                        @break

                                        @case('request_edited')
                                            <div class="font-semibold">Edited Request</div>
                                            <div class="text-gray-500">{{ $desc }}</div>
                                        @break

                                        @case('request_cancelled_admin')
                                            <div class="font-semibold text-red-600">Cancelled Request</div>
                                            <div class="text-gray-500">{{ $desc }}</div>
                                        @break

                                        @case('request_approved')
                                            <div class="font-semibold text-green-600">Approved Request</div>
                                            <div class="text-gray-500">{{ $desc }}</div>
                                        @break

                                        {{-- USER ACCOUNT ACTIONS --}}
                                        @case('user_approved')
                                            <div class="font-semibold text-green-600">
                                                Approved User Registration
                                            </div>
                                            <div class="text-gray-500">
                                                {{ $item['target_user_name'] ?? 'User' }}
                                            </div>
                                        @break

                                        @case('user_declined')
                                            <div class="font-semibold text-red-600">
                                                Declined User Registration
                                            </div>
                                            <div class="text-gray-500">
                                                {{ $item['target_user_name'] ?? 'User' }}
                                            </div>
                                        @break

                                        @case('user_deleted')
                                            <div class="font-semibold text-red-700">
                                                Deleted User Account
                                            </div>
                                            <div class="text-gray-500">
                                                {{ $item['target_user_name'] ?? 'User' }}
                                            </div>
                                        @break

                                        @case('user_restored')
                                            <div class="font-semibold text-green-700">
                                                Restored User Account
                                            </div>
                                            <div class="text-gray-500">
                                                {{ $item['target_user_name'] ?? 'User' }}
                                            </div>
                                        @break

                                        @case('user_deletion_approved')
                                            <div class="font-semibold text-red-600">
                                                Approved Account Deletion
                                            </div>
                                            <div class="text-gray-500">
                                                {{ $item['target_user_name'] ?? 'User' }}
                                            </div>
                                        @break

                                        @case('user_deletion_declined')
                                            <div class="font-semibold text-yellow-600">
                                                Declined Account Deletion
                                            </div>
                                            <div class="text-gray-500">
                                                {{ $item['target_user_name'] ?? 'User' }}
                                            </div>
                                        @break

@case('profile_updated')
    <div class="font-semibold text-blue-600 mb-1">
        Updated Own Profile
    </div>

    @php
        $decoded = json_decode($desc, true);
    @endphp

    @if(!empty($decoded) && is_array($decoded))
        <div class="space-y-1 text-sm">
            @foreach($decoded as $field => $change)

                @php
                    $old = $change['old'] ?? '-';
                    $new = $change['new'] ?? '-';

                    while (is_array($old) && isset($old['old'])) {
                        $old = $old['old'];
                    }

                    while (is_array($new) && isset($new['new'])) {
                        $new = $new['new'];
                    }

                    if (is_array($old)) $old = implode(', ', $old);
                    if (is_array($new)) $new = implode(', ', $new);

                    $old = $old ?: '-';
                    $new = $new ?: '-';
                @endphp

                <div class="flex items-center gap-2 text-gray-600 dark:text-gray-300">

                    <span class="font-medium text-gray-800 dark:text-gray-200 min-w-[120px]">
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
        <div class="text-gray-500 italic">
            No readable changes
        </div>
    @endif
@break

                                        @case('user_delete_requested')
                                            <div class="font-semibold text-red-500">
                                                Requested Account Deletion
                                            </div>
                                        @break
                                        
                                        
                                        @case('user_updated')
                                        <div class="font-semibold text-blue-600">
                                            Updated User
                                        </div>
                                        <div class="text-gray-500">
                                            {{ $item['target_user_name'] ?? 'User' }}
                                        </div>
                                    @break


                                        @default
                                            <div>{{ $desc ?? ucfirst($action) }}</div>

                                    @endswitch

                                {{-- ================= ADMIN LOGS ================= --}}
                                @else

                                    @switch($action)

                                        @case('request_approved')
                                            <div class="font-semibold text-green-600">
                                                Approved Request
                                            </div>
                                            <div class="text-gray-500">
                                                {{ $desc }}
                                            </div>
                                        @break

                                        @case('request_cancelled_admin')
                                            <div class="font-semibold text-red-600">
                                                Cancelled Request
                                            </div>
                                            <div class="text-gray-500">{{ $desc }}</div>
                                        @break

@case('return_accepted')
    <div class="font-semibold text-blue-600">
        Accepted Return
    </div>

    @php
        $data = json_decode($item['description'] ?? '{}', true);
    @endphp

    <div class="text-gray-500 space-y-2 mt-2">

        {{-- EVENT --}}
        <div>
            <span class="font-medium">Event:</span>
            {{ $data['event_name'] ?? 'Unknown Event' }}
        </div>

        {{-- ASSETS --}}
        <div>
            <span class="font-medium">Assets assigned:</span>

            @if(!empty($data['assets']))
                <ul class="list-disc ml-5 mt-1">
                    @foreach($data['assets'] as $asset)
                        <li>{{ $asset['asset_name'] }}</li>
                    @endforeach
                </ul>
            @else
                <span class="text-gray-400">No assets found</span>
            @endif
        </div>

        {{-- PERSONNEL --}}
        <div>
            <span class="font-medium">Assigned Retrieval Personnel:</span>
            {{ $data['personnel_name'] ?? 'N/A' }}
        </div>

    </div>
@break



@case('assets_retrieved')
    <div class="font-semibold text-indigo-600">
        Retrieved Assets
    </div>

    @php
        $data = json_decode($desc, true);
    @endphp

    <div class="text-gray-500 space-y-2 mt-1">

        {{-- EVENT --}}
        <div>
            <span class="font-medium">Event:</span>
            {{ $data['event_name'] ?? 'Unknown Event' }}
        </div>

        {{-- ASSETS --}}
        <div>
            <span class="font-medium">Assets retrieved:</span>

            @if(!empty($data['assets']))
                <ul class="list-disc ml-5 mt-1">
                    @foreach($data['assets'] as $asset)
                        <li>{{ $asset['asset_name'] }}</li>
                    @endforeach
                </ul>
            @else
                <span class="text-gray-400">No assets found</span>
            @endif
        </div>

        {{-- HANDLED BY --}}
        <div>
            <span class="font-medium">Handled by:</span>
            {{ $data['handled_by'] ?? 'Admin' }}
        </div>
        

    </div>
@break


                                        
@case('profile_updated')
    <div class="font-semibold text-blue-600 mb-1">
        Updated Own Profile
    </div>

    @php
        $decoded = json_decode($desc, true);
    @endphp

    @if(!empty($decoded) && is_array($decoded))
        <div class="space-y-1 text-sm">
            @foreach($decoded as $field => $change)

                @php
                    $old = $change['old'] ?? '-';
                    $new = $change['new'] ?? '-';

                    while (is_array($old) && isset($old['old'])) {
                        $old = $old['old'];
                    }

                    while (is_array($new) && isset($new['new'])) {
                        $new = $new['new'];
                    }

                    if (is_array($old)) $old = implode(', ', $old);
                    if (is_array($new)) $new = implode(', ', $new);

                    $old = $old ?: '-';
                    $new = $new ?: '-';
                @endphp

                <div class="flex items-center gap-2 text-gray-600 dark:text-gray-300">

                    <span class="font-medium text-gray-800 dark:text-gray-200 min-w-[120px]">
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
        <div class="text-gray-500 italic">
            No readable changes
        </div>
    @endif
@break
                                        
                                        @case('user_delete_requested')
                                            <div class="font-semibold text-red-500">
                                                Requested Account Deletion
                                            </div>
                                            <div class="text-gray-500">
                                                {{ $item['event_name'] ?? 'Admin requested account deletion' }}
                                            </div>
                                        @break
                                    

                                        @case('user_updated')
                                            <div class="font-semibold text-blue-600 mb-1">
                                                Edited User Account
                                            </div>

                                            @php
                                                $decoded = json_decode($desc, true);
                                            @endphp

                                            <div class="text-gray-600 mb-1">
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

                                        @case('user_created')
                                            <div class="font-semibold text-blue-600">
                                                Created User Account
                                            </div>
                                            <div class="text-gray-500">
                                                {{ $item['target_user_name'] ?? 'User' }}
                                            </div>
                                        @break

                                        @case('user_approved')
                                            <div class="font-semibold text-green-600">
                                                Approved User Registration
                                            </div>
                                            <div class="text-gray-500">
                                                {{ $item['target_user_name'] ?? 'User' }}
                                            </div>
                                        @break
                                                      

                                        @case('user_deleted')
                                            <div class="font-semibold text-red-600">
                                                Deleted User Account
                                            </div>
                                            <div class="text-gray-500">
                                                {{ $item['target_user_name'] ?? 'User' }}
                                            </div>
                                        @break

                                        @case('user_restored')
                                            <div class="font-semibold text-green-600">
                                                Restored User Account
                                            </div>
                                            <div class="text-gray-500">
                                                {{ $item['target_user_name'] ?? 'User' }}
                                            </div>
                                        @break


                                                                      
                                        @case('asset_created')
                                            <div class="font-semibold text-green-600">
                                                Created Asset
                                            </div>

                                            @php
                                                $decoded = json_decode($desc, true);
                                            @endphp

                                            <div class="text-gray-500 text-sm">
                                                {{ $decoded['asset_name'] ?? 'Asset' }}
                                            </div>
                                        @break

                                        @case('asset_updated')
                                            <div class="font-semibold text-blue-600 mb-1">
                                                Updated Asset
                                            </div>

                                            @php
                                                $decoded = json_decode($desc, true);
                                            @endphp

                                            @if(!empty($decoded) && is_array($decoded))
                                                <div class="space-y-1 text-sm">
                                                    @foreach($decoded as $field => $change)

                                                        @php
                                                            $old = $change['old'] ?? '-';
                                                            $new = $change['new'] ?? '-';
                                                        @endphp

                                                        <div class="flex items-center gap-2 text-gray-600">

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
                                            @endif
                                        @break                          

                                        @default
                                            <div class="text-gray-500">Admin Action</div>

                                    @endswitch

                                @endif

                            </div>

                            {{-- ACTION BADGE --}}
                            <div class="w-1/6 text-center">

                                @if($type === 'user_log')

                                    <span class="text-gray-500">User</span>

                                @else

                                    @switch($action)
                                        @case('request_approved')
                                            <span class="text-green-600">Approved Request</span>
                                        @break

                                        @case('request_cancelled_admin')
                                            <span class="text-red-600">Cancelled Request</span>
                                        @break

                                        @case('return_accepted')
                                            <span class="text-blue-600">Returned Request</span>
                                        @break

                                        @case('assets_retrieved')
                                            <span class="text-indigo-600">Retrieved Request</span>
                                        @break
                                        
                                        @case('profile_updated')
                                            <span class="text-blue-600">Updated Profile</span>
                                        @break
                                        
                                        @case('user_delete_requested')
                                            <span class="text-red-500">Account Deletion</span>
                                        @break
                                        
                                        @case('user_created')
                                            <span class="text-blue-600">Created User</span>
                                        @break

                                        @case('user_registered')
                                            <span class="text-indigo-600">Registered User</span>
                                        @break
                                        
                                        @case('user_approved')
                                            <span class="text-green-600">Approved User</span>
                                        @break
                                        
                                        @case('user_updated')
                                            <span class="text-green-600">Edited User</span>
                                        @break
                                        
                                        @case('user_deleted')
                                            <span class="text-indigo-600">Deleted User</span>
                                        @break
                                        
                                        @case('user_restored')
                                            <span class="text-green-600">Restored User</span>
                                        @break
                                        
                                        @case('asset_created')
                                            <span class="text-indigo-600">Created Asset</span>
                                        @break
                                        
                                        @case('asset_updated')
                                            <span class="text-green-600">Updated Asset</span>
                                        @break

                                        @default
                                            <span class="text-gray-400">Admin</span>
                                    @endswitch

                                @endif

                            </div>

                            {{-- DATE --}}
                            <div class="w-1/6 text-center text-xs">
                                {{ \Carbon\Carbon::parse($item['updated_at'])
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
    .request-history-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
</style>
