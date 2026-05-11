@if ($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
        <ul>
            @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center px-4">

    <div class="bg-white dark:bg-gray-800 dark:text-gray-200
            rounded-2xl w-full max-w-5xl
            max-h-[90vh] flex flex-col overflow-hidden shadow-xl">

        <!-- HEADER -->
        <div x-data="clock()" x-init="start()"
             class="flex justify-between items-center px-6 pt-6 pb-2 border-b dark:border-gray-700">

            <h1 class="text-lg font-semibold">Edit Request Form</h1>

            <div class="flex items-center gap-4">
                <div class="text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                    <span x-text="formattedDate"></span>
                    <span class="mx-1">•</span>
                    <span x-text="formattedTime"></span>
                </div>

                <button type="button"
                        @click="window.dispatchEvent(new CustomEvent('close-edit'))"
                        class="text-gray-500 hover:text-gray-700 dark:hover:text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>

            </div>
        </div>

        <!-- FORM -->
        <form action="{{ route('requests.update', $request->id) }}" method="POST"
              class="flex flex-col flex-1 overflow-hidden">
            @csrf
            @method('PUT')

            <!-- BODY -->
            <div class="overflow-y-auto px-6 py-4">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <!-- LEFT -->
                    <div>

                        <!-- Representative -->
                        <div class="mb-4">
                            <x-input-label value="Name of Representative" />
                            <x-text-input name="representative_name"
                                          class="block mt-1 w-full"
                                          value="{{ $request->representative_name }}"
                                          required />
                        </div>

                        <!-- Requested Employee -->
                        <div class="mb-4 relative"
                             x-data="{
                                showPersonnel: false,
                                selected: @js($request->requested_employee),
                                personnelList: @js($personnel ?? [])
                             }">

                            <div class="flex justify-between items-center mb-1">
                                <x-input-label value="Requested Employee (Optional)" />

                                <button type="button"
                                        @click="showPersonnel = !showPersonnel"
                                        class="text-xs text-indigo-600 hover:underline">
                                    Browse NOCS Personnel
                                </button>
                            </div>

                            <x-text-input name="requested_employee"
                                          x-model="selected"
                                          class="block mt-1 w-full" />

                            <div x-show="showPersonnel"
                                 @click.outside="showPersonnel = false"
                                 class="absolute z-50 mt-2 w-full bg-white dark:bg-gray-900
                                        border rounded-lg shadow-lg max-h-44 overflow-y-auto">

                                <template x-for="p in personnelList" :key="p.id">
                                    <button type="button"
                                            @click="selected = p.name; showPersonnel = false"
                                            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <div x-text="p.name"></div>
                                        <div class="text-xs text-gray-400" x-text="p.office ?? '-'"></div>
                                    </button>
                                </template>

                            </div>
                        </div>

                        <!-- Event -->
                        <div class="mb-4">
                            <x-input-label value="Name of Event" />
                            <x-text-input name="event_name"
                                          class="block mt-1 w-full"
                                          value="{{ $request->event_name }}"
                                          required />
                        </div>

                        <!-- Purpose -->
                        <div x-data="{
                            purpose: @js($request->purpose)
                        }" class="mb-6">

                            <x-input-label value="Purpose" />

                            <select name="purpose"
                                    x-model="purpose"
                                    class="block mt-1 w-full rounded-md dark:bg-gray-900">

                                <option value="Conference">Conference</option>
                                <option value="VideoCon">Video Con</option>
                                <option value="Training">Training</option>
                                <option value="Others">Others</option>
                            </select>

                            <div x-show="purpose === 'Others'" class="mt-3">
                                <x-input-label value="Specify Purpose" />
                                <input type="text"
                                       name="other_purpose"
                                       value="{{ $request->other_purpose }}"
                                       class="block mt-1 w-full rounded-md dark:bg-gray-900">
                            </div>
                        </div>

                        <!-- ITEMS -->
<div x-data="{
    maxItems: 20,
    showAssets: false,
    items: @js(
        is_array($request->items)
        ? $request->items
        : json_decode($request->items ?? '[]', true)
    ),
    assets: @js($assets ?? [])
}" class="relative">

<div class="flex items-center justify-between mb-2 relative z-10">
    <x-input-label value="Requested Items" />

    <!-- RIGHT SIDE ACTIONS -->
    <div class="flex items-center gap-3">

        <!-- Browse Available Items -->
        <div class="relative inline-block">

            <button type="button"
                    @click="showAssets = !showAssets"
                    class="text-sm text-indigo-600 hover:underline
                           active:scale-95 transition-transform duration-100">
                <span x-text="showAssets ? 'Hide Available Items' : 'Browse Available Items'"></span>
            </button>

            <!-- ASSET LIST -->
            <div x-show="showAssets"
     x-transition
     @click.outside="showAssets = false"
     class="absolute left-0 top-full mt-2 z-50 w-64 p-3 border rounded-lg
            bg-gray-50 dark:bg-gray-900 max-h-40 overflow-auto shadow-xl">

                <template x-if="assets.length === 0">
                    <p class="text-sm text-gray-500">No items available</p>
                </template>

                <template x-for="(asset, index) in assets" :key="asset + '-' + index">
                    <button type="button"
                            @click="items.push({ name: asset, quantity: 1 }); showAssets = false"
                            class="block w-full text-left px-2 py-1 rounded
                                   hover:bg-gray-200 dark:hover:bg-gray-700
                                   active:scale-95 transition">
                        <span x-text="asset"></span>
                    </button>
                </template>

            </div>
        </div>

        <!-- Add Item -->
        <button type="button"
                @click="items.push({ name: '', quantity: 1 })"
                class="text-sm text-blue-600 hover:underline
                       active:scale-95 transition-transform duration-100">
            + Add item
        </button>

    </div>

</div>

                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex gap-3 mb-2">

                                    <input type="text"
                                           :name="`items[${index}][name]`"
                                           x-model="item.name"
                                           class="w-full h-10 px-3 rounded-md dark:bg-gray-900"
                                           required>

                                    <input type="number"
                                           :name="`items[${index}][quantity]`"
                                           x-model="item.quantity"
                                           class="w-24 h-10 px-2 rounded-md dark:bg-gray-900"
                                           min="1"
                                           required>

                                    <button type="button"
                                            @click="items.splice(index,1)"
                                            class="text-red-500">
                                        ✕
                                    </button>
                                </div>
                            </template>

                        </div>

                    </div>

                    <!-- RIGHT -->
                    <div>

                        <div class="mb-4 flex gap-4">

                            <div class="w-full">
                                <x-input-label value="Start Date" />
                                <input type="date" name="start_date"
                                       value="{{ $request->start_date }}"
                                       class="w-full dark:bg-gray-900 rounded-md">
                            </div>

                            <div class="w-full">
                                <x-input-label value="End Date" />
                                <input type="date" name="end_date"
                                       value="{{ $request->end_date }}"
                                       class="w-full dark:bg-gray-900 rounded-md">
                            </div>

                        </div>

                        <div class="mb-4 flex gap-4">

                            <div class="w-full">
                                <x-input-label value="Setup Date" />
                                <input type="date" name="setup_date"
                                       value="{{ $request->setup_date }}"
                                       class="w-full dark:bg-gray-900 rounded-md">
                            </div>

                            <div class="w-full">
                                <x-input-label value="Setup Time" />
                                <input type="time" name="setup_time"
                                       value="{{ $request->setup_time }}"
                                       class="w-full dark:bg-gray-900 rounded-md">
                            </div>

                        </div>

                        <div class="mb-4">
                            <x-input-label value="Location" />
                            <x-text-input name="location"
                                          value="{{ $request->location }}"
                                          class="w-full" />
                        </div>

                        <div class="mb-6">
                            <x-input-label value="No. of Users" />
                            <x-text-input name="users"
                                          value="{{ $request->users }}"
                                          type="number"
                                          class="w-full" />
                        </div>

                        <div class="mb-4">
                            <x-input-label value="Notes" />
                            <textarea name="note"
                                      class="w-full dark:bg-gray-900 rounded-md">{{ $request->note }}</textarea>
                        </div>

                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="p-6 border-t dark:border-gray-700">
                <x-primary-button class="w-full bg-indigo-600 hover:bg-indigo-700">
                    Update Request
                </x-primary-button>
            </div>

        </form>
    </div>
</div>

<!-- CLOCK -->
<script>
function clock() {
    return {
        now: new Date(),
        start() { setInterval(() => this.now = new Date(), 1000); },
        formattedDate() {
            return this.now.toLocaleDateString();
        },
        formattedTime() {
            return this.now.toLocaleTimeString();
        }
    }
}
</script>
