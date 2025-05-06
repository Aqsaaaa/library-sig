<x-layout title="Edit Book">
    <div class="container mx-auto p-6 max-w-lg">
        <h1 class="text-2xl font-semibold mb-6">Edit Book</h1>
        <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="title" class="block mb-1 font-medium">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $book->title) }}" required class="w-full border border-gray-300 rounded p-2" />
                @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="author" class="block mb-1 font-medium">Author</label>
                <input type="text" name="author" id="author" value="{{ old('author', $book->author) }}" required class="w-full border border-gray-300 rounded p-2" />
                @error('author')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="publisher" class="block mb-1 font-medium">Publisher</label>
                <input type="text" name="publisher" id="publisher" value="{{ old('publisher', $book->publisher) }}" class="w-full border border-gray-300 rounded p-2" />
                @error('publisher')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="description" class="block mb-1 font-medium">Description</label>
                <textarea name="description" id="description" rows="4" class="w-full border border-gray-300 rounded p-2">{{ old('description', $book->description) }}</textarea>
                @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="published_at" class="block mb-1 font-medium">Published Date</label>
                <input type="date" name="published_at" id="published_at" value="{{ old('published_at', $book->published_at ? $book->published_at->format('Y-m-d') : '') }}" class="w-full border border-gray-300 rounded p-2" />
                @error('published_at')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="total_pages" class="block mb-1 font-medium">Total Pages</label>
                <input type="number" name="total_pages" id="total_pages" value="{{ old('total_pages', $book->total_pages) }}" min="1" class="w-full border border-gray-300 rounded p-2" />
                @error('total_pages')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="image" class="block mb-1 font-medium">Book Image</label>
                @if($book->image)
                    <img src="{{ asset('storage/' . $book->image) }}" alt="Book Image" class="mb-2 max-h-40">
                @endif
                <input type="file" name="image" id="image" accept="image/*" class="w-full border border-gray-300 rounded p-2" />
                @error('image')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.books.index') }}" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-[#f53003] text-white rounded hover:bg-red-600">Update Book</button>
            </div>
        </form>
    </div>
</x-layout>
