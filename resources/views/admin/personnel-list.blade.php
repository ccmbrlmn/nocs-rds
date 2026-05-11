<x-app-layout>

<div x-data="{
    open: false,
    editOpen: false,
    form: {
        id: null,
        name: '',
        email: '',
        office: ''
    },

    openEdit(person) {
        this.form.id = person.id;
        this.form.name = person.name;
        this.form.email = person.email;
        this.form.office = person.office;
        this.editOpen = true;
    }
}">

    {{-- HEADER --}}
    <div class="header-container rounded-2xl mb-3 mx-3 mt-3">
        <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
                Personnel List
            </div>

            @include('layouts.header')
        </div>
    </div>

    @php
        $highlightPersonnelId = request()->query('highlight');
        $statusColors = config('status');
    @endphp

    {{-- FILTER + BUTTON --}}
    <div class="mx-3 flex items-center justify-between flex-wrap gap-2">

        @include('layouts.filter', [
            'routeName' => 'admin.personnel',

            'statuses' => [
                'All' => null,
                'Active' => 'active',
                'Deleted' => 'deleted',
            ],

            'dateFilters' => [
                '30_days' => '30 Days',
                '7_days' => '7 Days',
                '24_hours' => '24 Hours'
            ],

            'exportPdf' => 'admin.personnel.pdf',
            'exportCsv' => 'admin.personnel.csv',
        ])

        {{-- ADD PERSONNEL --}}
        <button
            @click="open = true"
            class="px-4 py-2 rounded-xl text-sm font-medium transition
                bg-indigo-600 text-white
                        hover:bg-indigo-700
                        dark:bg-indigo-500 dark:hover:bg-indigo-600">
            Add Personnel
        </button>

    </div>

    {{-- TABLE --}}
    <div class="request-history-list rounded-xl shadow overflow-hidden mx-10">

        {{-- HEADER --}}
        <div class="head bg-blue-100 dark:bg-blue-900 px-4 py-2 flex justify-between text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-t-xl">

            <div class="w-1/6 text-center">ID</div>
            <div class="w-2/6 text-center">Name</div>
            <div class="w-2/6 text-center">Email</div>
            <div class="w-2/12 text-center">Office</div>
            <div class="w-1/6 text-center">Created</div>
            <div class="w-1/6 text-center">Status</div>
            <div class="w-1/6 text-center">Actions</div>
        </div>

        {{-- BODY --}}
        <div class="request-history-wrapper divide-y divide-gray-200 dark:divide-gray-700">

            @forelse(($personnel ?? []) as $p)

                @php
                    $isHighlighted = $highlightPersonnelId == $p->id;
                @endphp

                <div class="request-row {{ $isHighlighted ? 'highlighted-admin' : '' }}
                    bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700 transition cursor-pointer"
                    onclick="window.location='{{ route('admin.users.logs', $p->id) }}'">

                    <div class="row flex items-center px-6 py-3 text-sm">

                        <div class="w-1/6 text-center text-gray-600 dark:text-gray-300">
                            {{ $p->id }}
                        </div>

                        <div class="w-2/6 text-center text-gray-700 dark:text-gray-200 font-medium">
                            {{ $p->name }}
                        </div>

                        <div class="w-2/6 text-center text-gray-600 dark:text-gray-300">
                            {{ $p->email }}
                        </div>

                        <div class="w-2/12 text-center text-gray-600 dark:text-gray-300">
                            {{ $p->office ?? '-' }}
                        </div>

                        <div class="w-1/6 text-center text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($p->created_at)->format('M d, Y') }}
                        </div>

                        {{-- STATUS --}}
                        <div class="w-1/6 flex justify-center">
                            @php
                                $status = $p->deleted_at ? 'Deleted' : 'Active';

                                $statusConfig = config('status')[$status] ?? null;

                                $statusClass = $statusConfig
                                    ? $statusConfig['bg'].' '.$statusConfig['text']
                                    : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-200';
                            @endphp

                            <span class="px-3 py-1 rounded-xl text-sm font-semibold {{ $statusClass }}">
                                {{ $status }}
                            </span>
                        </div>

                        {{-- ACTIONS --}}
                        <div class="w-1/6 flex justify-center gap-2"
                             onclick="event.stopPropagation();">

                            @if($p->deleted_at)

                                <form action="{{ route('admin.personnel.restore', $p->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md font-semibold shadow">
                                        Restore
                                    </button>
                                </form>

                            @else

                                <button
                                    @click="openEdit({{ $p }})"
                                    class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded-md font-semibold shadow">
                                    Edit
                                </button>

                                <form action="{{ route('admin.personnel.destroy', $p->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this personnel?');">
                                    @csrf
                                    @method('DELETE')

                                    <button class="bg-rose-600 hover:bg-rose-700 text-white px-3 py-1 rounded-md font-semibold shadow">
                                        Delete
                                    </button>
                                </form>

                            @endif

                        </div>

                    </div>
                </div>

            @empty
                <div class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                    No personnel accounts found.
                </div>
            @endforelse

        </div>
    </div>

{{-- MODAL --}}
<div x-show="open"
     x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl w-full max-w-lg">

        <h2 class="text-gray-800 dark:text-gray-200 text-lg font-bold mb-4">
            Create Personnel
        </h2>

        <form method="POST" action="{{ route('admin.personnel.store') }}" class="space-y-3">
            @csrf

            <input type="text" name="name" placeholder="Name"
                class="w-full border p-2 rounded
                bg-white dark:bg-gray-900
                text-gray-800 dark:text-gray-200
                border-gray-300 dark:border-gray-600
                placeholder-gray-400 dark:placeholder-gray-500
                focus:ring-indigo-500 focus:border-indigo-500">

            <input type="email" name="email" placeholder="Email"
                class="w-full border p-2 rounded
                bg-white dark:bg-gray-900
                text-gray-800 dark:text-gray-200
                border-gray-300 dark:border-gray-600
                placeholder-gray-400 dark:placeholder-gray-500
                focus:ring-indigo-500 focus:border-indigo-500">

            <input type="password" name="password" placeholder="Password"
                class="w-full border p-2 rounded
                bg-white dark:bg-gray-900
                text-gray-800 dark:text-gray-200
                border-gray-300 dark:border-gray-600
                placeholder-gray-400 dark:placeholder-gray-500
                focus:ring-indigo-500 focus:border-indigo-500">

            <input type="text" name="office" placeholder="Office"
                class="w-full border p-2 rounded
                bg-white dark:bg-gray-900
                text-gray-800 dark:text-gray-200
                border-gray-300 dark:border-gray-600
                placeholder-gray-400 dark:placeholder-gray-500
                focus:ring-indigo-500 focus:border-indigo-500">

            <div class="flex justify-end gap-2 mt-4">

                <button type="button"
                        @click="open = false"
                        class="px-4 py-2 rounded
                        bg-gray-400 dark:bg-gray-600
                        text-white">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 rounded
                        bg-blue-600 hover:bg-blue-700
                        text-white">
                    Create
                </button>

            </div>
        </form>

    </div>
</div>

    <div x-show="editOpen"
     x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl w-full max-w-lg">

        <h2 class="text-lg font-bold mb-4">Edit Personnel</h2>

        <form :action="`/admin/personnel/${form.id}`" method="POST" class="space-y-3">
            @csrf
            @method('PUT')

            <input type="text" name="name" x-model="form.name"
                   class="w-full border p-2 rounded">

            <input type="email" name="email" x-model="form.email"
                   class="w-full border p-2 rounded">

            <input type="text" name="office" x-model="form.office"
                   class="w-full border p-2 rounded">

            <div class="flex justify-end gap-2 mt-4">

                <button type="button"
                        @click="editOpen = false"
                        class="px-4 py-2 bg-gray-400 text-white rounded">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded">
                    Update
                </button>

            </div>
        </form>

    </div>
</div>

</div>
</x-app-layout>
