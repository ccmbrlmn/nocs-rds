<x-app-layout>
    <div class="header-container flex items-center gap-5 text-white font-medium p-2 mt-8 mb-3">
        <div class="header">
            <h1 class="flex items-center gap-2 text-3xl">
                <span class="material-symbols-outlined text-2xl">description</span> 
                Request Application
            </h1>
        </div>
    </div>

    <div class="request-details p-6 rounded-2xl bg-white shadow-sm">

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
    $colors = config('status')[$status] ?? ['text' => 'text-gray-700', 'bg' => 'bg-gray-200'];
@endphp

<div class="request-header flex justify-between items-center border-b pb-5 mb-6">
    <div class="flex flex-col">
        <h2 class="text-3xl font-semibold mb-2">{{ $request->user->name }}</h2>
        <span class="inline-flex items-center gap-2 px-4 py-1 rounded-full text-sm font-semibold shadow-sm {{ $colors['text'] }} {{ $colors['bg'] }}">
            <span class="material-symbols-outlined text-sm">info</span>
            {{ $status }}
        </span>
    </div>

    <div class="flex gap-3 items-center">
        @auth
            @if(strtolower($request->status) === 'open' && auth()->id() === $request->requested_by)
                
                <div x-data="{ openEdit: false }" class="relative">
    <x-primary-button
        @click="openEdit = true"
        class="shadow-md"
        style="background-color: #3B82F6; color: white; width: 110px; height: 42px;">
        Edit
    </x-primary-button>

<template x-teleport="body">
    <div
    x-show="openEdit"
    x-transition.opacity
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50"
    style="display: none;"
    @keydown.escape.window="openEdit = false"
    @close-edit.window="openEdit = false"
>

        <div
            @click.outside="openEdit = false"
            class="w-full max-w-5xl mx-4"
        >
            @include('form.edit-request-form', ['request' => $request])
        </div>
    </div>
</template>

</div>

                
            @endif
        @endauth
    </div>
</div>

        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-8">
                <div class="space-y-6">
                    <div class="flex items-start gap-4 hover:bg-white p-3 rounded-lg transition">
                        <span class="material-symbols-outlined bg-red-100 text-red-600 p-2 rounded-lg">location_on</span>
                        <div>
                            <p class="header-text font-semibold">Location</p>
                            <p class="detail-text">{{ $request->location }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 hover:bg-white p-3 rounded-lg transition">
                        <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">event</span>
                        <div>
                            <p class="header-text font-semibold">Name of Event</p>
                            <p class="detail-text">{{ $request->event_name }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 hover:bg-white p-3 rounded-lg transition">
                        <span class="material-symbols-outlined bg-indigo-100 text-indigo-600 p-2 rounded-lg">event_available</span>
                        <div>
                            <p class="header-text font-semibold">Purpose of the Event</p>
                            <p class="detail-text">{{ $request->purpose }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 hover:bg-white p-3 rounded-lg transition">
                        <span class="material-symbols-outlined bg-orange-100 text-orange-600 p-2 rounded-lg">inventory_2</span>
                        <div>
                            <p class="header-text font-semibold">Requested Items</p>

                            @php
                                $items = is_array($request->items) ? $request->items : json_decode($request->items, true) ?? [];
                            @endphp

                            <ul class="detail-text list-disc list-inside">
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
                    <div class="flex items-start gap-4 hover:bg-white p-3 rounded-lg transition">
                        <span class="material-symbols-outlined bg-purple-100 text-purple-600 p-2 rounded-lg">calendar_clock</span>
                        <div>
                            <p class="header-text font-semibold">Date of Event</p>
                            <p class="detail-text">
                                {{ \Carbon\Carbon::parse($request->start_date)->format('M d') }} -
                                {{ \Carbon\Carbon::parse($request->end_date)->format('d, Y') }}
                            </p>
                        </div>
                    </div>

<div class="flex items-start gap-4 hover:bg-white p-3 rounded-lg transition">
    <span class="material-symbols-outlined bg-teal-100 text-teal-600 p-2 rounded-lg">calendar_clock</span>
    <div>
        <p class="header-text font-semibold">Request Set-up Date</p>
        <p class="detail-text">
            @php
                $dateOnly = \Carbon\Carbon::parse($request->setup_date)->format('Y-m-d');
                $setupDateTime = $request->setup_time ? "$dateOnly $request->setup_time" : $dateOnly;
            @endphp

            {{ \Carbon\Carbon::parse($setupDateTime)->format($request->setup_time ? 'M d, Y | h:i A' : 'M d, Y') }}
        </p>
    </div>
</div>



                    <div class="flex items-start gap-4 hover:bg-white p-3 rounded-lg transition">
    <span class="material-symbols-outlined bg-emerald-100 text-emerald-600 p-2 rounded-lg">group</span>
    <div>
        <p class="header-text font-semibold">No. of Users</p>
        <p class="detail-text">
            {{ $request->users }} {{ $request->users == 1 ? 'user' : 'users' }}
        </p>
    </div>
</div>


                    <div class="flex items-start gap-4 hover:bg-white p-3 rounded-lg transition">
                        <span class="material-symbols-outlined bg-yellow-100 text-yellow-600 p-2 rounded-lg">email</span>
                        <div>
                            <p class="header-text font-semibold">Requester Contact Information</p>
                            <p class="detail-text">{{ $request->user->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
            @if(in_array($request->status, ['Active', 'Closed', 'Declined']))
                <div class="space-y-6">

                    @if($request->handledByAdmin)
                        <div class="flex items-start gap-4 hover:bg-white p-3 rounded-lg transition">
                            <span class="material-symbols-outlined bg-blue-600 text-white p-2 rounded-lg">manage_accounts</span>
                            <div>
                                <p class="header-text font-semibold">Name of Personnel</p>
                                <p class="detail-text">{{ $request->handledByAdmin->name }}</p>
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

    // Format the handled time if available
    $handledTime = $request->handled_at ? \Carbon\Carbon::parse($request->handled_at)->format('M d, Y | h:i A') : null;
@endphp

<div class="flex items-start gap-4 hover:bg-white p-3 rounded-lg transition">
    <span class="material-symbols-outlined {{ $iconBg }} text-white p-2 rounded-lg">{{ $icon }}</span>
    <div>
        <p class="header-text font-semibold">Personnel Action</p>
<p class="detail-text">
    @if($handledTime)
        {{ $actionText }} on {{ $handledTime }}
    @else
        {{ $actionText }}
    @endif
</p>


    </div>
</div>


                    @if($request->status === 'Declined' && $request->decline_reason)
                        <div class="flex items-start gap-4 hover:bg-white p-3 rounded-lg transition">
                            <span class="material-symbols-outlined bg-red-600 text-white p-2 rounded-lg">info</span>
                            <div>
                                <p class="header-text font-semibold">Decline Reason</p>
                                <p class="detail-text">{{ $request->decline_reason }}</p>
                            </div>
                        </div>
                    @endif

                </div>
            @else
                <p class="detail-text text-gray-500">No deployment information available yet.</p>
            @endif
        </div>

    </div>
</x-app-layout>

<style>
.material-symbols-outlined {
    font-size: 24px;
    vertical-align: middle;
}

.request-details {
    margin-left: 1.5rem;
    margin-right: 1.5rem;
}

.header-container {
    margin-left: 1.5rem;
    margin-right: 1.5rem;
}

.header-text {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1F2937;
}

.detail-text {
    font-size: 1.125rem;
    color: #4B5563;
}

.request-details .flex.items-start.gap-4 {
    padding: 1rem;
    transition: background 0.2s;
    border-radius: 0.75rem;
}

.request-details .flex.items-start.gap-4:hover {
    background-color: #f9fafb;
}

h1 {
    font-size: 1.8rem;
    font-weight: 700;
}

.request-header h2 {
    font-size: 1.75rem;
    font-weight: 700;
}

.request-header span {
    font-size: 0.875rem;
}

ul.detail-text li {
    font-size: 1.125rem;
    line-height: 1.6;
}

.bg-gray-50.rounded-xl.p-6 {
    margin-bottom: 1.5rem;
}
</style>


