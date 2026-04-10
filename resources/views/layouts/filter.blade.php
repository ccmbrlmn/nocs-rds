<div class="mx-6 mb-4">

<div class="px-4 py-3 flex items-center w-full gap-4">
    <div class="flex flex-wrap items-center gap-3 flex-shrink-0">

        @if(isset($statuses))
        <div class="flex flex-wrap gap-1">
            @foreach($statuses as $label => $status)
                <a href="{{ route($routeName, array_merge(request()->except('status'), ['status' => $status])) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition
                   {{ request('status', 'all') == $status || ($status === null && !request('status')) 
                        ? 'bg-blue-600 text-white shadow-sm'
                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-blue-100 dark:hover:bg-gray-600' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="h-5 w-px bg-gray-300 dark:bg-gray-600"></div>
        @endif

        @if(isset($dateFilters))
        <div class="flex flex-wrap gap-1">
            @foreach($dateFilters as $key=>$label)
                <a href="{{ route($routeName, array_merge(request()->query(), ['date_filter' => $key])) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition
                   {{ request('date_filter') == $key || (!$key && !request('date_filter'))
                        ? 'bg-blue-600 text-white shadow-sm'
                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-blue-100 dark:hover:bg-gray-600' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="h-5 w-px bg-gray-300 dark:bg-gray-600"></div>
        @endif

        @if(isset($routeName))
        <div>
            <form action="{{ route($routeName) }}" method="GET">
                <input type="date"
                   name="specific_date"
                   value="{{ request('specific_date') }}"
                   class="bg-gray-100 dark:bg-gray-700 hover:bg-blue-100 dark:hover:bg-gray-600
                   text-gray-700 dark:text-gray-200 border-none rounded-xl px-6 py-2.5
                   text-sm font-medium cursor-pointer shadow-sm leading-none
                   focus:outline-none focus:ring-0 focus:border-transparent"
                   onchange="this.form.submit()">

                @foreach(request()->except('specific_date') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
            </form>
        </div>
        @endif

        @if(isset($routeName))
        <div x-data="{ open: false, selected: '{{ request('sort', 'desc') }}' }" class="relative inline-block">
            <button @click="open = !open"
                class="px-5 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 hover:bg-blue-100 dark:hover:bg-gray-600 dark:text-gray-200 rounded-xl text-sm font-medium flex items-center gap-2">
                
                <span x-text="selected === 'desc' ? 'Newest' : 'Oldest'"></span>

                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transform transition-transform"
                     :class="{'rotate-180': open}" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" x-cloak @click.away="open = false" x-transition
                 class="absolute right-0 mt-2 w-28 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">

                <a href="{{ route($routeName, array_merge(request()->except('sort'), ['sort' => 'desc'])) }}"
                   @click="selected='desc'; open=false;"
                   class="block px-3 py-2 text-sm hover:bg-blue-100 dark:hover:bg-gray-600 dark:text-gray-200">
                    Newest
                </a>

                <a href="{{ route($routeName, array_merge(request()->except('sort'), ['sort' => 'asc'])) }}"
                   @click="selected='asc'; open=false;"
                   class="block px-3 py-2 text-sm hover:bg-blue-100 dark:hover:bg-gray-600 dark:text-gray-200">
                    Oldest
                </a>

            </div>
        </div>
        @endif

    </div>

@if(isset($exportPdf) && isset($exportCsv))
<div x-data="{ open:false }" class="relative inline-block">

    <button @click="open = !open"
        class="px-5 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-blue-100 dark:hover:bg-gray-600 rounded-xl text-sm font-medium flex items-center gap-2">

        Export
        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-4 h-4 transform transition-transform"
             :class="{'rotate-180': open}"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7" />
        </svg>

    </button>

    <div x-show="open" x-cloak
         @click.away="open=false"
         x-transition
         class="absolute right-0 mt-2 w-30 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg overflow-hidden z-50">

        <a href="{{ route($exportPdf, request()->query()) }}"
           target="_blank"
           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 transition hover:bg-blue-100 dark:hover:bg-gray-600">
            Export PDF
        </a>

        <a href="{{ route($exportCsv, request()->query()) }}"
           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 transition hover:bg-blue-100 dark:hover:bg-gray-600">
            Export CSV
        </a>

    </div>
</div>
@endif

@if(isset($rightSlot))
<div class="flex items-center gap-2 ml-auto flex-nowrap">
    {!! $rightSlot ?? '' !!}
</div>
@endif
</div>

</div>
