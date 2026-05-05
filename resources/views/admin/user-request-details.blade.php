<x-app-layout>

{{-- HEADER --}}
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

    <div></div>
</div>

{{-- MAIN WRAPPER --}}
<div class="request-details p-6 rounded-2xl bg-white dark:bg-gray-800 shadow-sm">

    {{-- ALERTS --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition
            class="mb-5 p-4 bg-green-100 text-green-700 rounded-xl flex justify-between shadow-sm">
            <span>{{ session('success') }}</span>
            <button @click="show=false" class="font-bold">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition
            class="mb-5 p-4 bg-red-100 text-red-700 rounded-xl flex justify-between shadow-sm">
            <span>{{ session('error') }}</span>
            <button @click="show=false" class="font-bold">✕</button>
        </div>
    @endif

    {{-- TIMELINE --}}
    <div class="mb-8">
        <x-request-timeline :request="$request" />
    </div>

    {{-- HEADER --}}
    <div class="flex flex-wrap items-center justify-between border-b border-gray-200 dark:border-gray-500 pb-5 mb-6">

        <div class="flex flex-wrap items-center gap-3">

            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                {{ $request->event_name ?? 'Unknown Event' }}
            </h2>

            @php
                $statusClasses = [
                    'Open' => 'bg-amber-100 text-amber-700 dark:bg-amber-700 dark:text-amber-200',
                    'Active' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-700 dark:text-indigo-200',
                    'Pending Return' => 'bg-sky-100 text-sky-700 dark:bg-sky-700 dark:text-sky-200',
                    'Pending Retrieval' => 'bg-purple-100 text-purple-700 dark:bg-purple-700 dark:text-purple-200',
                    'Closed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-200',
                    'Cancelled' => 'bg-rose-100 text-rose-700 dark:bg-rose-700 dark:text-rose-200',
                ];

                $statusColor = $statusClasses[$request->status] ?? 'bg-gray-100 text-gray-700';
            @endphp

            <span class="px-4 py-1 rounded-xl text-sm font-semibold shadow-sm {{ $statusColor }}">
                {{ $request->status }}
            </span>

            @if($request->is_edited)
                <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">edit</span>
                    Edited {{ $request->updated_at->diffForHumans() }}
                </span>
            @endif

        </div>
    </div>

    {{-- DETAILS --}}
    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-500 mb-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-8">

            {{-- LEFT --}}
            <div class="space-y-6">

                <x-request-field icon="location_on" label="Location" :value="$request->location" />

                <x-request-field icon="event" label="Requester" :value="optional($request->user)->name ?? 'Unknown User'" />
                
                <x-request-field icon="event" label="Requester" :value="optional($request->user)->name ?? 'Unknown User'" />

<x-request-field 
    icon="badge" 
    label="Requested Employee" 
    :value="$request->requested_employee ?? 'N/A'" 
/>

<x-request-field icon="event_available" label="Purpose"
    :value="$request->purpose === 'Others' ? $request->other_purpose : $request->purpose" />

<x-request-field 
    icon="notes" 
    label="Notes" 
    :value="$request->note ?? 'N/A'" 
/>

            </div>

            {{-- RIGHT --}}
            <div class="space-y-6">
            
                            <x-request-field icon="event_available" label="Purpose"
                    :value="$request->purpose === 'Others' ? $request->other_purpose : $request->purpose" />

                <div class="flex gap-4 p-3">
                    <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">inventory_2</span>
                    <div>
                        <p class="header-text">Requested Items</p>
                        @php
                            $items = is_array($request->items) ? $request->items : json_decode($request->items, true) ?? [];
                        @endphp
                        <ul class="detail-text">
                            @forelse($items as $item)
                                <li>{{ $item['quantity'] }} {{ $item['name'] }}</li>
                            @empty
                                <li>No items requested</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                

                <x-request-field icon="calendar_clock" label="Event Date"
                    :value="\Carbon\Carbon::parse($request->start_date)->format('M d') . ' - ' . \Carbon\Carbon::parse($request->end_date)->format('d, Y')" />

                <x-request-field icon="calendar_clock" label="Setup"
                    :value="$request->setup_date . ' | ' . $request->setup_time" />

                <x-request-field icon="group" label="Users" :value="number_format($request->users)" />

                <x-request-field icon="email" label="Contact" :value="optional($request->user)->email ?? '-'" />

            </div>

        </div>
    </div>

</div>

</x-app-layout>
