<x-layout title="Manage Libraries">
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Manage Libraries</h1>
            <a href="{{ route('admin.libraries.create') }}" class="px-4 py-2 bg-[#f53003] text-white rounded hover:bg-red-600">Add New Library</a>
        </div>
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        <table class="min-w-full bg-white border border-gray-200 rounded">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 border-b">Image</th>
                    <th class="py-2 px-4 border-b">Name</th>
                    <th class="py-2 px-4 border-b">Address</th>
                    <th class="py-2 px-4 border-b">LatLong</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach($libraries as $library)
                <tr>
                    <td class="py-2 px-4 border-b">
                        @if($library->image)
                            <img src="{{ asset('storage/' . $library->image) }}" alt="Library Image" class="mx-auto max-h-16 rounded">
                        @else
                            -
                        @endif
                    </td>
                    <td class="py-2 px-4 border-b">{{ $library->name }}</td>
                    <td class="py-2 px-4 border-b">{{ $library->address ?? '-' }}</td>
                    <td class="py-2 px-4 border-b">{{ $library->latitude . ', ' . $library->longitude ?? '-' }}</td>
                    <td class="py-2 px-4 border-b space-x-2">
                        <a href="{{ route('admin.libraries.edit', $library) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('admin.libraries.destroy', $library) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this library?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if($libraries->isEmpty())
                <tr>
                    <td colspan="4" class="text-center py-4">No libraries found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</x-layout>
