<x-layout title="Book Details">
    <div class="container max-w-3xl p-4 rounded-lg bg-white border border-black mx-auto">
    <h1 class="text-2xl font-semibold mb-4 text-center">Book Details</h1>
        <div class="mb-6 flex space-x-6 mt-6 justify-center">
            @if($book->image)
            <img class="object-cover w-full h-96 md:h-auto md:w-48 p-4 rounded-lg md:rounded-s-lg" src="{{ asset('storage/' . $book->image) }}" alt="Cover Image">
            @endif
            <div class="grid items-center">
                <p>Title :</p>
                <strong><small>{{ $book->title }}</small></strong>
                <p>Author :</p>
                <strong><small>{{ $book->author }}</small></strong>
                <p>Publisher :</p>
                <strong><small>{{ $book->publisher }}</small></strong>
                <p>Published Date :</p>
                <strong><small>{{ $book->published_at ? $book->published_at->format('j F, Y') : '-' }}</small></strong>
                <p>Total Pages :</p>
                <strong><small>{{ $book->total_pages }} Pages</small></strong>
            </div>
            <div class="mb-6 mx-20">
                <h2 class="text-xl font-semibold mb-2">Description :</h2>
                <p class="whitespace-pre-line">{{ $book->description }}</p>
            </div>
        </div>
        <hr>
        <div class="mb-6 flex flex-col mt-6 items-center justify-center">
            <div>
                <h2 class="text-xl font-semibold mb-2">Available in Libraries</h2>
                @if($book->libraries->isEmpty())
                    <p>This book is not currently available in any library.</p>
                @else
                    <ul class="list-disc list-inside">
                        @foreach($book->libraries as $library)
                            <li>
                                <a href="{{ route('libraries.show', $library) }}">
                                    {{ $library->name }} - {{ $library->address ?? '' }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <a href="{{ route('books') }}" class="inline-block px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Back to Books</a>
        </div>
    </div>
</x-layout>
