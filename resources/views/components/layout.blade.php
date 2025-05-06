<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Laravel' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-[#1b1b18] min-h-screen flex flex-col">
    <x-navbar />
    <main class="flex-grow container mx-auto p-6 lg:p-8">
        {{ $slot }}
    </main>
    <footer class="bg-gray-100 text-center p-4 text-sm text-gray-600">
        &copy; {{ date('Y') }} Abiyu Fawaz Ramadhan. All rights reserved.
    </footer>
</body>
</html>
