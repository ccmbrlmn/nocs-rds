

@php

$status = $request->status;

/*
|--------------------------------------------------------------------------
| GET TIMESTAMPS FROM ASSET TRANSACTIONS
|--------------------------------------------------------------------------
*/

$transactions = $request->transactions ?? collect();

if ($transactions->isEmpty()) {
    // fallback to request-level timestamps
    $activeAt = $request->active_at;
    $returnedAt = $request->returned_at;
    $retrievedAt = $request->retrieved_at;
} else {
    $activeAt = $transactions->min('borrowed_at');
    $returnedAt = $transactions->min('returned_at');
    $retrievedAt = $transactions->min('retrieved_at');
}

$activeAt = $transactions->min('borrowed_at');
$returnedAt = $transactions->min('returned_at');
$retrievedAt = $transactions->min('retrieved_at');

/*
|--------------------------------------------------------------------------
| TIMELINE
|--------------------------------------------------------------------------
*/

$timeline = [
    [
        'label' => 'Request Created',
        'icon' => 'add_circle',
        'time' => $request->created_at,
        'done' => true,
    ],
    [
        'label' => $status === 'Cancelled' ? 'Cancelled' : 'Approved',
        'icon' => $status === 'Cancelled' ? 'cancel' : 'check_circle',
        'time' => $request->approved_at,
        'done' => !is_null($request->approved_at) || $status === 'Cancelled',
    ],
    [
        'label' => 'Active',
        'icon' => 'play_circle',
        'time' => $activeAt,
        'done' => in_array($status, ['Active','Pending Return','Pending Retrieval','Closed']),
    ],
    [
        'label' => 'Pending Return',
        'icon' => 'assignment_return',
        'time' => $returnedAt,
        'done' => in_array($status, ['Pending Return','Pending Retrieval','Closed']),
    ],
    [
        'label' => 'Pending Retrieval',
        'icon' => 'inventory',
        'time' => $retrievedAt,
        'done' => in_array($status, ['Pending Retrieval','Closed']),
    ],
    [
        'label' => 'Closed',
        'icon' => 'task_alt',
        'time' => $status === 'Closed' ? $request->updated_at : null,
        'done' => $status === 'Closed',
    ],
];

$progress = collect($timeline)->where('done', true)->count() / count($timeline) * 100;

@endphp


<div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-600 mb-6">

    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-6">
        Request Progress
    </h3>

    {{-- PROGRESS BAR --}}
    <div class="w-full bg-gray-200 dark:bg-gray-700 h-2 rounded-full mb-8 overflow-hidden">
        <div class="h-2 bg-indigo-500 transition-all duration-700 ease-in-out"
             style="width: {{ $progress }}%"></div>
    </div>

    {{-- TIMELINE --}}
    <div class="flex justify-between relative">

        @foreach($timeline as $step)

            <div class="flex flex-col items-center text-center w-full relative">

                {{-- connector --}}
                @if(!$loop->last)
                    <div class="absolute top-5 left-1/2 w-full h-[2px]
                        {{ $step['done'] ? 'bg-indigo-500' : 'bg-gray-300 dark:bg-gray-600' }}">
                    </div>
                @endif

                {{-- icon --}}
                <div class="w-10 h-10 flex items-center justify-center rounded-full z-10
                    {{ $step['done'] ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">

                    <span class="material-symbols-outlined text-[18px]">
                        {{ $step['icon'] }}
                    </span>
                </div>

                {{-- label --}}
                <p class="mt-2 text-xs font-semibold text-gray-700 dark:text-gray-200">
                    {{ $step['label'] }}
                </p>

                {{-- time --}}
                <p class="text-[11px] text-gray-400">
                    {{ $step['time'] ? \Carbon\Carbon::parse($step['time'])->format('M d, h:i A') : '—' }}
                </p>

            </div>

        @endforeach

    </div>
</div>
