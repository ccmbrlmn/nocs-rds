<x-app-layout>
    <div class="header-container flex items-center justify-between p-3 mt-8 mb-6">

        <a href="{{ route('admin.requests') }}"
           class="px-4 py-2 rounded-xl text-sm font-medium transition 
                  bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 
                  hover:bg-blue-100 dark:hover:bg-gray-600 shadow-sm flex items-center gap-2">

            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Back
        </a>

        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
                Request Application
            </h1>
        </div>

    </div>

    <div class="request-details p-6 rounded-2xl bg-white dark:bg-gray-800 shadow-sm">

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

        <div class="mb-8">
            <x-request-timeline :request="$request" />
        </div>

        <div class="request-header flex flex-wrap items-start justify-between border-b border-gray-200 dark:border-gray-500 pb-5 mb-6">

            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
                    {{ $request->event_name ?? 'Unknown Event' }}
                </h2>

                @php
                    $status = $request->status;

                    $statusClasses = [
                        'Open' => 'bg-amber-100 text-amber-700 dark:bg-amber-700 dark:text-amber-200',
                        'Active' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-700 dark:text-indigo-200',
                        'Pending Return' => 'bg-sky-100 text-sky-700 dark:bg-sky-700 dark:text-sky-200',
                        'Pending Retrieval' => 'bg-purple-100 text-purple-700 dark:bg-purple-700 dark:text-purple-200',
                        'Closed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-200',
                        'Cancelled' => 'bg-rose-100 text-rose-700 dark:bg-rose-700 dark:text-rose-200',
                    ];

                    $statusColor = $statusClasses[$status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200';
                @endphp

                <span class="inline-flex items-center gap-2 px-4 py-1 rounded-xl text-sm font-semibold shadow-sm {{ $statusColor }}">
                    <span class="material-symbols-outlined text-sm">info</span>
                    {{ $status }}
                </span>
            </div>

        </div>


        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-500 mb-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-8">

                {{-- LEFT --}}
                <div class="space-y-6">

                    <x-request-field icon="location_on" label="Location" :value="$request->location" />

                    <x-request-field icon="person" label="Requester"
                        :value="optional($request->user)->name ?? 'Unknown User'" />

                    <x-request-field icon="badge" label="Requested Employee"
                        :value="$request->requested_employee ?? 'N/A'" />

                    <x-request-field icon="event_available" label="Purpose"
                        :value="$request->purpose === 'Others' ? $request->other_purpose : $request->purpose" />

                    <x-request-field icon="notes" label="Notes"
                        :value="$request->note ?? 'N/A'" />

                </div>

                <div class="space-y-6">

                    <div class="flex gap-4 p-3">
                        <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">inventory_2</span>
                        <div>
                            <p class="header-text">Requested Items</p>

                            @php
                                $items = is_array($request->items)
                                    ? $request->items
                                    : json_decode($request->items, true) ?? [];
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

                    <x-request-field icon="calendar_month" label="Event Date"
                        :value="\Carbon\Carbon::parse($request->start_date)->format('M d') . ' - ' . \Carbon\Carbon::parse($request->end_date)->format('d, Y')" />

                    <x-request-field icon="schedule" label="Setup"
                        :value="$request->setup_date . ' | ' . $request->setup_time" />

                    <x-request-field icon="group" label="Users"
                        :value="number_format($request->users)" />

                    <x-request-field icon="email" label="Contact"
                        :value="optional($request->user)->email ?? '-'" />

                </div>

            </div>
        </div>


        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-500">

            @if(in_array($request->status, ['Active', 'Closed', 'Cancelled']))
                <div class="space-y-6">

                    @if($request->handledByAdmin)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-8">

                            @php
                                $actionStatus = $request->status;

                                if($actionStatus === 'Cancelled') {
                                    $icon = 'cancel';
                                    $actionText = 'Cancelled the Request';
                                    $iconBg = 'bg-red-600';
                                } elseif(in_array($actionStatus, ['Active', 'Closed'])) {
                                    $icon = 'check_circle';
                                    $actionText = 'Accepted the Request';
                                    $iconBg = 'bg-green-600';
                                } else {
                                    $icon = 'info';
                                    $actionText = 'No action yet';
                                    $iconBg = 'bg-gray-400';
                                }
                            @endphp

                            <div class="flex items-start gap-4 p-3 rounded-lg transition">
                                <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">
                                    manage_accounts
                                </span>
                                <div>
                                    <p class="header-text font-semibold dark:text-gray-300">Name of Personnel</p>

                                    @php
                                        $admin = $request->handledByAdmin;
                                        $adminClass = ($admin && $admin->trashed())
                                            ? 'text-red-600 italic'
                                            : 'text-gray-800';
                                    @endphp

                                    <p class="detail-text dark:text-gray-300 {{ $adminClass }}">
                                        {{ $admin->name ?? 'Unknown Admin' }}
                                        @if($admin && $admin->trashed())
                                            (Deleted)
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 p-3 rounded-lg transition">
                                <span class="material-symbols-outlined bg-blue-100 text-blue-600 p-2 rounded-lg">
                                    {{ $icon }}
                                </span>
                                <div>
                                    <p class="header-text font-semibold dark:text-gray-300">Personnel Action</p>
                                    <p class="detail-text dark:text-gray-300">{{ $actionText }}</p>
                                </div>
                            </div>

                        </div>
                    @endif

                </div>
            @else
                <p class="detail-text text-gray-500 dark:text-gray-200">
                    No deployment information available yet.
                </p>
            @endif

        </div>

    </div>
</x-app-layout>
