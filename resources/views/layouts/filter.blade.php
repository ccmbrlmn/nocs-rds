@php
    $showCreate = $showCreate ?? false;
@endphp

<div class="mx-6 mb-4" x-data>

    <div class="flex items-center justify-between gap-4 flex-wrap">

        <div class="flex items-center gap-3 flex-wrap">

            <div class="flex bg-gray-100 dark:bg-gray-700 rounded-xl p-1">
                @foreach($statuses as $label => $status)
                    <a href="{{ route($routeName, array_merge(request()->except('status'), ['status' => $status])) }}"
                       class="px-3 py-1.5 text-sm rounded-lg transition font-medium
                       {{ request('status') == $status || ($status === null && !request('status'))
                            ? 'bg-white dark:bg-gray-900 text-indigo-600 shadow'
                            : 'text-gray-500 dark:text-gray-300 hover:text-gray-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <!-- MORE FILTERS -->
            <div x-data="{ open:false }" class="relative">

                <button @click="open = !open"
                    class="px-3 py-2 text-sm rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 flex items-center gap-2">


                    <span class="text-gray-600 dark:text-gray-200 font-medium">
                        Filters
                    </span>
                </button>

                <div x-show="open" @click.away="open=false" x-transition
                     class="absolute mt-2 p-4 w-60 bg-white dark:bg-gray-800 rounded-xl shadow-xl z-50 space-y-3">

                    <!-- DATE FILTER -->
                    <div class="flex flex-wrap gap-2">
                        @foreach($dateFilters as $key => $label)
                            <a href="{{ route($routeName, array_merge(request()->query(), ['date_filter' => $key])) }}"
                            class="px-3 py-1 text-xs rounded-lg transition
                            {{ request('date_filter') == $key
                                    ? 'bg-indigo-600 text-white dark:bg-indigo-500'
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <!-- SPECIFIC DATE -->
                    <form action="{{ route($routeName) }}" method="GET">
                        <input type="date" name="specific_date"
                            value="{{ request('specific_date') }}"
                            class="w-full rounded-lg bg-gray-100 dark:bg-gray-700 text-sm px-3 py-2 border-none text-gray-800 dark:text-gray-100"
                            style="color-scheme: dark;">
                    </form>

                    <!-- SORT -->
                    <div class="flex gap-2">
                        <a href="{{ route($routeName, array_merge(request()->except('sort'), ['sort'=>'desc'])) }}"
                           class="px-3 py-1 text-xs rounded-lg
                           {{ request('sort')=='asc'
    ? 'bg-indigo-600 text-white'
    : 'bg-gray-100 dark:bg-gray-700 dark:text-gray-200' }}">
                            Newest
                        </a>

                        <a href="{{ route($routeName, array_merge(request()->except('sort'), ['sort'=>'asc'])) }}"
                           class="px-3 py-1 text-xs rounded-lg
                           {{ request('sort')=='asc'
    ? 'bg-indigo-600 text-white'
    : 'bg-gray-100 dark:bg-gray-700 dark:text-gray-200' }}">
                            Oldest
                        </a>
                    </div>

                </div>
            </div>

            <!-- EXPORT -->
            <div x-data="{ open:false }" class="relative">

                <button @click="open = !open"
                    class="px-3 py-2 text-sm rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 flex items-center gap-2 transition">

                    <span class="text-gray-700 dark:text-gray-200 font-medium">
                        Export
                    </span>
                </button>

                <div x-show="open" @click.away="open=false" x-transition
                     class="absolute mt-2 w-28 bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden z-50">

                    <a href="{{ route($exportPdf, request()->query()) }}"
                    target="_blank"
                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                        Export PDF
                    </a>

                    <a href="{{ route($exportCsv, request()->query()) }}"
                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                        Export CSV
                    </a>

                </div>
            </div>

        </div>

        <div class="flex items-center gap-3 ml-auto">

            @auth
                @if($showCreate)
                    <button
                        @click="$dispatch('open-request-form')"
                        class="px-3 py-2 text-sm rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 flex items-center gap-2 transition shadow whitespace-nowrap">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4"/>
                        </svg>

                        Create Request
                    </button>
                @endif
            @endauth

        </div>

    </div>
</div>
