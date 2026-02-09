<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Dashboard') }}
        </h2>
    </x-slot>

    <div class="p-6 grid grid-cols-1 xl:grid-cols-2 gap-6"
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

        <div class="card-sched bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex flex-col">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Scheduled Requests</h2>
                    <p class="text-sm text-gray-500">Your upcoming activities</p>
                </div>

                <div class="flex items-center gap-3">
                    @php $count = $scheduledRequests->count(); @endphp

                    @if($count > 0)
                        <button
                            @click="openRequestForm = true"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700
                                   text-white text-xs font-semibold rounded-lg shadow transition">
                            + Create Request
                        </button>
                    @endif
                </div>
            </div>

            <div class="sched-scroll flex flex-col gap-4 pr-1" id="sched-container">

                @forelse ($scheduledRequests as $sched)

                    @php
                        $isToday = \Carbon\Carbon::parse($sched->setup_date)->isToday();
                        $label = $sched->computed_status ?? 'Open';
                        $statusConfig = config('status')[$label] ?? null;
                        $color = $statusConfig
                            ? $statusConfig['bg'] . ' ' . $statusConfig['text']
                            : 'bg-gray-100 text-gray-700';
                    @endphp

                    <div class="relative p-4 pl-10 rounded-2xl border border-gray-100 bg-white hover:bg-gray-50 transition duration-200 hover:shadow-md">

                        <span class="absolute left-4 top-6 w-2.5 h-2.5 rounded-full
                            {{ $isToday ? 'bg-blue-600 ring-4 ring-blue-100' : 'bg-indigo-500 ring-4 ring-indigo-100' }}">
                        </span>

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

                        <div class="mt-3 space-y-1">
                            <h6 class="text-base font-semibold text-gray-900 leading-snug">
                                {{ $sched->event_name }}
                            </h6>

                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                <span>📍</span> {{ $sched->location }}
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

                        <h3 class="text-lg font-semibold text-gray-800">
                            No scheduled requests yet
                        </h3>

                        <p class="text-sm text-gray-500 max-w-sm mt-1">
                            You're all caught up. Start by creating your first request.
                        </p>

                        <button
                            @click="openRequestForm = true"
                            class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow transition">
                            + Create Request
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

        <div class="cal-col bg-white rounded-3xl shadow-sm border border-gray-100 p-4">
            @include('layouts.calendar', ['calendarEvents' => $calendarEvents])
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

