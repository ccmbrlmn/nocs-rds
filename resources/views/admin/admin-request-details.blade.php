<x-app-layout>
    <div class="header-container flex items-center gap-5 text-white font-medium p-3 mt-8 mb-4">
        <div class="header">
            <h1 class="flex items-center gap-3 text-3xl">
                <span class="material-symbols-outlined text-2xl bg-white bg-opacity-20 p-2 rounded-lg">description</span> 
                Request Application
            </h1>
        </div>
    </div>

    <div class="request-details p-6 rounded-2xl bg-white shadow-sm">

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

        <div class="request-header flex flex-wrap items-start justify-between border-b pb-5 mb-6">
            <div>
                <h2 class="text-3xl font-semibold mb-2">{{ $request->user->name }}</h2> 

                @php
                    $status = $request->status;
                    $colors = config('status')[$status] ?? ['text' => 'text-gray-800', 'bg' => 'bg-gray-200'];
                @endphp

                <span class="inline-flex items-center gap-2 px-4 py-1 rounded-full text-sm font-semibold shadow-sm {{ $colors['text'] }} {{ $colors['bg'] }}">
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

                        <div x-show="openAccept" x-cloak class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center px-4">
                            <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-lg" @click.away="openAccept = false">
                                <h2 class="text-xl font-semibold mb-4">Confirm Acceptance</h2>
                                <p class="mb-4">Are you sure you want to accept this request?</p>
                                <div class="flex justify-end gap-3">
                                    <button @click="openAccept = false" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</button>
                                    <form action="{{ route('admin.requests.accept', $request->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Yes, Accept</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-data="{ openDecline: false, reason: '' }">
                        <x-primary-button @click="openDecline = true" class="shadow-md" style="background-color: #EF4444; color: white; width: 110px; height: 42px;">
                            Decline
                        </x-primary-button>

                        <div x-show="openDecline" x-cloak class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center px-4">
                            <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-lg" @click.away="openDecline = false">
                                <h2 class="text-xl font-semibold mb-4">Decline Request</h2>
                                <p class="mb-2">Please provide a reason for declining this request:</p>
                                <textarea x-model="reason" class="w-full p-3 border rounded-lg mb-4" rows="4" placeholder="Enter reason" required></textarea>
                                <div class="flex justify-end gap-3">
                                    <button @click="openDecline = false" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</button>
                                    <form :action="'{{ route('admin.requests.decline', $request->id) }}'" method="POST">
                                        @csrf
                                        <input type="hidden" name="decline_reason" :value="reason">
                                        <button type="submit" :disabled="reason === ''" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700" :class="{ 'opacity-50 cursor-not-allowed': reason === '' }">
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
                            <p class="detail-text">{{ $request->setup_date }} | {{ $request->setup_time }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 hover:bg-white p-3 rounded-lg transition">
                        <span class="material-symbols-outlined bg-emerald-100 text-emerald-600 p-2 rounded-lg">group</span>
                        <div>
                            <p class="header-text font-semibold">No. of Users</p>
                            <p class="detail-text">{{ $request->users }} users</p>
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
                } elseif($actionStatus === 'Closed') {
                    $icon = 'task_alt';
                    $actionText = 'Completed the Request';
                    $iconBg = 'bg-green-600';
                } elseif($actionStatus === 'Active') {
                    $icon = 'check_circle';
                    $actionText = 'Accepted the Request';
                    $iconBg = 'bg-green-600';
                } else {
                    $icon = 'info';
                    $actionText = 'No action yet';
                    $iconBg = 'bg-gray-400';
                }
            @endphp

            <div class="flex items-start gap-4 hover:bg-white p-3 rounded-lg transition">
                <span class="material-symbols-outlined {{ $iconBg }} text-white p-2 rounded-lg">{{ $icon }}</span>
                <div>
                    <p class="header-text font-semibold">Personnel Action</p>
                    <p class="detail-text">{{ $actionText }}</p>
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

    </div>
</x-app-layout>

<style>
.material-symbols-outlined {
    font-size: 28px;
    vertical-align: middle;
}

.request-details {
    margin-left: 4.5rem;
    margin-right: 5rem;
}

.header-container {
    margin-left: 5rem;
    margin-right: 5rem;
}

.header-text {
    font-size: 1.5rem;
}

.detail-text {
    font-size: 1.5rem;
    color: #404040;
}

h1 { font-size: 1.7rem; }

</style>

