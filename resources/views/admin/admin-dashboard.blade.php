<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>
    
    <div class="p-6 flex gap-6" x-data="{ openRequestForm: false, openCancelForm: false }">

        {{-- Scheduled Requests Card --}}
        <div class="card-sched bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">Scheduled Requests</h2>
                    <p class="text-sm text-gray-500">Your upcoming activities</p>
                </div>

                @php $count = $scheduledRequests->count(); @endphp
                <div class="text-xs text-gray-400 font-medium">
                    {{ $count === 0
                        ? 'No item'
                        : $count . ' ' . \Illuminate\Support\Str::plural('item', $count)
                    }}
                </div>
            </div>
            
            @php
                $displayLimit = 3;
                $displayRequests = $scheduledRequests->take($displayLimit); // take only first 5
                @endphp


            <div class="sched-scroll flex flex-col gap-4 pr-1">
                @forelse ($displayRequests as $sched)
                    @php
                        $isToday = \Carbon\Carbon::parse($sched->setup_date)->isToday();
                        $label = $sched->computed_status ?? 'Open';
                        $statusConfig = config('status')[$label] ?? null;
                        $color = $statusConfig
                            ? $statusConfig['bg'] . ' ' . $statusConfig['text']
                            : 'bg-gray-100 text-gray-700';
                    @endphp

                    <div class="relative p-4 pl-10 rounded-xl border border-gray-100 bg-gradient-to-br from-white to-gray-50 hover:shadow-md transition">
                        {{-- Timeline Dot --}}
                        <span class="absolute left-4 top-6 w-2.5 h-2.5 rounded-full
                            {{ $isToday ? 'bg-blue-600 ring-2 ring-blue-200' : 'bg-indigo-400' }}">
                        </span>

                        <div class="flex justify-between items-center gap-3">
                            <div class="flex flex-col gap-1">
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ \Carbon\Carbon::parse($sched->setup_date)->format('M d, Y') }}
                                    @if($sched->setup_time)
                                        • {{ \Carbon\Carbon::parse($sched->setup_time)->format('h:i A') }}
                                    @endif
                                </p>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full
                                        {{ $isToday ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700' }}">
                                        {{ $isToday ? 'Today' : 'Upcoming' }}
                                    </span>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $color }}">
                                {{ $label }}
                            </span>
                        </div>

                        <div class="mt-3">
                            <h6 class="text-base font-semibold text-gray-900 leading-snug">
                                {{ $sched->event_name }}
                            </h6>
                            <p class="text-sm text-gray-500">
                                📍 {{ $sched->location }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                {{ $sched->purpose }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <p class="text-gray-400 text-sm">No scheduled requests.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="cal-col">
            <div class="calendar">
                @include('layouts.calendar', ['calendarEvents' => $calendarEvents])
            </div>
        </div>

        @auth
        <div x-show="openRequestForm">
            @include('form.request-form')
        </div>
        @endauth

    </div>
</x-app-layout>

<style>
.card-sched{
    width: 35%;
    height: 800px;
}

.cal-col{
    width: 65%;
}

.sched-scroll{
    overflow-y: auto;
    flex: 1;
    scrollbar-width: thin;
}

.sched-scroll::-webkit-scrollbar{
    width: 6px;
}

.sched-scroll::-webkit-scrollbar-thumb{
    background: rgba(0,0,0,.15);
    border-radius: 6px;
}

[x-cloak] { display: none !important; }
</style>

