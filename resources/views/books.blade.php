<x-layout title="Books List">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-6">Books List</h1>
        @if($books->isEmpty())
            <p>No books found.</p>
        @else
            <ul class="list-disc pl-5 space-y-2">
                @foreach($books as $book)
                    <li>
                        <strong>{{ $book->title }}</strong> by {{ $book->author }}
                        @if($book->libraries->isNotEmpty())
                            <br><small>Libraries: {{ $book->libraries->pluck('name')->join(', ') }}</small>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-layout>
