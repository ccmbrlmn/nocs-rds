<x-app-layout>
    <div 
        class="page-wrapper flex flex-col h-screen"
        x-data="assignAssets()"
    >

        <!-- HEADER -->
        <div class="header-container rounded-2xl mb-3 mx-3 mt-3">
            <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            
                <a 
        href="{{ auth()->user()->role === 'admin' ? route('admin.requests') : route('user.requests') }}"
        class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium transition"
    >
        ← Back
    </a>
    

                <div class="text-2xl font-semibold text-gray-800 dark:text-gray-200 tracking-tight">
                    Assign Assets for {{ $request->user->name }} — {{ $request->event_name }}
                </div>

                @include('layouts.header')
            </div>
        </div>

        <div class="mx-10">

            <form action="{{ route('admin.requests.assign.store', $request->id) }}" method="POST">
                @csrf

                <div class="request-history-list rounded-xl shadow overflow-hidden">

                    <!-- HEADER -->
                    <div class="head bg-blue-100 dark:bg-blue-900 px-4 py-2 flex justify-between text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-t-xl">
                        <div class="w-1/12 text-center"></div>
                        <div class="w-4/12 text-center">Asset</div>
                        <div class="w-4/12 text-center">Category</div>
                        <div class="w-3/12 text-center">Status</div>
                    </div>

                    <!-- SCROLLABLE BODY -->
                    <div class="request-history-wrapper max-h-[60vh] overflow-y-auto">

                        @forelse($assets as $asset)

                            <div class="block bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700">

                                <div class="flex justify-between items-center px-4 py-3 text-sm">

                                    <!-- CHECKBOX -->
                                    <div class="w-1/12 text-center">
                                        <input 
                                            type="checkbox" 
                                            name="assets[]" 
                                            value="{{ $asset->id }}"
                                            @change="toggleSelection($event)"
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        >
                                    </div>

                                    <!-- NAME -->
                                    <div class="w-4/12 text-center">
                                        {{ $asset->asset_name }}
                                    </div>

                                    <!-- CATEGORY -->
                                    <div class="w-4/12 text-center">
                                        {{ $asset->asset_category }}
                                    </div>

                                    <!-- STATUS -->
                                    <div class="w-3/12 text-center">
                                        <span class="px-3 py-1 rounded-xl bg-green-100 text-green-700 text-xs font-semibold">
                                            {{ $asset->asset_status }}
                                        </span>
                                    </div>

                                </div>
                            </div>

                        @empty
                            <div class="px-6 py-6 text-center text-gray-500">
                                No available assets.
                            </div>
                        @endforelse

                    </div>
                </div>

                <!-- ACTION -->
                <div class="sticky bottom-0 bg-white dark:bg-gray-800 border-t px-4 py-3 flex justify-between items-center">

                    <div class="text-sm text-gray-600 dark:text-gray-300">
                        Selected: 
                        <span class="font-semibold text-indigo-600" x-text="selectedCount"></span>
                    </div>

                    <button 
                        type="button"
                        @click="openConfirmModal()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg shadow disabled:opacity-50"
                        :disabled="selectedCount === 0"
                    >
                        Confirm Assignment
                    </button>

                </div>

            </form>

        </div>

        <!-- CONFIRM MODAL -->
        <div x-show="confirmOpen" x-cloak
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">

            <div class="bg-white rounded-xl p-6 w-[400px] shadow-xl">

                <h2 class="text-lg font-semibold mb-4">Confirm Assignment</h2>

                <p class="text-sm text-gray-600 mb-2">
                    You selected 
                    <span class="font-semibold text-indigo-600" x-text="selectedCount"></span> 
                    asset(s).
                </p>

                <p class="text-sm text-gray-500 mb-6">
                    Do you want to assign these assets to this request?
                </p>

                <div class="flex justify-end gap-2">
                    <button @click="confirmOpen = false"
                        class="px-3 py-1 bg-gray-400 text-white rounded-lg">
                        Select More
                    </button>

                    <button @click="submitForm()"
                        class="px-3 py-1 bg-indigo-600 text-white rounded-lg">
                        Confirm
                    </button>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>

<script>
function assignAssets() {
    return {
        confirmOpen: false,
        selectedCount: 0,

        toggleSelection(event) {
            if (event.target.checked) {
                this.selectedCount++;
            } else {
                this.selectedCount--;
            }
        },

        openConfirmModal() {
            if (this.selectedCount === 0) {
                alert('Please select at least one asset.');
                return;
            }
            this.confirmOpen = true;
        },

        submitForm() {
            this.$root.querySelector('form').submit();
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }

.request-history-wrapper::-webkit-scrollbar { width: 6px; }
.request-history-wrapper::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}
.request-history-wrapper::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
