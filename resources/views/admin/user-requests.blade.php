<x-app-layout>
<div class="page-wrapper flex flex-col h-screen"
     x-data="returnModal()"
     x-init="
        editOpen = false;
        window.addEventListener('close-edit', () => closeEditModal());
     "
     @open-request-form.window="openRequestForm = true"
>
    

    <!-- HEADER -->
    <div class="header-container rounded-2xl mb-3 mx-3 mt-3">
        <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
                User Requests
            </div>
            @include('layouts.header')
        </div>
        
    </div>

    @include('layouts.filter', [
        'routeName' => 'user.requests',
        'statuses' => [
            'All' => null,
            'Open' => 'Open',
            'Active' => 'Active',
            'Closed' => 'Closed',
            'Cancelled' => 'Cancelled',
            'Pending Return' => 'Pending Return'
        ],
        'dateFilters' => [
            '30_days' => '30 Days',
            '7_days' => '7 Days',
            '24_hours' => '24 Hours'
        ],
        'exportPdf' => 'user.requests.pdf',
        'exportCsv' => 'user.requests.csv',
        
        'showCreate' => true
    ])

    <!-- TABLE -->
    <div class="request-history-list rounded-xl shadow overflow-hidden mx-10">

        <!-- HEADER -->
        <div class="bg-blue-100 dark:bg-blue-900 px-4 py-2 flex justify-between text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-t-xl">
            <div class="w-2/6 text-center">Event</div>
            <div class="w-1/6 text-center">Date</div>
            <div class="w-1/6 text-center">Purpose</div>
            <div class="w-1/6 text-center">Status</div>
            <div class="w-1/6 text-center">Action</div>
        </div>

        <div class="request-history-wrapper max-h-[60vh] overflow-y-auto">

            @forelse ($requests as $request)
                @php
                    $status = $request->computed_status;
                    $statusClasses = [
                        'Open' => 'bg-amber-100 text-amber-700 dark:bg-amber-700 dark:text-amber-200',
                        'Active' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-700 dark:text-indigo-200',
                        'Closed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-200',
                        'Cancelled' => 'bg-rose-100 text-rose-700 dark:bg-rose-700 dark:text-rose-200',
                        'Pending Return' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-700 dark:text-yellow-200',
                    ];
                @endphp

                <div class="flex items-center px-4 py-3 text-sm bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700">

                    <!-- ROW -->
                    <div onclick="window.location='{{ route('request-details.show', $request->id) }}'"
                        class="flex w-5/6 justify-between items-center cursor-pointer">

                        <div class="w-2/6 text-center text-gray-600 dark:text-gray-300">
                            {{ $request->event_name }}
                        </div>

                        <div class="w-1/6 text-center text-gray-600 dark:text-gray-300">
                            {{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}
                        </div>

                        <div class="w-1/6 text-center text-gray-600 dark:text-gray-300">
                            {{ $request->purpose === 'Others' ? $request->other_purpose : $request->purpose }}
                        </div>

                        <div class="w-1/6 text-center">
                            <span class="px-3 py-1 rounded-xl text-sm font-medium
                                {{ $statusClasses[$status] ?? 'bg-gray-200 text-gray-600' }}">
                                {{ $status }}
                            </span>
                        </div>
                    </div>

                    <!-- ACTIONS -->
                    <div class="w-1/6 flex justify-center gap-2 relative z-10">

                        {{-- RETURN --}}
                        @if($status === 'Active')
                            <button type="button"
                                    @click.stop="openReturnModal({{ $request->id }})"
                                    class="text-xs px-3 py-1 rounded-lg bg-orange-500 text-white hover:bg-orange-600">
                                Return
                            </button>
                        @else
                            <button disabled class="text-xs px-3 py-1 rounded-lg bg-gray-400 text-white opacity-60">
                                Return
                            </button>
                        @endif

                        {{-- EDIT --}}
                        @if($status === 'Open' && !$request->is_edited)
                            <button type="button"
                                @click.stop="confirmEdit({{ $request->id }}, {{ $request->is_edited ? '1' : '0' }})"
                                class="text-xs px-3 py-1 rounded-lg bg-blue-500 text-white hover:bg-blue-600">
                                Edit
                            </button>
                        @else
                            <button disabled class="text-xs px-3 py-1 rounded-lg bg-gray-400 text-white opacity-60">
                                Edit
                            </button>
                        @endif

                    </div>
                </div>
            @empty
                <div class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                    No requests found.
                </div>
            @endforelse

        </div>
    </div>
    
<!-- EDIT MODAL -->
<div x-show="editOpen"
     x-cloak
     class="fixed inset-0 flex items-center justify-center z-50">

    <!-- BACKDROP -->
    <div class="absolute inset-0 bg-black bg-opacity-50"
         @click="closeEditModal()"></div>

    <!-- MODAL BOX -->
    <div class="relative">

        <!-- CLOSE -->
        <button type="button"
                @click="closeEditModal()"
                class="absolute top-3 right-3 text-gray-500 hover:text-red-500 text-xl">
            ✕
        </button>

        <div x-html="editForm"></div>

    </div>
</div>

<div x-show="confirmEditOpen"
     x-cloak
     class="fixed inset-0 flex items-center justify-center z-50">

    <!-- BACKDROP -->
    <div class="absolute inset-0 bg-black bg-opacity-50"
         @click="confirmEditOpen = false"></div>

    <!-- MODAL -->
    <div class="bg-white rounded-xl p-6 w-[400px] shadow-xl relative z-10">

        <h2 class="text-lg font-semibold mb-2">
            Edit Request
        </h2>

        <p class="text-sm text-gray-600 mb-4">
            You can only edit this request <strong>once</strong>.
            Do you want to proceed?
        </p>

        <div class="flex justify-end gap-2">
            <button @click="confirmEditOpen = false"
                    class="px-3 py-1 bg-gray-400 text-white rounded-lg">
                Cancel
            </button>

            <button 
                @click="
                    confirmEditOpen = false;
                    openEditModal(selectedRequestId);
                "
                class="px-3 py-1 bg-blue-500 text-white rounded-lg">
                Proceed
            </button>
        </div>
    </div>
</div>



    <!-- RETURN MODAL -->
    <div x-show="open" x-cloak
         class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">

        <div class="bg-white rounded-xl p-6 w-[400px] shadow-xl"
             @click.away="closeModal()">

            <h2 class="text-lg font-semibold mb-4">Return Checklist</h2>

            <div class="space-y-2 mb-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" x-model="checklist.complete">
                    Items are complete
                </label>

                <label class="flex items-center gap-2">
                    <input type="checkbox" x-model="checklist.notLost">
                    No items lost
                </label>

                <label class="flex items-center gap-2">
                    <input type="checkbox" x-model="checklist.notDamaged">
                    No items damaged
                </label>
            </div>

            <div class="flex justify-end gap-2">
                <button @click="closeModal()"
                        class="px-3 py-1 bg-gray-400 text-white rounded-lg">
                    Cancel
                </button>

                <form x-bind:action="formAction" method="POST">
                    @csrf
                    <button type="submit"
                            :disabled="!isValid"
                            class="px-3 py-1 bg-orange-500 text-white rounded-lg disabled:bg-gray-400">
                        Confirm Return
                    </button>
                </form>
            </div>

        </div>
    </div>
        @include('form.request-form')
    </div>

</div>
</div>

<div 
    x-show="showToast"
    x-transition
    x-cloak
    class="fixed top-5 right-5 z-[999] px-4 py-3 rounded-lg shadow-lg text-white text-sm"
    :class="toastType === 'error' ? 'bg-red-500' : 'bg-green-500'"
>
    <span x-text="toastMessage"></span>
</div>



</x-app-layout>

<script>
function returnModal() {
    return {
        open: false,
        editOpen: false,
        confirmEditOpen: false,
        selectedRequestId: null,
        editForm:'',
        formAction: '',
        showToast: false,
        toastMessage: '',
        toastType: 'error',
        checklist: {
            complete: false,
            notLost: false,
            notDamaged: false
        },
        
        openRequestForm: false,
view: new URLSearchParams(window.location.search).get('view') || 'list',
        
        showToastMessage(message, type = 'error') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;

            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        },
        
        confirmEdit(id, alreadyEdited) {
            alreadyEdited = Number(alreadyEdited);

            if (alreadyEdited === 1) {
                this.showToastMessage(
                    "This request has already been edited. You can only edit it once.",
                    "error"
                );
                return;
            }

            this.selectedRequestId = id;
            this.confirmEditOpen = true;
        },

        get isValid() {
            return this.checklist.complete &&
                   this.checklist.notLost &&
                   this.checklist.notDamaged;
        },

        openReturnModal(id) {
            this.formAction = `/request/${id}/return`;
            this.open = true;
        },

        closeModal() {
            this.open = false;
            this.formAction = '';
            this.checklist = {
                complete: false,
                notLost: false,
                notDamaged: false
            };
        },
        
        openEditModal(id) {
            this.editOpen = true;
            this.editForm = `<p class="text-gray-500">Loading...</p>`;

            fetch(`/requests/${id}/edit`)
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Not allowed');
                    }
                    return res.text();
                })
                .then(html => {
                    this.editForm = html;
                })
                .catch(() => {
                    this.editForm = `<p class="text-red-500">You are not allowed to edit this request anymore.</p>`;
                    this.editOpen = true;
                });
        },

        closeEditModal() {
            this.editOpen = false;
            this.editForm = '';
        }
    }
}
</script>

<style>

.header-container {
    margin-left: 1.5rem;
    margin-right: 1.5rem;
}

.request-history-list {
    padding-left: 0;
    padding-right: 0;
}
.material-symbols-outlined {
    font-size: 28px;
    vertical-align: middle;
}

.filter-container {
    margin-left: 1.5rem;
    margin-right: 1.5rem;
    margin-bottom: 0.5rem;
}

.request-history-list {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 300px);
    position: relative;
    margin-left: 1.5rem;
    margin-right: 1.5rem;
    padding-left: 0;
    padding-right: 0;

    overflow: visible;
}

.request-history-wrapper {
    position: relative;
    top: auto;
    bottom: auto;
    left: auto;
    right: auto;
    overflow: visible;
    overflow-y: auto;       
    max-height: 60vh;      

.request-history-wrapper::-webkit-scrollbar {
    width: 8px;
}

.request-history-wrapper::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.request-history-wrapper::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.request-history-wrapper::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.sort-tab .sort-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-color: white;
    border: none;
    font-size: 0.875rem;
    font-weight: 500;
    color: #6B7280;
    text-align: center;
    border-radius: 0.375rem;
    cursor: pointer;
    padding: 0 30px 0 12px;
    height: 44px;
    min-width: 160px;
}

.sort-tab .sort-select:focus {
    outline: none;
    box-shadow: none;
}

.sort-tab .sort-select::-ms-expand {
    display: none;
}

.sort-tab {
    position: relative;
}

.sort-tab::after {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #6B7280;
    font-size: 0.875rem;
}

</style>

