<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <script src="//unpkg.com/alpinejs" defer></script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Icons -->
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
       
    </head>
    <body class="font-sans antialiased">
    <div class="h-screen bg-gray-100 dark:bg-gray-900 flex overflow-hidden">

        <!-- Side Bar Nav -->
        
        <div class="sidebar-layout bg-white dark:bg-gray-800">
    @include('layouts.sidebar-nav') {{-- single sidebar handles admin, user, guest --}}
        </div>

        
        <!-- Page Content -->
            <div class="flex-1 p-6 overflow-y-auto scroll-smooth">

            {{ $slot }}
        </div>
    </div>
</body>

<style>
    .sidebar-layout{
        margin-right: 5.5rem;
        transition: 0.4s;
    }

    .sidebar-layout:hover{
        margin-right: 16rem;
    }


</style>

</html>

