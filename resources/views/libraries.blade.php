<x-layout title="Libraries List">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-6 text-center">Libraries List</h1>
        @if($libraries->isEmpty())
            <p>No libraries found.</p>
            @else
            <ul class="list-disc pl-5 space-y-2">
                @foreach($libraries as $library)
                    <li class="flex items-center space-x-4">
                        @if($library->image)
                            <img src="{{ asset('storage/' . $library->image) }}" alt="Library Image" class="w-16 h-16 object-cover rounded">
                        @endif
                        <div>
                            <a href="{{ route('libraries.show', $library) }}" class="text-blue-600 hover:underline">
                                <strong>{{ $library->name }}</strong>
                            </a><br>
                            <small>Address: {{ $library->address }}</small>
                        </div>
                    </li>
                @endforeach
            </ul>
            @endif
    </div>
</x-layout>
