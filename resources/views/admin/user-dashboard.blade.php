<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Dashboard') }}
        </h2>
    </x-slot>

    <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        {{-- Greeting --}}
        <div class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            @php
                date_default_timezone_set('Asia/Manila');
                $hour = now()->hour;
                if ($hour < 12) $greeting = 'Good morning';
                elseif ($hour < 18) $greeting = 'Good afternoon';
                else $greeting = 'Good evening';
            @endphp

            @if(auth()->check())
                {{ $greeting }}, {{ auth()->user()->name }}!
            @else
                {{ $greeting }}!
            @endif
        </div>

        {{-- Search + Notification --}}
        <div class="flex items-center gap-4"
             
             x-data="{
    searchQuery: '',
    notifications: [],
    hasNotifications: false,
    openDropdown: false,


    fetchNotifications() {
        fetch('/notifications')
            .then(res => res.json())
            .then(data => {
                this.notifications = data;
                this.hasNotifications = this.notifications.some(n => !n.is_read);
            });
    },

    markNotificationsRead() {
        fetch('/notifications/read', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => {
            this.notifications.forEach(n => n.is_read = true);
            this.hasNotifications = false;
        });
    },

    openNotificationsModal() {
        this.openModal = true;
        this.markNotificationsRead();
    }
}"
x-init="fetchNotifications(); setInterval(fetchNotifications, 30000)"

        >

            <div class="relative">
                <input type="text" placeholder="Search..."
                    class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm w-64"
                    x-model="searchQuery" @input.debounce.300ms="console.log('Searching for', searchQuery)">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                    </svg>
                </span>
            </div>

            <div class="relative">
                <button @click="openNotificationsModal()"
                        class="relative p-2 rounded-full hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span x-show="hasNotifications" x-transition.opacity
                          class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
                </button>

                <div x-show="openModal" x-cloak
                     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center px-4">
                    <div @click.away="openModal = false"
                         class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 overflow-y-auto max-h-[80vh]">

                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold text-gray-800">Notifications</h2>
                            <button @click="openModal = false" class="text-gray-500 hover:text-gray-700">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>

                        <template x-if="notifications.length === 0">
                            <div class="text-center text-gray-600 py-8">No new notifications</div>
                        </template>

                        <template x-for="notif in notifications" :key="notif.id">
                            <div class="p-3 mb-3 border rounded-lg hover:bg-gray-50 transition">
                                <div class="flex justify-between items-center">
                                    <h3 class="font-semibold text-gray-900" x-text="notif.request.event_name"></h3>
                                    <span class="text-sm px-2 py-1 rounded-full font-medium"
                                          :class="notif.action === 'request_accepted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                          x-text="notif.action === 'request_accepted' ? 'Accepted' : 'Declined'"></span>
                                </div>
                                <p class="text-gray-500 text-sm mt-1"
                                   x-text="new Date(notif.request.setup_date + ' ' + (notif.request.setup_time ?? '')).toLocaleString()"></p>
                                <p class="text-gray-600 text-sm mt-1" x-text="notif.request.location"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6 grid grid-cols-1 xl:grid-cols-12 gap-6"
         x-data="{
            openRequestForm: false,
            openCancelForm: false,
            loading: false,
            loadPage(url) {
                if (!url || this.loading) return;
                this.loading = true;
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                    .then(r => r.text())
                    .then(html => {
                        document.getElementById('sched-container').innerHTML = html;
                        this.loading = false;
                    });
            }
         }">

        <div class="cal-col xl:col-span-8 bg-white rounded-3xl shadow-sm border border-gray-100 p-3 sm:p-4">
            @include('layouts.calendar', ['calendarEvents' => $calendarEvents])
        </div>

        <div class="card-sched xl:col-span-4 bg-white rounded-3xl shadow-sm border border-gray-100 px-3 sm:px-4 py-4 flex flex-col">

            <div class="flex items-start justify-between gap-4 mb-4 pt-4 px-2 sm:px-3">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Scheduled Requests</h2>
                    <p class="text-sm text-gray-500 mt-1">Your upcoming activities</p>
                </div>

                <div class="flex items-center gap-3">
                    @php $count = $scheduledRequests->count(); @endphp
                    @if($count > 0)
                        <button
                            @click="openRequestForm = true"
                            class="whitespace-nowrap inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition shadow">
                            Create Request
                        </button>
                    @endif
                </div>
            </div>

            <div class="sched-scroll flex flex-col gap-3 pr-1" id="sched-container">
                @forelse ($scheduledRequests as $sched)
                    @php
                        $isToday = \Carbon\Carbon::parse($sched->setup_date)->isToday();
                        $label = $sched->computed_status ?? 'Open';
                        $statusConfig = config('status')[$label] ?? null;
                        $color = $statusConfig
                            ? $statusConfig['bg'] . ' ' . $statusConfig['text']
                            : 'bg-gray-100 text-gray-700';
                    @endphp

                    <div class="relative p-3 pt-7 pl-4 rounded-2xl border border-gray-100 bg-white hover:bg-gray-50 transition duration-200 hover:shadow-md">
                        <div class="flex justify-between items-center gap-3">
                            <div class="flex flex-col gap-1">
                                <p class="text-sm font-semibold text-gray-800 tracking-tight">
                                    {{ \Carbon\Carbon::parse($sched->setup_date)->format('M d, Y') }}
                                    @if($sched->setup_time)
                                        • {{ \Carbon\Carbon::parse($sched->setup_time)->format('h:i A') }}
                                    @endif
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $color }}">
                                {{ $label }}
                            </span>
                        </div>
                        <div class="mt-2 space-y-1.5">
                            <h6 class="text-base font-semibold text-gray-900 leading-snug">
                                {{ $sched->event_name }}
                            </h6>
                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                {{ $sched->location }}
                            </p>
                            <p class="text-sm text-gray-600 line-clamp-2">
                                {{ $sched->purpose }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-center py-12">
                        <div class="w-20 h-20 flex items-center justify-center rounded-full bg-indigo-50 mb-4">
                            <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">No scheduled requests yet</h3>
                        <p class="text-sm text-gray-500 max-w-sm mt-1">You're all caught up. Start by creating your first request.</p>
                        <button
                            @click="openRequestForm = true"
                            class="mt-5 whitespace-nowrap inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition shadow">
                            Create Request
                        </button>
                    </div>
                @endforelse
            </div>

            @if($scheduledRequests instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="flex items-center justify-between pt-4">
                    <button
                        @click="loadPage('{{ $scheduledRequests->previousPageUrl() }}')"
                        class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition disabled:opacity-50"
                        @disabled(!$scheduledRequests->previousPageUrl())>
                        Previous
                    </button>

                    <button
                        @click="loadPage('{{ $scheduledRequests->nextPageUrl() }}')"
                        class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition disabled:opacity-50"
                        @disabled(!$scheduledRequests->nextPageUrl())>
                        Next
                    </button>
                </div>
            @endif
        </div>

        @auth
            @include('form.request-form')
        @endauth

    </div>
</x-app-layout>

<style>
.card-sched {
    height: 800px;
}

.sched-scroll {
    overflow-y: auto;
    flex: 1;
    scrollbar-width: thin;
}

.sched-scroll::-webkit-scrollbar {
    width: 6px;
}

.sched-scroll::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,.15);
    border-radius: 6px;
}

[x-cloak] {
    display: none !important;
}
</style>

