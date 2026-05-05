@if ($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
        <ul>
            @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="w-full">

    <!-- MODAL HEADER (UPGRADED LIKE REQUEST FORM) -->
    <div x-data="clock()" x-init="start()"
         class="flex justify-between items-center px-6 pt-6 pb-2 border-b dark:border-gray-700">

        <!-- LEFT -->
        <h1 class="text-lg font-semibold">Edit Request Form</h1>

        <!-- RIGHT -->
        <div class="flex items-center gap-4">

            <!-- DATE & TIME -->
            <div class="text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                <span x-text="formattedDate"></span>
                <span class="mx-1">•</span>
                <span x-text="formattedTime"></span>
            </div>

            <!-- NOTE: no modal close here unless you actually use x-show modal wrapper -->
        </div>
    </div>

    <!-- FORM -->
    <form action="{{ route('requests.update', $request->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 px-6 py-4">

            <!-- LEFT SIDE -->
            <div>

                <div class="mb-4">
                    <x-input-label for="representative_name" value="Name of Representative" />
                    <x-text-input id="representative_name"
                                  class="block mt-1 w-full"
                                  type="text"
                                  name="representative_name"
                                  value="{{ $request->representative_name }}"
                                  required />
                </div>

                <div class="mb-4">
                    <x-input-label for="requested_employee" value="Requested Employee (Optional)" />
                    <x-text-input id="requested_employee"
                                  class="block mt-1 w-full"
                                  type="text"
                                  name="requested_employee"
                                  value="{{ $request->requested_employee }}" />
                </div>

                <div class="mb-4">
                    <x-input-label for="event_name" value="Name of Event" />
                    <x-text-input id="event_name"
                                  class="block mt-1 w-full"
                                  type="text"
                                  name="event_name"
                                  value="{{ $request->event_name }}"
                                  required />
                </div>

                <!-- PURPOSE (FIXED) -->
                <div x-data="{ purpose: '{{ $request->purpose }}' }" class="mb-6">

                    <x-input-label value="Purpose" />

                    <select name="purpose"
                            x-model="purpose"
                            class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200">

                        <option value="Conference">Conference</option>
                        <option value="VideoCon">Video Con</option>
                        <option value="Training">Training</option>
                        <option value="Others">Others</option>
                    </select>

                    <div x-show="purpose === 'Others'" x-transition class="mt-3">
                        <x-input-label for="other_purpose" value="Specify Purpose" />
                        <input type="text"
                               name="other_purpose"
                               value="{{ $request->other_purpose }}"
                               class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200">
                    </div>

                </div>

                <!-- ITEMS -->
                <div x-data="{
                    maxItems: 20,
                    items: @js(is_array($request->items)
                        ? $request->items
                        : json_decode($request->items ?? '[]', true)),
                    assets: @js($assets ?? [])
                }">

                    <x-input-label value="Requested Items" class="mb-2" />

                    <!-- ADD BUTTON -->
                    <button type="button"
                            @click="if(items.length < maxItems) items.push({name:'',quantity:1})"
                            class="text-sm text-blue-600 hover:underline mb-3">
                        + Add item
                    </button>

                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex gap-3 mb-3 items-center">

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

            <!-- RIGHT SIDE -->
            <div>

                <div class="mb-4 flex gap-4">
                    <div class="w-full">
                        <x-input-label value="Start Date" />
                        <input type="date"
                               name="start_date"
                               value="{{ \Carbon\Carbon::parse($request->start_date)->format('Y-m-d') }}"
                               class="block mt-1 w-full rounded-md dark:bg-gray-900 dark:text-gray-200">
                    </div>

                    <div class="w-full">
                        <x-input-label value="End Date" />
                        <input type="date"
                               name="end_date"
                               value="{{ \Carbon\Carbon::parse($request->end_date)->format('Y-m-d') }}"
                               class="block mt-1 w-full rounded-md dark:bg-gray-900 dark:text-gray-200">
                    </div>
                </div>

                <div class="mb-4 flex gap-4">
                    <div class="w-full">
                        <x-input-label value="Set up Date" />
                        <input type="date"
                               name="setup_date"
                               value="{{ $request->setup_date ? \Carbon\Carbon::parse($request->setup_date)->format('Y-m-d') : '' }}"
                               class="block mt-1 w-full rounded-md dark:bg-gray-900 dark:text-gray-200">
                    </div>

                    <div class="w-full">
                        <x-input-label value="Set up Time" />
                        <input type="time"
                               name="setup_time"
                               value="{{ $request->setup_time }}"
                               class="block mt-1 w-full rounded-md dark:bg-gray-900 dark:text-gray-200">
                    </div>
                </div>

                <div class="mb-4">
                    <x-input-label value="Location" />
                    <x-text-input name="location"
                                  class="block mt-1 w-full"
                                  type="text"
                                  value="{{ $request->location }}"
                                  required />
                </div>

                <div class="mb-6">
                    <x-input-label value="No. of Users" />
                    <x-text-input name="users"
                                  class="block mt-1 w-full"
                                  type="number"
                                  value="{{ $request->users }}"
                                  required />
                </div>

                <div class="mb-4">
                    <x-input-label value="Notes (Optional)" />
                    <textarea name="note"
                              rows="3"
                              class="block mt-1 w-full rounded-md dark:bg-gray-900 dark:text-gray-200">{{ $request->note }}</textarea>
                </div>

            </div>
        </div>

        <!-- SUBMIT -->
        <div class="px-6 pb-6">
            <x-primary-button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white">
                Update Request
            </x-primary-button>
        </div>

    </form>
</div>

<!-- CLOCK SCRIPT (same as request form) -->
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
