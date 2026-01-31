<div x-show="openEdit"
     x-cloak
     class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center px-4">

    <div class="bg-white rounded-lg w-full max-w-5xl p-6"
         @click.away="openEdit = false">

        <div class="flex justify-end">
            <button @click="openEdit = false" class="text-gray-500 hover:text-gray-700">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <h1 class="form-header mb-5 text-center">Edit Request</h1>

        <form action="{{ route('requests.update', $request->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- LEFT COLUMN -->
            <div>
                <div class="mb-4">
                    <x-input-label for="representative_name" value="Name of Representative" />
                    <x-text-input id="representative_name" class="block mt-1 w-full" type="text" name="representative_name" value="{{ $request->representative_name }}" required />
                </div>

                <div class="mb-4">
                    <x-input-label for="event_name" value="Name of Event" />
                    <x-text-input id="event_name" class="block mt-1 w-full" type="text" name="event_name" value="{{ $request->event_name }}" required />
                </div>

                <div class="mb-4">
                    <x-input-label for="purpose" value="Purpose of the Event" />
                    <select id="purpose" name="purpose"
                            class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
                            onchange="toggleOtherInputEdit()">
                        <option value=""></option>
                        <option value="Conference" {{ $request->purpose === 'Conference' ? 'selected' : '' }}>Conference</option>
                        <option value="VideoCon" {{ $request->purpose === 'VideoCon' ? 'selected' : '' }}>Video Con</option>
                        <option value="Training" {{ $request->purpose === 'Training' ? 'selected' : '' }}>Training</option>
                        <option value="Others" {{ $request->purpose === 'Others' ? 'selected' : '' }}>Others</option>
                    </select>
                </div>

                <div id="other_purpose_edit" class="mb-4 {{ $request->purpose === 'Others' ? '' : 'hidden' }}">
                    <x-input-label for="other_purpose" value="Specify Purpose" />
                    <input type="text" id="other_purpose_edit" name="other_purpose"
                           value="{{ $request->other_purpose }}"
                           class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                </div>
                
                <div x-data="{
                        maxItems: 5,
                        items: @json(json_decode($request->items, true))
                    }"
                    class="mb-4">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Requested Items
                    </label>
                    
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex gap-3 mb-3 items-center">

                            <!-- Item name -->
                            <input type="text"
                                   :name="`items[${index}][name]`"
                                   x-model="item.name"
                                   placeholder="Item name"
                                   class="w-full h-10 px-3 rounded-md border-gray-300 shadow-sm"
                                   required>

                            <!-- Quantity -->
                            <input type="number"
                                   :name="`items[${index}][quantity]`"
                                   x-model="item.quantity"
                                   min="1"
                                   class="w-24 h-10 px-2 rounded-md border-gray-300 shadow-sm"
                                   required>

                            <!-- Remove -->
                            <button type="button"
                                    x-show="items.length > 1"
                                    @click="items.splice(index, 1)"
                                    class="flex items-center justify-center w-8 h-8 text-red-500 hover:text-red-700">
                                ✕
                            </button>
                        </div>
                    </template>

                    <!-- Add item button -->
                    <button type="button"
                            @click="if (items.length < maxItems) items.push({ name: '', quantity: 1 })"
                            x-show="items.length < maxItems"
                            class="text-sm text-blue-600 hover:underline mt-2">
                        + Add item
                    </button>

                    <p x-show="items.length >= maxItems"
                       class="text-xs text-red-500 mt-1">
                        Maximum of 5 items only.
                    </p>


                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div>
                <div class="mb-4 flex gap-4">
                    <div class="w-full">
                        <x-input-label for="start_date" value="Start Date" />
                        <input id="start_date" type="date" name="start_date"
                               value="{{ $request->start_date }}"
                               class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="w-full">
                        <x-input-label for="end_date" value="End Date" />
                        <input id="end_date" type="date" name="end_date"
                               value="{{ $request->end_date }}"
                               class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>

                <div class="mb-4 flex gap-4">
                    <div class="w-full">
                        <x-input-label for="setup_date" value="Set up Date" />
                        <input id="setup_date" type="date" name="setup_date"
                               value="{{ $request->setup_date }}"
                               class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="w-full">
                        <x-input-label for="setup_time" value="Set up Time" />
                        <input id="setup_time" type="time" name="setup_time"
                               value="{{ $request->setup_time }}"
                               class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>

                <div class="mb-4">
                    <x-input-label for="location" value="Location" />
                    <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" value="{{ $request->location }}" required />
                </div>

                <div class="mb-6">
                    <x-input-label for="users" value="No. of Users" />
                    <x-text-input id="users" class="block mt-1 w-full" type="number" name="users" value="{{ $request->users }}" required />
                </div>
            </div>
            </div>

            <div class="mb-4 flex items-start gap-2">
                <input type="checkbox"
                       id="terms_agreement_edit"
                       name="terms_agreement"
                       required
                       onclick="toggleSubmitButtonEdit()"
                       class="mt-1">

                <label for="terms_agreement_edit" class="text-sm text-gray-700">
                    I understand that any borrowed materials will be my responsibility, and any damages incurred will be shouldered by me.
                </label>
            </div>

            <div class="mt-4">
                <x-primary-button id="submit_button_edit" class="w-full bg-gray-400" disabled>
                    {{ __('Save Changes') }}
                </x-primary-button>
            </div>
          
        </form>
    </div>
</div>

<script>
    function toggleOtherInputEdit() {
        const purposeSelect = document.getElementById('purpose');
        const otherInputDiv = document.getElementById('other_purpose_edit');

        if (purposeSelect.value === 'Others') {
            otherInputDiv.classList.remove('hidden');
        } else {
            otherInputDiv.classList.add('hidden');
        }
    }

    function toggleSubmitButtonEdit() {
        const checkbox = document.getElementById('terms_agreement_edit');
        const submitButton = document.getElementById('submit_button_edit');

        if (checkbox.checked) {
            submitButton.classList.remove('bg-gray-400');
            submitButton.classList.add('bg-blue-600');
            submitButton.disabled = false;
        } else {
            submitButton.classList.add('bg-gray-400');
            submitButton.classList.remove('bg-blue-600');
            submitButton.disabled = true;
        }
    }
</script>
