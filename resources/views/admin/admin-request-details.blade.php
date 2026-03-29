<x-app-layout>
    <div class="header-container flex items-center justify-between p-3 mt-8 mb-6">

    <a href="{{ route('admin.requests') }}"
       class="px-4 py-2 rounded-xl text-sm font-medium transition 
              bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 
              hover:bg-blue-100 dark:hover:bg-gray-600 shadow-sm flex items-center gap-2">

        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Back
    </a>

    <div class="flex items-center gap-3">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
            Request Application
        </h1>
    </div>

    </div>

    <div class="request-details p-6 rounded-2xl bg-white dark:bg-gray-800 shadow-sm">

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="mb-5 p-4 bg-green-100 text-green-700 rounded-xl flex items-center justify-between shadow-sm">
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="font-bold hover:text-green-900">✕</button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition class="mb-5 p-4 bg-red-100 text-red-700 rounded-xl flex items-center justify-between shadow-sm">
                <span>{{ session('error') }}</span>
                <button @click="show = false" class="font-bold hover:text-red-900">✕</button>
            </div>
        @endif

    <div class="request-header flex flex-wrap items-start justify-between border-b border-gray-200 dark:border-gray-500 pb-5 mb-6">
        <div class="flex items-center gap-3 flex-wrap">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
    {{ $request->event_name ?? 'Unknown Event' }}
            </h2>

                @php
                    $status = $request->status;

                    $statusClasses = [
                        'Open' => 'bg-amber-100 text-amber-700 dark:bg-amber-700 dark:text-amber-200',
                        'Active' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-700 dark:text-indigo-200',
                        'Closed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-200',
                        'Declined' => 'bg-rose-100 text-rose-700 dark:bg-rose-700 dark:text-rose-200',
                    ];

                    $statusColor = $statusClasses[$status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200';
                    @endphp

                <span class="inline-flex items-center gap-2 px-4 py-1 rounded-xl text-sm font-semibold shadow-sm {{ $statusColor }}">
                    <span class="material-symbols-outlined text-sm">info</span>
                    {{ $status }}
                </span>
            </div>

            <div class="flex gap-3 items-center mt-3 sm:mt-0">
                @auth
                    @if(auth()->user()->role === 'admin' && $request->status === 'Open')

<div x-data="{ openAccept: false }">
    <x-primary-button @click="openAccept = true" class="shadow-md" style="background-color: #22C55E; color: white; width: 110px; height: 42px;">
        Accept
    </x-primary-button>

    <div x-show="openAccept" x-cloak class="fixed inset-0 bg-black bg-opacity-25 z-50 flex items-center justify-center px-4">
        <div class="bg-white rounded-xl w-full max-w-sm p-5 border border-gray-200 dark:border-gray-500 shadow-sm" @click.away="openAccept = false">
            <h2 class="text-lg font-semibold mb-2">Confirm Acceptance</h2>
            <p class="mb-4 text-gray-600">Are you sure you want to accept this request?</p>
            <div class="flex justify-end gap-2">
                <button @click="openAccept = false" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">Cancel</button>
                <form action="{{ route('admin.requests.accept', $request->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 transition">Yes, Accept</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div x-data="{ openDecline: false, reason: '' }">
    <x-primary-button @click="openDecline = true" class="shadow-md" style="background-color: #EF4444; color: white; width: 110px; height: 42px;">
        Decline
    </x-primary-button>

    <div x-show="openDecline" x-cloak class="fixed inset-0 bg-black bg-opacity-25 z-50 flex items-center justify-center px-4">
        <div class="bg-white rounded-xl w-full max-w-sm p-5 border border-gray-200 dark:border-gray-500 shadow-sm" @click.away="openDecline = false">
            <h2 class="text-lg font-semibold mb-2">Decline Request</h2>
            <p class="mb-2 text-gray-600">Please provide a reason:</p>
            <textarea x-model="reason" class="w-full p-2 border border-gray-300 rounded-md mb-4 text-sm" rows="3" placeholder="Enter reason" required></textarea>
            <div class="flex justify-end gap-2">
                <button @click="openDecline = false" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">Cancel</button>
                <form :action="'{{ route('admin.requests.decline', $request->id) }}'" method="POST">
                    @csrf
                    <input type="hidden" name="decline_reason" :value="reason">
                    <button type="submit" :disabled="reason === ''" class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Decline
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


                    @endif
                @endauth
            </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-500 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-8">

                <div class="space-y-6">
                    <div class="flex items-start gap-4 p-3 rounded-lg transition">
                        <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">location_on</span>
                        <div>
                            <p class="dark:text-gray-300 header-text font-semibold">Location</p>
                            <p class="dark:text-gray-300 detail-text">{{ $request->location }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-3 rounded-lg transition">
                        <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">event</span>
                        <div>
                            <p class="dark:text-gray-300 header-text font-semibold">Name of Requester</p>
                            <p class="dark:text-gray-300 detail-text">{{ optional($request->user)->name ?? 'Unknown User' }}</p>                                
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-3 rounded-lg transition">
                        <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">event_available</span>
                        <div>
                            <p class="dark:text-gray-300 header-text font-semibold">Purpose of the Event</p>
                            <p class="dark:text-gray-300 detail-text">{{ $request->purpose }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-3 rounded-lg transition">
                        <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">inventory_2</span>
                        <div>
                            <p class="dark:text-gray-300 header-text font-semibold">Requested Items</p>
                            @php
                                $items = is_array($request->items) ? $request->items : json_decode($request->items, true) ?? [];
                            @endphp
                            <ul class="dark:text-gray-300 detail-text">
                                @if(count($items) > 0)
                                    @foreach($items as $item)
                                        <li>{{ $item['quantity'] }} {{ $item['name'] }}</li>
                                    @endforeach
                                @else
                                    <li>No items requested</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="flex items-start gap-4 p-3 rounded-lg transition">
                        <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">calendar_clock</span>
                        <div>
                            <p class="dark:text-gray-300 header-text font-semibold">Date of Event</p>
                            <p class="dark:text-gray-300 detail-text">
                                {{ \Carbon\Carbon::parse($request->start_date)->format('M d') }} - 
                                {{ \Carbon\Carbon::parse($request->end_date)->format('d, Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-3 rounded-lg transition">
                        <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">calendar_clock</span>
                        <div>
                            <p class="dark:text-gray-300 header-text font-semibold">Request Set-up Date</p>
                            <p class="dark:text-gray-300 detail-text">{{ $request->setup_date }} | {{ $request->setup_time }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-3 rounded-lg transition">
                        <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">group</span>
                        <div>
                            <p class="dark:text-gray-300 header-text font-semibold">No. of Users</p>
                            <p class="dark:text-gray-300 detail-text">
                                {{ number_format($request->users) }} {{ \Illuminate\Support\Str::plural('User', $request->users) }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-3 rounded-lg transition">
                        <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">email</span>
                        <div>
                            <p class="dark:text-gray-300 header-text font-semibold">Requester Contact Information</p>
                            <p class="dark:text-gray-300 detail-text">{{ optional($request->user)->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-500">

    @if(in_array($request->status, ['Active', 'Closed', 'Declined']))
        <div class="space-y-6">

@if($request->handledByAdmin)
<div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-8">

            @php
                $actionStatus = $request->status;
                if($actionStatus === 'Declined') {
                    $icon = 'cancel';
                    $actionText = 'Declined the Request';
                    $iconBg = 'bg-red-600';
                } elseif($actionStatus === 'Active' || 'Closed') {
                    $icon = 'check_circle';
                    $actionText = 'Accepted the Request';
                    $iconBg = 'bg-green-600';
                } else {
                    $icon = 'info';
                    $actionText = 'No action yet';
                    $iconBg = 'bg-gray-400';
                }
            @endphp

    <div class="flex items-start gap-4 p-3 rounded-lg transition">
        <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">manage_accounts</span>
        <div>
            <p class="header-text font-semibold dark:text-gray-300">Name of Personnel</p>
            @php
                $admin = $request->handledByAdmin;
                $adminClass = ($admin && $admin->trashed()) ? 'text-red-600 italic' : 'text-gray-800';
            @endphp
            <p class="dark:text-gray-300 detail-text {{ $adminClass }}">
                {{ $admin->name ?? 'Unknown Admin' }}
                @if($admin && $admin->trashed())
                    (Deleted)
                @endif
            </p>
        </div>
    </div>

    <div class="flex items-start gap-4 p-3 rounded-lg transition">
        <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">{{ $icon }}</span>
        <div>
            <p class="header-text font-semibold dark:text-gray-300">Personnel Action</p>
            <p class="detail-text dark:text-gray-300">{{ $actionText }}</p>
        </div>
    </div>

</div>
@endif



            @if($request->status === 'Declined' && $request->decline_reason)
                <div class="flex items-start gap-4 p-3 rounded-lg transition">
                    <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">info</span>
                    <div>
                        <p class="header-text font-semibold dark:text-gray-300">Decline Reason</p>
                        <p class="detail-text dark:text-gray-300">{{ $request->decline_reason }}</p>
                    </div>
                </div>
            @endif

        </div>
    @else
        <p class="detail-text text-gray-500 dark:text-gray-200">No deployment information available yet.</p>
    @endif

</div>


        </div>

    </div>
</x-app-layout>

<style>
.material-symbols-outlined{
    font-size:22px;
}

.request-details{
    margin-left:4.5rem;
    margin-right:5rem;
}

.header-container{
    margin-left:5rem;
    margin-right:5rem;
}

.header-text{
    font-size:0.85rem;
    font-weight:600;
    color:#6B7280;
}

.detail-text{
    font-size:0.95rem;
    color:#1F2937;
}

h1{
    font-size:1.5rem;
    font-weight:600;
}

[x-cloak]{
    display:none !important;
}

</style>

