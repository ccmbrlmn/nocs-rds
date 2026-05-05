<x-app-layout>

    <div class="p-6 max-w-5xl mx-auto">

        @include('form.edit-request-form', [
            'request' => $request
        ])

    </div>

</x-app-layout>
