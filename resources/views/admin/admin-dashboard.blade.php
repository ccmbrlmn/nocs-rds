<x-app-layout>
<div
x-data="{
    view: 'calendar',
    pinnedView: localStorage.getItem('pinnedView'),

    openRequestForm: false,
    openCancelForm: false,
    openAssetForm: false,
    loading: false,
    showEventModal: false,
    selectedEvent: null,

    init() {
        const urlView = new URLSearchParams(window.location.search).get('view');

        if (urlView) {
            this.view = urlView;
        }
        else if (this.pinnedView) {
            this.view = this.pinnedView;
            history.replaceState(null, '', `?view=${this.pinnedView}`);
        }
        else {
            this.view = 'calendar';
        }
    },

    setView(v) {
        this.view = v;
        history.replaceState(null, '', `?view=${v}`);
    },

    togglePin(v) {
        if (this.pinnedView === v) {
            localStorage.removeItem('pinnedView');
            this.pinnedView = null;
        } else {
            localStorage.setItem('pinnedView', v);
            this.pinnedView = v;
        }
    },

    isPinned(v) {
        return this.pinnedView === v;
    },

    loadPage(url) {
        if (!url || this.loading) return;
        this.loading = true;
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
            .then(r => r.text())
            .then(html => {
                document.getElementById('sched-container').innerHTML = html;
                this.loading = false;
            });
    },

    openEvent(event) {
        this.selectedEvent = event;
        this.showEventModal = true;
    },

    closeEvent() {
        this.selectedEvent = null;
        this.showEventModal = false;
    }
}"
x-init="init()"
>


    <style>
        [x-cloak] { display: none !important; }
    </style>

<div class="rounded-2xl mb-3 mx-3 mt-3">

    <!-- TOP NAV -->
    <div class="p-4 flex items-center justify-between">
        <div class="flex gap-3">

            <button @click="setView('calendar')"
                class="px-4 py-2 text-sm font-semibold rounded-xl transition"
                :class="view === 'calendar'
                    ? 'bg-white dark:bg-gray-900 text-indigo-600 shadow'
                    : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'">
                Calendar
            </button>

            <button @click="setView('asset-dashboard')"
                class="px-4 py-2 text-sm font-semibold rounded-xl transition"
                :class="view === 'asset-dashboard'
                    ? 'bg-white dark:bg-gray-900 text-indigo-600 shadow'
                    : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'">
                Assets
            </button>

            <button @click="setView('request-dashboard')"
                class="px-4 py-2 text-sm font-semibold rounded-xl transition"
                :class="view === 'request-dashboard'
                    ? 'bg-white dark:bg-gray-900 text-indigo-600 shadow'
                    : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'">
                Requests
            </button>

        </div>

        @include('layouts.header')
    </div>



</div>

    <div id="calendar-container"
         x-data="{
            openRequestForm: false,
            openCancelForm: false,
            openAssetForm: false,
            loading: false,
            showEventModal: false,
            selectedEvent: null,
            loadPage(url) {
                if (!url || this.loading) return;
                this.loading = true;
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                    .then(r => r.text())
                    .then(html => {
                        document.getElementById('sched-container').innerHTML = html;
                        this.loading = false;
                    });
            },
            openEvent(event) {
                this.selectedEvent = event;
                this.showEventModal = true;
            },
            closeEvent() {
                this.selectedEvent = null;
                this.showEventModal = false;
            }
         }">



<div x-show="openAssetForm"
     x-cloak
     class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center px-4">

    <div class="bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm dark:text-gray-200 rounded-2xl w-full max-w-3xl p-6 shadow-xl">

        <div class="flex justify-end">
            <button @click="openAssetForm = false"
                    class="text-gray-500 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white">
                ✕
            </button>
        </div>

        <h1 class="text-2xl font-semibold text-center mb-6">
            Add Asset
        </h1>

        <form action="{{ route('assets.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <x-input-label value="Asset Name" />
                    <x-text-input name="asset_name" class="w-full" required />
                </div>

                <div>
                    <x-input-label value="Asset Tag" />
                    <x-text-input name="asset_tag" class="w-full" />
                </div>

                <div>
                    <x-input-label value="Serial" />
                    <x-text-input name="asset_serial" class="w-full" />
                </div>

                <div>
                    <x-input-label value="Model" />
                    <x-text-input name="asset_model" class="w-full" />
                </div>

                <div>
                    <x-input-label value="Category" />
                    <x-text-input name="asset_category" class="w-full" />
                </div>

                <div>
                    <x-input-label value="Status" />
                    <select name="asset_status"
                        class="w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-200">
                        <option value="Available">Available</option>
                        <option value="In Use">In Use</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>

            </div>

            <div class="mt-6">
                <button class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                    Save Asset
                </button>
            </div>
        </form>

    </div>
</div>

        <div x-show="view === 'calendar'" x-cloak
             class="p-6 grid grid-cols-1 xl:grid-cols-12 gap-6">

            <div class="cal-col xl:col-span-8  bg-white/40 dark:bg-gray-800/40 backdrop-blur-md rounded-3xl dark:border-gray-200 p-3 sm:p-4 dark:bg-gray-800 text-gray-900 dark:text-gray-200">
                @include('layouts.calendar', ['calendarEvents' => $calendarEvents])
            </div>

            <div class="card-sched xl:col-span-4
                bg-white/40 dark:bg-gray-800/40
                backdrop-blur-md
                rounded-3xl
                shadow-sm
                border border-gray-200/60 dark:border-gray-600/40
                px-3 sm:px-4 py-4
                flex flex-col
                text-gray-900 dark:text-gray-200">

                @php
                    $todayRequests = $scheduledRequests
                        ->filter(function($sched) {
                            $isToday = \Carbon\Carbon::parse($sched->setup_date)->isToday();
                            $status = $sched->computed_status ?? 'Open';
                            return $isToday && in_array($status, ['Active', 'Closed']);
                        })
                        ->sortBy(function($sched) {
                            return $sched->computed_status === 'Active' ? 0 : 1;
                        })
                        ->values();
                @endphp

                <div class="flex items-start justify-between gap-4 mb-4 pt-4 px-2 sm:px-3">

                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-200 tracking-tight">
                            Scheduled Requests
                        </h2>

                    </div>

                    @if($todayRequests->isNotEmpty())
                        <a href="{{ route('admin.requests') }}"
                           class="px-4 py-2 rounded-xl text-sm font-medium transition
                        bg-indigo-600 text-white
                        hover:bg-indigo-700
                        dark:bg-indigo-500 dark:hover:bg-indigo-600">
                            View Requests
                        </a>
                    @endif
                </div>

                <div class="sched-scroll flex flex-col gap-3 pr-1" id="sched-container">
                    @forelse ($todayRequests as $sched)
                        @php
                            $label = $sched->computed_status ?? 'Open';
                            $statusConfig = config('status')[$label] ?? null;
                            $color = $statusConfig
                                ? $statusConfig['bg'] . ' ' . $statusConfig['text']
                                : 'bg-gray-100 text-gray-700 dark:text-gray-200';
                        @endphp

                        <a href="{{ url('/admin/requests/' . $sched->id) }}"
                           class="block relative p-3 pt-7 pl-4 rounded-2xl border border-gray-100 dark:border-gray-700 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200 hover:shadow-md cursor-pointer">

                            <div class="flex justify-between items-center gap-3">
                                <div class="flex flex-col gap-1">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
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
                                <h6 class="text-base font-semibold text-gray-900 dark:text-gray-200 leading-snug">
                                    {{ $sched->event_name }}
                                </h6>

                                <p class="text-sm text-gray-500 dark:text-gray-200 flex items-center gap-1">
                                    {{ $sched->location }}
                                </p>

                                <p class="text-sm text-gray-600 dark:text-gray-200 line-clamp-2">
                                    {{ $sched->purpose }}
                                </p>
                            </div>

                        </a>

                    @empty
                        <!-- RESTORED NICE EMPTY STATE -->
                        <div class="flex flex-col items-center justify-center h-full text-center py-12">
                            <div class="w-20 h-20 flex items-center justify-center rounded-full bg-indigo-50 mb-4">
                                <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.5"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                No scheduled requests yet
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-300 max-w-sm mt-1">
                                You're all caught up. View other requests.
                            </p>
                            <a href="{{ route('admin.requests') }}"
                               class="mt-5 whitespace-nowrap inline-flex items-center gap-2 bg-indigo-600 font-semibold text-white px-4 py-2 rounded-xl text-sm font-medium transition
        bg-indigo-600 text-white
        hover:bg-indigo-700
        dark:bg-indigo-500 dark:hover:bg-indigo-600">

                                View Requests
                            </a>
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

        </div>

        <div x-show="view === 'asset-dashboard'" x-cloak>
            @include('admin.asset-dashboard', [
                'statusData' => $statusData,
                'categoryData' => $categoryData,
                'categoryStatusData' => $categoryStatusData
            ])
        </div>

        <div x-show="view === 'request-dashboard'" x-cloak>
            @include('admin.admin-request-dashboard', [
                'requests' => $requests
            ])
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
</style>

