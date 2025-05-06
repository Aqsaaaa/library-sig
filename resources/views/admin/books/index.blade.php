<x-layout title="Manage Books">
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Manage Books</h1>
            <a href="{{ route('admin.books.create') }}" class="px-4 py-2 bg-[#f53003] text-white rounded hover:bg-red-600">Add New Book</a>
        </div>
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        <table class="min-w-full bg-white border border-gray-200 rounded">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 border-b">Title</th>
                    <th class="py-2 px-4 border-b">Author</th>
                    <th class="py-2 px-4 border-b">Published At</th>
                    <th class="py-2 px-4 border-b">Library</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach($books as $book)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $book->title }}</td>
                    <td class="py-2 px-4 border-b">{{ $book->author }}</td>
                    <td class="py-2 px-4 border-b">{{ $book->published_at ? $book->published_at->format('Y-m-d') : '-' }}</td>
                    <td class="py-2 px-4 border-b">
                        @if($book->libraries->isNotEmpty())
                            {{ $book->libraries->pluck('name')->join(', ') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="py-2 px-4 border-b space-x-2">
                        <a href="{{ route('admin.books.edit', $book) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this book?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if($books->isEmpty())
                <tr>
                    <td colspan="5" class="text-center py-4">No books found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</x-layout>
