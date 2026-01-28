<!-- old -->
<!--
<div x-show="open" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
-->

<!-- new -->
<div x-show="openRequestForm" x-cloak
     class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
     <!-- old -->
     <!--
    <div class="form-container bg-white rounded-lg" @click.away="open = false"
    -->
    
    <!-- new -->
    <div class="form-container bg-white rounded-lg" @click.away="openRequestForm = false"
        
        >
        <div class="flex justify-end">
            <!-- old -->
            <!--
            <button @click="open = false" class="text-gray-500 hover:text-gray-700">
            -->
            
            <!-- new -->
            <button @click="openRequestForm = false" class="text-gray-500 hover:text-gray-700">

                <span class="material-symbols-outlined">close</span>
            </button>
            
            
        </div>

        <h1 class="form-header mb-5 text-center">Request Form</h1>

        <form action="{{ route('requests.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <x-input-label for="representative_name" :value="__('Name of Representative')" />
                <x-text-input id="representative_name" class="block mt-1 w-full" type="text" name="representative_name" required />
            </div>
            <div class="mb-4">
                <x-input-label for="event_name" :value="__('Name of Event')" />
                <x-text-input id="event_name" class="block mt-1 w-full" type="text" name="event_name" required />
            </div>
            <div class="mb-4">
                <x-input-label for="purpose" :value="__('Purpose of the Event')" />
                    <select id="purpose" name="purpose" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" onchange="toggleOtherInput()">
                        <option value=""></option>
                        <option value="Conference">Conference</option>
                        <option value="VideoCon">Video Con</option>
                        <option value="Training">Training</option>
                        <option value="Others">Others</option>
                    </select>
            </div>
            <div id="other_purpose" class="mb-4 hidden">
                <x-input-label for="other_purpose" :value="__('Specify Purpose')" />
                <input type="text" id="other_purpose" name="other_purpose" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
            </div>
            
            <div class="mb-4">
                <x-input-label for="item_name" :value="__('Item Name')" />
                <x-text-input id="item_name" class="block mt-1 w-full" type="text" name="item_name" required />
            </div>

            <div class="mb-4">
                <x-input-label for="item_number" :value="__('Item Number')" />
                <x-text-input 
                    id="item_number" 
                    class="block mt-1 w-full" 
                    type="number" 
                    name="item_number" 
                    min="1" 
                    value="1" 
                    required 
                />
            </div>

            <div class="mb-4 flex gap-4">
                <div class="w-full">
                    <x-input-label for="start_date" :value="__('Start Date')" />
                    <input id="start_date" type="date" name="start_date" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="w-full">
                    <x-input-label for="end_date" :value="__('End Date')" />
                    <input id="end_date" type="date" name="end_date" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <div class="mb-4 flex gap-4">
                <div class="w-full">
                    <x-input-label for="setup_date" :value="__('Set up Date')" />
                    <input id="setup_date" type="date" name="setup_date" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="w-full">
                    <x-input-label for="setup_time" :value="__('Set up Time')" />
                    <input id="setup_time" type="time" name="setup_time" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <div class="mb-4">
                <x-input-label for="location" :value="__('Location')" />
                <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" required />
            </div>

            <div class="mb-6">
                <x-input-label for="users" :value="__('No. of Users')" />
                <x-text-input id="users" class="block mt-1 w-full" type="number" name="users" required />
            </div>

            <div class="mb-4">
                <input type="checkbox" id="terms_agreement" name="terms_agreement" required onclick="toggleSubmitButton()">
                <label for="terms_agreement" class="text-sm text-gray-700">
                    I understand that any borrowed materials will be my responsibility, and any damages incurred will be shouldered by me.
                </label>
            </div>

            <div class="mt-4">
                <x-primary-button id="submit_button" class="w-full bg-gray-400 " disabled>
                    {{ __('Submit') }}
                </x-primary-button>
            </div>
          
        </form>
    </div>
</div>



<script>
    function toggleOtherInput() {
        const purposeSelect = document.getElementById('purpose');
        const otherInputDiv = document.getElementById('other_purpose');

        if (purposeSelect.value === 'Others') {
            otherInputDiv.classList.remove('hidden');
        } else {
            otherInputDiv.classList.add('hidden');
        }
    }

    function toggleSubmitButton() {
        const checkbox = document.getElementById('terms_agreement');
        const submitButton = document.getElementById('submit_button');

        if (checkbox.checked) {
            submitButton.style.backgroundColor = "#0575E6"; 
            submitButton.disabled = false;
        } else {
            submitButton.style.backgroundColor = "#969696"; 
            submitButton.disabled = true;
        }
    }
</script>
