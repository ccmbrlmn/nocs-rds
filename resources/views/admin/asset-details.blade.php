<x-app-layout>

{{-- HEADER --}}
<div class="header-container flex items-center justify-between p-3 mt-8 mb-6">

    <a href="{{ route('admin.assets') }}"
       class="px-4 py-2 rounded-xl text-sm font-medium transition 
              bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 
              hover:bg-blue-100 dark:hover:bg-gray-600 shadow-sm flex items-center gap-2">

        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Back
    </a>

    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
        Asset Details
    </h1>

    <div></div>
</div>


{{-- MAIN WRAPPER --}}
<div 
    class="p-6 rounded-2xl bg-white dark:bg-gray-800 shadow-sm text-gray-800 dark:text-gray-200"
    x-data="{ tab: 'details' }"
>

    {{-- SUCCESS / ERROR --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition
            class="mb-5 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 rounded-xl flex justify-between shadow-sm">
            <span>{{ session('success') }}</span>
            <button @click="show=false">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition
            class="mb-5 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 rounded-xl flex justify-between shadow-sm">
            <span>{{ session('error') }}</span>
            <button @click="show=false">✕</button>
        </div>
    @endif


    {{-- TAB BUTTONS --}}
    <div class="flex gap-2 mb-6 border-b border-gray-200 dark:border-gray-600">

        <button 
            @click="tab = 'details'"
            :class="tab === 'details' 
                ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 font-semibold' 
                : 'text-gray-500 dark:text-gray-400'"
            class="px-4 py-2 text-sm"
        >
            Asset Details
        </button>

        <button 
            @click="tab = 'history'"
            :class="tab === 'history' 
                ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 font-semibold' 
                : 'text-gray-500 dark:text-gray-400'"
            class="px-4 py-2 text-sm"
        >
            Usage History
        </button>

    </div>


    {{-- DETAILS TAB --}}
    <div x-show="tab === 'details'" x-transition>

    @php
        $latest = $asset->transactions->sortByDesc('id')->first();

        $steps = [
            ['label' => 'Available', 'icon' => 'inventory_2'],
            ['label' => 'Borrowed', 'icon' => 'assignment_returned'],
            ['label' => 'Returned', 'icon' => 'assignment_turned_in'],
            ['label' => 'Retrieved', 'icon' => 'inventory'],
        ];

        $currentStep = 0;

        if ($latest) {
            if ($latest->status === 'Borrowed') $currentStep = 1;
            elseif ($latest->status === 'Returned') $currentStep = 2;
            elseif ($latest->status === 'Retrieved') $currentStep = 3;
        }
    @endphp

    @php
        $logs = $asset->transactions->sortBy('created_at');

        $latest = $asset->transactions->sortByDesc('id')->first();

        $borrow = null;
        $return = null;
        $retrieved = null;

        if ($latest) {
            if ($latest->status === 'Borrowed') {
                $borrow = $latest;
            } elseif ($latest->status === 'Returned') {
                $borrow = $latest;
                $return = $latest;
            } elseif ($latest->status === 'Retrieved') {
                $borrow = $latest;
                $return = $latest;
                $retrieved = $latest;
            }
        }

        $assigned = $borrow?->request ?? $return?->request ?? $retrieved?->request;

        $flowSteps = [
            [
                'label' => 'Assigned',
                'icon' => 'assignment_ind',
                'active' => $assigned ? true : false,
                'time' => $assigned?->created_at
            ],
            [
                'label' => 'In Use',
                'icon' => 'play_circle',
                'active' => $borrow ? true : false,
                'time' => $borrow?->borrowed_at
            ],
            [
                'label' => 'Returning',
                'icon' => 'assignment_return',
                'active' => $return ? true : false,
                'time' => $return?->returned_at
            ],
            [
                'label' => 'Retrieved',
                'icon' => 'inventory',
                'active' => $retrieved ? true : false,
                'time' => $retrieved?->retrieved_at
            ],
        ];
    @endphp


    <div class="mb-10">

        <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-2xl p-6 shadow-sm">

        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-6 flex items-center gap-2">
            Asset Progress in 
            <span class="text-gray-800 dark:text-gray-200">
                {{ $assigned?->event_name ?? 'Unknown Event' }}
            </span>
            for 
            <span class="text-gray-800 dark:text-gray-200">
                {{ $assigned?->user?->name ?? 'Unknown User' }}
            </span>
        </h3>
        
        <div class="relative flex items-center justify-between mt-6">

    <div class="absolute top-5 left-0 right-0 h-1 bg-gray-200 dark:bg-gray-600 rounded-full"></div>

    @foreach($flowSteps as $index => $step)

        <div class="relative flex flex-col items-center flex-1 text-center">

            @if($index !== 0)
                <div class="absolute top-5 -left-1/2 w-full h-1
                    {{ $step['active'] ? 'bg-indigo-500' : 'bg-gray-200 dark:bg-gray-600' }}">
                </div>
            @endif

            <div class="z-10 flex items-center justify-center w-12 h-12 rounded-full shadow-md
                {{ $step['active']
                    ? 'bg-indigo-600 text-white scale-105'
                    : 'bg-gray-200 dark:bg-gray-600 text-gray-500' }}">

                <span class="material-symbols-outlined text-[22px]">
                    {{ $step['icon'] }}
                </span>
            </div>

            <p class="mt-3 text-xs font-medium
                {{ $step['active']
                    ? 'text-indigo-600 dark:text-indigo-400'
                    : 'text-gray-500 dark:text-gray-400' }}">
                {{ $step['label'] }}
            </p>

            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">
                @if($step['time'])
                    {{ \Carbon\Carbon::parse($step['time'])->format('M d, Y') }}<br>
                    {{ \Carbon\Carbon::parse($step['time'])->format('h:i A') }}
                @else
                    —
                @endif
            </p>

        </div>

    @endforeach

</div>

        </div>
    </div>


    {{-- HEADER INFO --}}
    <div class="flex flex-wrap items-center justify-between border-b border-gray-200 dark:border-gray-600 pb-5 mb-6">

        <div class="flex flex-wrap items-center gap-3">

            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                {{ $asset->asset_name ?? 'Unknown Asset' }}
            </h2>

            @php
                $statusClasses = [
                    'Available' => 'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300',
                    'In Use' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300',
                    'Maintenance' => 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
                ];

                $statusColor = $statusClasses[$asset->asset_status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
            @endphp

            <span class="px-4 py-1 rounded-xl text-sm font-semibold shadow-sm {{ $statusColor }}">
                {{ $asset->asset_status }}
            </span>
        </div>
    </div>


    {{-- DETAILS --}}
    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-8">

            {{-- LEFT --}}
            <div class="space-y-6">

                <div class="flex gap-4 p-3">
                    <span class="material-symbols-outlined bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 p-2 rounded-lg">inventory_2</span>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Created by</p>
                        <p class="text-base font-medium text-gray-800 dark:text-gray-200">
                            {{ $asset->creator?->name ?? 'Unknown' }}
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 p-3">
                    <span class="material-symbols-outlined bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 p-2 rounded-lg">sell</span>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Asset Tag</p>
                        <p class="text-base font-medium text-gray-800 dark:text-gray-200">
                            {{ $asset->asset_tag ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 p-3">
                    <span class="material-symbols-outlined bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 p-2 rounded-lg">qr_code</span>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Serial Number</p>
                        <p class="text-base font-medium text-gray-800 dark:text-gray-200">
                            {{ $asset->asset_serial ?? '-' }}
                        </p>
                    </div>
                </div>

            </div>

            {{-- RIGHT --}}
            <div class="space-y-6">

                <div class="flex gap-4 p-3">
                    <span class="material-symbols-outlined bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 p-2 rounded-lg">memory</span>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Model</p>
                        <p class="text-base font-medium text-gray-800 dark:text-gray-200">
                            {{ $asset->asset_model ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 p-3">
                    <span class="material-symbols-outlined bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 p-2 rounded-lg">category</span>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Category</p>
                        <p class="text-base font-medium text-gray-800 dark:text-gray-200">
                            {{ $asset->asset_category ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 p-3">
                    <span class="material-symbols-outlined bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 p-2 rounded-lg">schedule</span>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Created on</p>
                        <p class="text-base font-medium text-gray-800 dark:text-gray-200">
                            {{ $asset->created_at?->format('M d, Y h:i A') }}
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>

    </div>


    {{-- HISTORY TAB --}}
    <div x-show="tab === 'history'" x-transition class="mt-6">

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-600 p-6">

            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">
                Usage History
            </h3>

            <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-[500px] overflow-y-auto pr-2">

            @php
                $grouped = $asset->transactions->sortBy('borrowed_at')
                    ->groupBy('request_id');
            @endphp

            @forelse($grouped as $requestId => $logs)

                @php
                    $borrow = $logs->sortBy('borrowed_at')->first();
                    $return = $logs->sortByDesc('returned_at')->first();
                    $request = $logs->first()?->request;
                    $user = $request?->user;
                    $retrievedAt = $logs->sortByDesc('retrieved_at')->first()?->retrieved_at;
                @endphp

                <div class="py-5 flex flex-col gap-2 text-sm border-b border-gray-200 dark:border-gray-700">

                    <div class="flex justify-between items-center">
                        <div class="font-medium text-gray-800 dark:text-gray-200">
                            Borrowed by {{ $user->name ?? 'N/A' }}
                        </div>

                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            @if($borrow && $borrow->borrowed_at)
                                Borrowed on {{ \Carbon\Carbon::parse($borrow->borrowed_at)->format('M d, Y h:i A') }}
                            @else
                                <span class="text-yellow-500 dark:text-yellow-400">No borrow record</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-between items-center pl-4">
                        <div class="text-gray-500 dark:text-gray-400">
                            Returned by {{ $user->name ?? 'N/A' }}
                        </div>

                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            @if($return)
                                Returned on {{ \Carbon\Carbon::parse($return->returned_at)->format('M d, Y h:i A') }}
                            @else
                                <span class="text-yellow-500 dark:text-yellow-400">Not yet returned</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-between items-center pl-4">
                        <div class="text-gray-500 dark:text-gray-400">
                            Retrieved by Admin
                        </div>

                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Retrieved on {{ \Carbon\Carbon::parse($retrievedAt)->format('M d, Y h:i A') }}
                        </div>
                    </div>

                </div>

            @empty
                <div class="text-gray-500 dark:text-gray-400 text-sm py-3">
                    No history yet.
                </div>
            @endforelse

            </div>
        </div>

    </div>

</div>

</x-app-layout>
