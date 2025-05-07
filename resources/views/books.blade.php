<x-layout title="Books List">
    <h1 class="text-2xl font-semibold mb-6 text-center">Books List</h1>
    <div class="container mx-auto grid grid-cols-2 gap-4 p-6 ">
        @if ($books->isEmpty())
            <p>No books found.</p>
        @endif
            @foreach($books as $book)
            <a href="{{ route('admin.books.show', $book) }}" class="flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow-sm md:flex-row hover:bg-gray-100">
                <img class="object-cover w-full h-96 md:h-auto md:w-48 p-4 rounded-lg md:rounded-s-lg" src="{{ asset('storage/' . $book->image) }}" alt="Cover Image">
                <div class="flex flex-col justify-between p-4 leading-normal">
                    <p class="font-bold text-gray-900">Title</p>
                    <p class="font-bold text-gray-900">Author</p>
                    <p class="font-bold text-gray-900">Publisher</p>
                    <p class="font-bold text-gray-900">Total Page</p>
                    <p class="font-bold text-gray-900">Published At</p>
                    <p class="font-bold text-gray-900">Description</p>
                </div>
                <div class="flex flex-col justify-between p-4 leading-normal">
                    <p class="font-normal">: {{ $book->title }}</p>
                    <p class="font-normal">: {{ $book->author }}</p>
                    <p class="font-normal">: {{ $book->publisher }}</p>
                    <p class="font-normal">: {{ $book->total_pages }} pages</p>
                    <p class="font-normal">: {{ $book->published_at->format('Y') }}</p>
                    <p class="font-normal">: {{ $book->description }}</p>
                </div>
            </a>
            @endforeach
    </div>
</x-layout>
