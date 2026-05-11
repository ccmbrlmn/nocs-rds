<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="p-6" x-data="{ openRequestForm: false }">

        @guest
            @include('modal.not-auth')
        @endguest

        @auth
            @include('form.request-form', [
                'personnel' => $personnel,
                'assets' => $assets
            ])
        @endauth

        <div class="calendar mt-6">
            @include('layouts.calendar')
        </div>
    </div>
</x-app-layout>



