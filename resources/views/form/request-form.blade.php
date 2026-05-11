<div x-show="openRequestForm"
     x-cloak
     class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center px-4">

    <div class="bg-white dark:bg-gray-800 dark:text-gray-200
                rounded-2xl w-full max-w-5xl
                max-h-[90vh] flex flex-col shadow-xl">

<div x-data="clock()" x-init="start()"
     class="flex justify-between items-center px-6 pt-6 pb-2 border-b dark:border-gray-700">

    <!-- LEFT -->
    <h1 class="text-lg font-semibold">Request Form</h1>

    <!-- RIGHT -->
    <div class="flex items-center gap-4">

        <!-- DATE & TIME -->
        <div class="text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
            <span x-text="formattedDate"></span>
            <span class="mx-1">•</span>
            <span x-text="formattedTime"></span>
        </div>

        <!-- CLOSE BUTTON -->
        <button @click="openRequestForm = false"
                class="text-gray-500 hover:text-gray-700 dark:hover:text-white">
            <span class="material-symbols-outlined">close</span>
        </button>

    </div>
</div>

        <!-- FORM -->
        <form action="{{ route('requests.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
            @csrf

            <!-- SCROLLABLE BODY -->
            <div class="overflow-y-auto px-6 py-4">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <!-- LEFT SIDE -->
                    <div>

                        <div class="mb-4">
                            <x-input-label for="representative_name" value="Name of Representative" />
                            <x-text-input id="representative_name" class="block mt-1 w-full" type="text" name="representative_name" required />
                        </div>

<div class="mb-4 relative"
     x-data="{
        showPersonnel: false,
        personnelList: @js($personnel ?? []),
        selected: ''
     }">

    <!-- LABEL ROW (LABEL + BUTTON) -->
    <div class="flex items-center justify-between mb-1">

        <x-input-label for="requested_employee" value="Requested Employee (Optional)" />

        <!-- BUTTON MOVED OUTSIDE INPUT -->
        <button type="button"
                @click="showPersonnel = !showPersonnel"
                class="text-xs text-indigo-600 hover:underline">
            Browse NOCS Personnel
        </button>

    </div>

    <!-- INPUT -->
    <x-text-input id="requested_employee"
                  name="requested_employee"
                  x-model="selected"
                  class="block mt-1 w-full"
                  placeholder="Enter employee name (if any)" />

    <!-- DROPDOWN -->
    <div x-show="showPersonnel"
         x-transition
         @click.outside="showPersonnel = false"
         class="absolute z-20 mt-2 w-full bg-white dark:bg-gray-900
                border rounded-lg shadow-lg max-h-44 overflow-y-auto">

        <template x-if="personnelList.length === 0">
            <div class="p-3 text-sm text-gray-500">
                No personnel available
            </div>
        </template>

        <template x-for="(p, i) in personnelList" :key="p.id">
            <button type="button"
                    @click="
                        selected = p.name;
                        showPersonnel = false;
                    "
                    class="w-full text-left px-3 py-2 text-sm
                           hover:bg-gray-100 dark:hover:bg-gray-700">
                <div class="font-medium" x-text="p.name"></div>
                <div class="text-xs text-gray-400" x-text="p.office ?? '-'"></div>
            </button>
        </template>

    </div>
</div>

                        <div class="mb-4">
                            <x-input-label for="event_name" value="Name of Event" />
                            <x-text-input id="event_name" class="block mt-1 w-full" type="text" name="event_name" required />
                        </div>

<div x-data="{ purpose: '' }" class="mb-6">
    <x-input-label for="event_name" value="Purpose" />
    <select id="purpose"
            name="purpose"
            x-model="purpose"
            class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200">

        <option value="Conference">Conference</option>
        <option value="VideoCon">Video Con</option>
        <option value="Training">Training</option>
        <option value="Others">Others</option>
    </select>

    <div x-show="purpose === 'Others'" x-transition class="mt-3 mb-6">
        <x-input-label for="other_purpose" value="Specify Purpose" />
        <input type="text" name="other_purpose"
               class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200">
    </div>

</div>


                        <!-- ITEMS -->
                        <div x-data="{
                                maxItems: 20,
                                items: [],
                                showAssets: false,
                                assets: @js($assets ?? []),

                                init() {
                                    console.log('REQUEST FORM LOADED');
                                    console.log('assets:', this.assets);
                                }
                            }"
                            class="mb-4">


<div class="flex items-center justify-between mb-2">

    <x-input-label for="items" value="Requested Items" />

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

            <!-- ASSET LIST (UNCHANGED) -->
            <div x-show="showAssets"
                 x-transition
                 class="absolute z-10 mt-2 w-64 p-3 border rounded-lg
                        bg-gray-50 dark:bg-gray-900 max-h-40 overflow-auto shadow-lg">

                <template x-if="assets.length === 0">
                    <p class="text-sm text-gray-500">No items available</p>
                </template>

                <template x-for="(asset, index) in assets" :key="asset + '-' + index">
                    <button type="button"
                            @click="items.push({ name: asset, quantity: 1, id: Date.now() }); showAssets = false"
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
                @click="if (items.length < maxItems) items.push({ name: '', quantity: 1, id: Date.now() })"
                class="text-sm text-blue-600 hover:underline
                       active:scale-95 transition-transform duration-100">
            + Add item
        </button>

    </div>

</div>


                            <!-- ITEMS CONTAINER -->
                            <div class="max-h-52 overflow-y-auto pr-2 border rounded-lg p-2 bg-gray-50 dark:bg-gray-900">

                                <template x-for="(item, index) in items" :key="item.id">
                                    <div class="flex gap-3 mb-2 items-center bg-white dark:bg-gray-800 p-2 rounded-lg shadow-sm">

                                        <input type="text"
                                               :name="`items[${index}][name]`"
                                               x-model="item.name"
                                               class="w-full h-10 px-3 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200"
                                               required>

                                        <input type="number"
                                               :name="`items[${index}][quantity]`"
                                               x-model="item.quantity"
                                               min="1"
                                               class="w-24 h-10 px-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200"
                                               required>

                                        <button type="button"
                                                x-show="items.length > 1"
                                                @click="items.splice(index, 1)"
                                                class="text-red-500 hover:text-red-700">
                                            ✕
                                        </button>
                                    </div>
                                </template>



                            </div>
                        </div>
                    </div>

                    <!-- RIGHT SIDE -->
                    <div>

                        <div class="mb-4 flex gap-4">
                            <div class="w-full">
                                <x-input-label for="start_date" value="Start Date" />
                                <input type="date" name="start_date" required
                                       class="block mt-1 w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-gray-200">
                            </div>

                            <div class="w-full">
                                <x-input-label for="end_date" value="End Date" />
                                <input type="date" name="end_date" required
                                       class="block mt-1 w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-gray-200">
                            </div>
                        </div>

                        <div class="mb-4 flex gap-4">
                            <div class="w-full">
                                <x-input-label for="setup_date" value="Set up Date" />
                                <input type="date" name="setup_date" required
                                       class="block mt-1 w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-gray-200">
                            </div>

                            <div class="w-full">
                                <x-input-label for="setup_time" value="Set up Time" />
                                <input type="time" name="setup_time"
                                       class="block mt-1 w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-gray-200">
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="location" value="Location" />
                            <x-text-input name="location" class="block mt-1 w-full" type="text" required />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="users" value="No. of Users" />
                            <x-text-input name="users" class="block mt-1 w-full" type="number" required />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="note" value="Notes (Optional)" />
                            <textarea name="note"
                                      rows="3"
                                      class="block mt-1 w-full rounded-md dark:bg-gray-900 dark:text-gray-200"></textarea>
                        </div>

                    </div>
                </div>

                <!-- TERMS -->
                <div x-data="{ openTerms: false }" class="mt-4">
                    <div class="flex items-start gap-2">
                        <input type="checkbox"
                               id="terms_agreement"
                               name="terms_agreement"
                               required
                               onclick="toggleSubmitButton()"
                               class="mt-1">

                        <label class="text-sm">
                            I have read and agree to the
                            <span @click="openTerms = true"
                                  class="text-blue-600 underline cursor-pointer">
                                terms and conditions
                            </span>.
                        </label>
                    </div>

                    <!-- TERMS MODAL -->
                    <div x-show="openTerms" x-cloak
                         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center px-4">

                        <div class="bg-white dark:bg-gray-700 rounded-lg w-full max-w-2xl p-6 shadow-lg"
                             @click.away="openTerms = false">

                            <h2 class="text-xl font-semibold mb-4">
                                Terms and Conditions & Liability
                            </h2>

                            <div class="h-64 overflow-y-auto mb-4 text-sm border p-3 rounded">
                                <p><strong>Liability:</strong> Any borrowed items will be your responsibility...</p>
                                <p><strong>Usage:</strong> Borrowed items must be returned...</p>
                                <p><strong>Compliance:</strong> By agreeing, you acknowledge...</p>
                            </div>

                            <div class="flex justify-end">
                                <button @click="openTerms = false"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                                    I Accept
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="p-6 border-t dark:border-gray-700">
                <x-primary-button id="submit_button" class="w-full bg-gray-400 text-white" disabled>
                    Submit
                </x-primary-button>
            </div>

        </form>
    </div>
</div>

<script>
function toggleOtherInput() {
    const purpose = document.getElementById('purpose');
    const other = document.getElementById('other_purpose');
    other.classList.toggle('hidden', purpose.value !== 'Others');
}

function toggleSubmitButton() {
    const cb = document.getElementById('terms_agreement');
    const btn = document.getElementById('submit_button');

    btn.disabled = !cb.checked;
    btn.classList.toggle('bg-indigo-600', cb.checked);
    btn.classList.toggle('bg-gray-400', !cb.checked);
}
</script>

<script>
function clock() {
    return {
        now: new Date(),

        start() {
            this.update();
            setInterval(() => this.update(), 1000);
        },

        update() {
            this.now = new Date();
        },

        get formattedDate() {
            return this.now.toLocaleDateString(undefined, {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },

        get formattedTime() {
            return this.now.toLocaleTimeString(undefined, {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
    }
}
</script>
