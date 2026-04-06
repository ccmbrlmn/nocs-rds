<x-app-layout>

<div class="header-container flex items-center justify-between p-3 mt-8 mb-6">

    <a href="{{ route('user.requests') }}"
       class="px-4 py-2 rounded-xl text-sm font-medium transition 
              bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 
              hover:bg-blue-100 dark:hover:bg-gray-600 shadow-sm flex items-center gap-2">

        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Back
    </a>

    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
        Request Application
    </h1>
</div>

<div class="request-details p-6 rounded-2xl bg-white dark:bg-gray-800 shadow-sm">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition
            class="mb-5 p-4 bg-green-100 text-green-700 rounded-xl flex items-center justify-between shadow-sm">
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="font-bold hover:text-green-900">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition
            class="mb-5 p-4 bg-red-100 text-red-700 rounded-xl flex items-center justify-between shadow-sm">
            <span>{{ session('error') }}</span>
            <button @click="show = false" class="font-bold hover:text-red-900">✕</button>
        </div>
    @endif

    @php
        $status = $request->computed_status;

        $statusClasses = [
            'Open' => 'bg-amber-100 text-amber-700 dark:bg-amber-700 dark:text-amber-200',
            'Active' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-700 dark:text-indigo-200',
            'Closed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-200',
            'Declined' => 'bg-rose-100 text-rose-700 dark:bg-rose-700 dark:text-rose-200',
        ];

        $statusColor = $statusClasses[$status] ?? 'bg-gray-100 text-gray-700';
    @endphp

    <div class="request-header flex items-center justify-between border-b border-gray-200 dark:border-gray-500 pb-5 mb-6">

        <div class="flex items-center gap-3 flex-wrap">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
                {{ $request->event_name ?? 'Unknown Event' }}
            </h2>

            <span class="inline-flex items-center gap-2 px-4 py-1 rounded-xl text-sm font-semibold shadow-sm {{ $statusColor }}">
                <span class="material-symbols-outlined text-sm">info</span>
                {{ $status }}
            </span>
        </div>


@auth
<div x-data="{ openEdit: false, confirmEdit: false }"
     @close-edit.window="openEdit = false"
     class="flex items-center ml-auto">

    <div class="flex gap-3 items-center mt-3 sm:mt-0 ml-auto">
    @if($request->is_edited)
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
        You have already edited this request.
    </p>
@endif

        @if(
    strtolower($request->status) === 'open' &&
    auth()->id() === $request->requested_by &&
    !$request->is_edited
)
            <x-primary-button 
                class="bg-blue-500 hover:bg-blue-600 text-white"
                @click="confirmEdit = true">
                Edit
            </x-primary-button>
        @endif
    </div>
    
    <!-- CONFIRM EDIT MODAL -->
<div x-show="confirmEdit" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">

    <div @click.away="confirmEdit = false"
         class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6 mx-4">

        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
            Confirm Edit
        </h2>

        <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
            Are you sure you want to edit this request? <br>
            <span class="font-semibold text-red-500">
                You can only edit this once.
            </span>
        </p>

        <div class="flex justify-end gap-3">
            <button @click="confirmEdit = false"
                    class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-300">
                Cancel
            </button>

            <button @click="confirmEdit = false; openEdit = true"
                    class="px-4 py-2 rounded-lg bg-blue-500 text-white hover:bg-blue-600">
                Yes, Edit
            </button>
        </div>

    </div>
</div>

    @if(
    strtolower($request->status) === 'open' &&
    auth()->id() === $request->requested_by &&
    !$request->is_edited
)
    
    <div x-show="openEdit" x-cloak
         x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">

<div class="bg-white dark:bg-gray-800 w-full max-w-5xl rounded-2xl shadow-xl
    max-h-[90vh] overflow-y-auto p-6 mx-4"
@click.away="openEdit = false">

    @include('form.edit-request-form', ['request' => $request])
</div>
    </div>
    
    
    @endif

</div>
@endauth

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
                        <p class="dark:text-gray-300 header-text font-semibold">Name of Event</p>
                        <p class="dark:text-gray-300 detail-text">{{ $request->event_name }}</p>
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
                            @if(count($items))
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
                        <p class="dark:text-gray-300 detail-text">
                            @php
                                $dateOnly = \Carbon\Carbon::parse($request->setup_date)->format('Y-m-d');
                                $setupDateTime = $request->setup_time ? "$dateOnly $request->setup_time" : $dateOnly;
                            @endphp
                            {{ \Carbon\Carbon::parse($setupDateTime)->format($request->setup_time ? 'M d, Y | h:i A' : 'M d, Y') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-3 rounded-lg transition">
                    <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">group</span>
                    <div>
                        <p class="dark:text-gray-300 header-text font-semibold">No. of Users</p>
                        <p class="dark:text-gray-300 detail-text">
                            {{ $request->users }} {{ $request->users == 1 ? 'user' : 'users' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-3 rounded-lg transition">
                    <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">email</span>
                    <div>
                        <p class="dark:text-gray-300 header-text font-semibold">Requester Contact Information</p>
                        <p class="dark:text-gray-300 detail-text">{{ $request->user->email }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-500">
        @if(in_array($request->status, ['Active', 'Closed', 'Declined']))
            <div class="space-y-6">

                @if($request->handledByAdmin)
                    <div class="flex items-start gap-4 p-3 rounded-lg transition">
                        <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">manage_accounts</span>
                        <div>
                            <p class="header-text font-semibold dark:text-gray-300">Name of Personnel</p>
                            <p class="dark:text-gray-300 detail-text">{{ $request->handledByAdmin->name }}</p>
                        </div>
                    </div>
                @endif

                @php
                    $actionStatus = $request->status;
                    if($actionStatus === 'Declined') {
                        $icon = 'cancel';
                        $actionText = 'Declined the Request';
                        $iconBg = 'bg-red-600';
                    } elseif($actionStatus === 'Closed' || $actionStatus === 'Active') {
                        $icon = 'task_alt';
                        $actionText = 'Accepted the Request';
                        $iconBg = 'bg-green-600';
                    } else {
                        $icon = 'info';
                        $actionText = 'No action yet';
                        $iconBg = 'bg-gray-400';
                    }

                    $handledTime = $request->handled_at ? \Carbon\Carbon::parse($request->handled_at)->format('M d, Y | h:i A') : null;
                @endphp

                <div class="flex items-start gap-4 p-3 rounded-lg transition">
                <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">{{ $icon }}</span>
                    <div>
                        <p class="header-text font-semibold dark:text-gray-300">Personnel Action</p>
                        <p class="detail-text dark:text-gray-300">
                            @if($handledTime)
                                {{ $actionText }} on {{ $handledTime }}
                            @else
                                {{ $actionText }}
                            @endif
                        </p>
                    </div>
                </div>

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
            <p class="detail-text text-gray-500 dark:text-gray-300">No deployment information available yet.</p>
        @endif
    </div>

</div>

</x-app-layout>

<style>
[x-cloak]{
    display:none !important;
}

.material-symbols-outlined{
    font-size:22px;
}

.request-details{
    margin-left:4.5rem;
    margin-right:5rem;
    margin-top: 1.5rem;
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
</style>
