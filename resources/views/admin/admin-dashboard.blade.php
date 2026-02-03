{{-- resources/views/admin/admin-dashboard.blade.php --}}
<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>
    
    <div class="p-6 flex gap-6" x-data="{ openRequestForm: false, openCancelForm: false }">
    
        <!-- Card Schedule -->
        <div class="card-sched mt-5">
            <h2 class="font-manrope text-3xl leading-tight text-gray-900 mb-1.5 mt-5">Scheduled Requests</h2>
            <p class="text-lg font-normal text-gray-600 mb-8">Don’t miss schedule</p>

            <div class="flex gap-5 flex-col">
                @forelse ($scheduledRequests as $sched)
                    <div class="p-6 rounded-xl bg-white">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                <p class="text-base font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($sched->setup_date)->format('M d, Y') }}
                                    @if($sched->setup_time)
                                        - {{ \Carbon\Carbon::parse($sched->setup_time)->format('h:i A') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <h6 class="text-xl leading-8 font-semibold text-black mb-1">{{ $sched->location }} - {{ $sched->event_name }}</h6>
                        <p class="text-base font-normal text-gray-600">{{ $sched->purpose }}</p>
                    </div>
                @empty
                    <p class="text-gray-500">No scheduled requests in progress today.</p>
                @endforelse
            </div>
        </div>

        <!-- Calendar -->
        <div class="cal-col">
            <div class="calendar">
                @include('layouts.calendar', ['calendarEvents' => $calendarEvents])
            </div>
        </div>

        @auth
        <div x-data="{ openRequestForm: false }">
            @include('form.request-form')
        </div>
        @endauth

    </div>
</x-app-layout>

<style>
    .card-sched{
        width: 30%;
        margin-top: 1.5rem;
    }
    .cal-col{
        width: 70%;
    }
    
    [x-cloak] { display: none !important; }
</style>
