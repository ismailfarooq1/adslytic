<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 font-family-karla flex">

            @include('layouts.sidebar')
            <!-- Page Heading -->
            <div class="w-full flex flex-col h-screen overflow-y-hidden">
                @include("layouts.navigation")
                @include("layouts.mobile-nav")
            <!-- Page Content -->

            <div class="w-full overflow-x-hidden border-t flex flex-col">
                <main class="w-full flex-grow p-6">
                {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
