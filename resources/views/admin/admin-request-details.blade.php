{{-- resources/views/admin/admin-request-details.blade.php --}}
<x-app-layout>
    <div class="header-container flex items-center gap-5 text-white font-medium p-2 mt-8 mb-3">
        <div class="header">
            <h1 class="flex items-center gap-2 text-3xl">
                <span class="material-symbols-outlined text-2xl">description</span> 
                Request Application
            </h1>
        </div>
    </div>

    <div class="request-details p-5 rounded-lg bg-white">
        {{-- Notifications --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="mb-4 p-3 bg-green-100 text-green-700 rounded flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="text-green-700 font-bold ml-4 hover:text-green-900">✕</button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="mb-4 p-3 bg-red-100 text-red-700 rounded flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button @click="show = false" class="text-red-700 font-bold ml-4 hover:text-red-900">✕</button>
            </div>
        @endif

        {{-- Request Header --}}
        <div class="request-header flex items-center justify-between w-full mb-3">
            <div class="mt-4">
                <h2 class="text-3xl font-semibold mb-2">{{ $request->user->name }}</h2> 

        @php
            $status = $request->status;
            $colors = config('status')[$status] ?? ['text' => 'text-gray-800', 'bg' => 'bg-gray-200'];
        @endphp


                <span class="px-4 py-1 rounded-full text-sm font-semibold
                    {{ $colors['text'] }} {{ $colors['bg'] }}">
                    {{ $status }}
                </span>
            </div>

            {{-- Admin Action Buttons --}}
            <div class="flex gap-4 items-center">
                @auth
                    @if(auth()->user()->role === 'admin' && $request->status === 'Open')
{{-- Accept Modal --}}
<div x-data="{ openAccept: false }" class="flex gap-2">
    <x-primary-button @click="openAccept = true" style="background-color: #22C55E; color: white; width: 100px; height: 40px;">
        Accept
    </x-primary-button>

    <div x-show="openAccept" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center px-4">
        <div class="bg-white rounded-lg w-full max-w-md p-6" @click.away="openAccept = false">
            <h2 class="text-xl font-semibold mb-4">Confirm Acceptance</h2>
            <p class="mb-4">Are you sure you want to accept this request?</p>
            <div class="flex justify-end gap-3">
                <button @click="openAccept = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <form action="{{ route('admin.requests.accept', $request->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Yes, Accept</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Decline Modal --}}
<div x-data="{ openDecline: false, reason: '' }" class="flex gap-2">
    <x-primary-button @click="openDecline = true" style="background-color: #EF4444; color: white; width: 100px; height: 40px;">
        Decline
    </x-primary-button>

        <div x-show="openDecline" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center px-4">
            <div class="bg-white rounded-lg w-full max-w-md p-6" @click.away="openDecline = false">
                <h2 class="text-xl font-semibold mb-4">Decline Request</h2>
                <p class="mb-2">Please provide a reason for declining this request:</p>
                <textarea x-model="reason" class="w-full p-2 border rounded mb-4" rows="4" placeholder="Enter reason" required></textarea>
                <div class="flex justify-end gap-3">
                    <button @click="openDecline = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <form :action="'{{ route('admin.requests.decline', $request->id) }}'" method="POST">
                        @csrf
                        <input type="hidden" name="decline_reason" :value="reason">
                        <button type="submit" :disabled="reason === ''" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700" :class="{ 'opacity-50 cursor-not-allowed': reason === '' }">
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

        <hr class="mb-4">

        {{-- Request Information --}}
        <div class="request-information flex justify-between pt-3">
            <div class="left-col-info">
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-gray-600">event</span>
                    <div>
                        <p class="header-text font-semibold mb-1">Name of Event:</p>
                        <p class="detail-text">{{ $request->event_name }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-gray-600">event_available</span>
                    <div>
                        <p class="header-text font-semibold mb-1">Purpose of the Event:</p>
                        <p class="detail-text">{{ $request->purpose }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 mb-6">
                    <span class="material-symbols-outlined text-gray-600">inventory_2</span>
                    <div>
                        <p class="header-text font-semibold mb-1">Requested Items:</p>


@php
    // Ensure $items is always an array, whether it's stored as JSON or already as array
    $items = is_array($request->items) ? $request->items : json_decode($request->items, true) ?? [];
@endphp

<ul class="detail-text list-disc list-inside">
    @if(count($items) > 0)
        @foreach($items as $item)
            <li>{{ $item['name'] }} — Quantity: {{ $item['quantity'] }}</li>
        @endforeach
    @else
        <li>No items requested</li>
    @endif
</ul>


                    </div>
                </div>
            </div>

            <div class="right-col-info">
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-gray-600">location_on</span>
                    <div>
                        <p class="header-text font-semibold mb-1">Location:</p>
                        <p class="detail-text">{{ $request->location }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-gray-600">calendar_clock</span>
                    <div>
                        <p class="header-text font-semibold mb-1">Date of Event:</p>
                        <p class="detail-text">{{ \Carbon\Carbon::parse($request->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($request->created_at)->format('d, Y') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-gray-600">calendar_clock</span>
                    <div>
                        <p class="header-text font-semibold mb-1">Request Set-up Date:</p>
                        <p class="detail-text">{{ $request->setup_date }} | {{ $request->setup_time }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-gray-600">group</span>
                    <div>
                        <p class="header-text font-semibold mb-1">No. of Users:</p>
                        <p class="detail-text">{{ $request->users }} users</p>
                    </div>
                </div>
            </div>
        </div>

        <hr class="mb-4 mt-4">

{{-- Deployment Info --}}
<p class="header-text font-semibold mb-1">Deployment Information</p>
<div class="request-information flex justify-between pt-3">
    <div class="left-col-info w-full">
        @if($request->status === 'Active')
            <div class="flex items-center gap-3 mb-6">
                <span class="material-symbols-outlined text-gray-600">manage_accounts</span>
                <div>
                    <p class="header-text font-semibold mb-1">Name of Personnel:</p>
                    <p class="detail-text">{{ $request->handledByAdmin ? $request->handledByAdmin->name : '-' }}</p>
                </div>
            </div>
        @endif

        @if($request->status === 'Declined' && $request->decline_reason)
            <div class="flex items-center gap-3 mb-6">
                <span class="material-symbols-outlined text-gray-600">block</span>
                <div>
                    <p class="header-text font-semibold mb-1">Decline Reason:</p>
                    <p class="detail-text">{{ $request->decline_reason }}</p>
                </div>
            </div>
        @endif
    </div>

    @if($request->status === 'Active')
        <div class="right-col-info">
            <div class="flex items-center gap-3 mb-6">
                <span class="material-symbols-outlined text-gray-600">home_repair_service</span>
                <div>
                    <p class="header-text font-semibold mb-1">Additional Equipments:</p>
                    <p class="detail-text">{{ $request->other_equipments }}</p>
                </div>
            </div>
        </div>
    @endif
</div>


    </div>
</x-app-layout>

{{-- Styles (reuse from regular view) --}}
<style>
    .material-symbols-outlined { font-size: 28px; vertical-align: middle; }
    .request-details { margin-left: 4.5rem; margin-right: 5rem; padding: 2rem; max-height: calc(100vh - 160px); overflow-y: auto; }
    h1 { font-size: 1.7rem; }
    .header-container { margin-left: 5rem; margin-right: 5rem; }
    .request-information { margin-top: 10px; display: flex; flex-wrap: wrap; gap: 2%; }
    .left-col-info, .right-col-info { text-align: left; width: 48%; min-width: 250px; }
    .header-text { font-size: 1.5rem; }
    .detail-text { font-size: 1.5rem; color:#404040; }
    [x-cloak] { display: none !important; }
</style>
