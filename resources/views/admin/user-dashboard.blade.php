<x-app-layout>

    <!-- ✅ Dark Mode Script (ONLY ONCE) -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (
                localStorage.theme === 'dark' ||
                (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
            ) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
    </script>

    <!-- ✅ HEADER (FIXED & SAME AS USER-REQUESTS) -->
    <div class="header-container rounded-2xl mb-3 mx-3 mt-3">
        <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <!-- Greeting -->
            <div class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
                @php
                    date_default_timezone_set('Asia/Manila');
                    $hour = now()->hour;
                    if ($hour < 12) $greeting = 'Good morning';
                    elseif ($hour < 18) $greeting = 'Good afternoon';
                    else $greeting = 'Good evening';
                @endphp

{{ $greeting }}, {{ auth()->check() ? auth()->user()->name : 'Guest' }}!
            </div>

            <!-- ✅ Shared Header (Notifications FIXED HERE) -->
            @include('layouts.header')

        </div>
    </div>

    <!-- ✅ MAIN CONTENT -->
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

        <!-- CALENDAR -->
        <div class="cal-col xl:col-span-8 bg-white rounded-3xl dark:border-gray-200 p-3 sm:p-4 dark:bg-gray-800 text-gray-900 dark:text-gray-200">
            @include('layouts.calendar', ['calendarEvents' => $calendarEvents])
        </div>

        <!-- SCHEDULED REQUESTS -->
        <div class="card-sched xl:col-span-4 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 px-3 sm:px-4 py-4 flex flex-col text-gray-900 dark:text-gray-200">

            <div class="flex items-start justify-between gap-4 mb-4 pt-4 px-2 sm:px-3">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-200 tracking-tight">
                        Scheduled Requests
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">
                        Your upcoming activities
                    </p>
                </div>
            </div>

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

            <div class="sched-scroll flex flex-col gap-3 pr-1" id="sched-container">
                @forelse ($todayRequests as $sched)

                    @php
                        $label = $sched->computed_status ?? 'Open';
                        $statusConfig = config('status')[$label] ?? null;
                        $color = $statusConfig
                            ? $statusConfig['bg'] . ' ' . $statusConfig['text']
                            : 'bg-gray-100 text-gray-700 dark:text-gray-200';
                    @endphp

                    <a href="{{ route('request-details.show', $sched->id) }}"
                       class="block relative p-3 pt-7 pl-4 rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200 hover:shadow-md cursor-pointer">

                        <div class="flex justify-between items-center gap-3">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
                                {{ \Carbon\Carbon::parse($sched->setup_date)->format('M d, Y') }}
                                @if($sched->setup_time)
                                    • {{ \Carbon\Carbon::parse($sched->setup_time)->format('h:i A') }}
                                @endif
                            </p>

                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $color }}">
                                {{ $label }}
                            </span>
                        </div>

                        <div class="mt-2 space-y-1.5">
                            <h6 class="text-base font-semibold text-gray-800 dark:text-gray-200">
                                {{ $sched->event_name }}
                            </h6>

                            <p class="text-sm text-gray-800 dark:text-gray-200">
                                {{ $sched->location }}
                            </p>

                            <p class="text-sm text-gray-800 dark:text-gray-200 line-clamp-2">
                                {{ $sched->purpose }}
                            </p>
                        </div>

                    </a>

                @empty
                    <div class="flex flex-col items-center justify-center h-full text-center py-12">
                    
                            <div class="w-20 h-20 flex items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-900 mb-4">
            <svg class="w-10 h-10 text-indigo-400 dark:text-indigo-300" fill="none" stroke="currentColor" stroke-width="1.5"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z"/>
            </svg>
        </div>
        
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            No scheduled requests yet
                        </h3>
                        <p class="text-sm text-gray-800 dark:text-gray-200 mt-1">
                            You're all caught up.
                        </p>

                        <a href="{{ route('user.requests') }}"
                           class="mt-5 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition shadow">
                            View Requests
                        </a>
                    </div>
                    
                    
                @endforelse
            </div>

            @if($scheduledRequests instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="flex justify-between pt-4">
                    <button
                        @click="loadPage('{{ $scheduledRequests->previousPageUrl() }}')"
                        class="px-4 py-2 text-sm border rounded-lg">
                        Previous
                    </button>

                    <button
                        @click="loadPage('{{ $scheduledRequests->nextPageUrl() }}')"
                        class="px-4 py-2 text-sm border rounded-lg">
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
.card-sched { height: 800px; }
.sched-scroll { overflow-y: auto; flex: 1; }
[x-cloak] { display: none !important; }
</style>
