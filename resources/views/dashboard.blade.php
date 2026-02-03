
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Use the correct Alpine state -->
    <div class="p-6" x-data="{ openRequestForm: false }">

        <!-- Add Request Button -->
        <!-- old -->
        <!--
        
        @auth
        <button 
            @click="openRequestForm = true"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 mb-5">
            Add Request
        </button>
        @endauth
        
        -->

        @guest 
            @include('modal.not-auth')
        @endguest

        @auth
            @include('form.request-form')
        @endauth

        <div class="calendar mt-6">
            @include('layouts.calendar')
        </div>
    </div>
</x-app-layout>



