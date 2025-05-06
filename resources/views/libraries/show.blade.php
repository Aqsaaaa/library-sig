<x-layout :title="'Library Details - ' . $library->name">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-4">{{ $library->name }}</h1>
        <p class="mb-2"><strong>Address:</strong> {{ $library->address }}</p>
        <div id="map" style="height: 400px; width: 100%; border: 1px solid #ccc;" class="mb-4"></div>
        <p>
            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $library->latitude }},{{ $library->longitude }}" target="_blank" class="text-blue-600 hover:underline">
                Get Directions (GPS)
            </a>
        </p>
        <p id="distance" class="mt-2"></p>
    </div>

    @include('libraries.show-scripts')
</x-layout>
