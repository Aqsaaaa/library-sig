<x-layout title="Add Book to Library">
    <div class="mx-auto bg-white p-6 rounded-lg shadow-md border border-gray-300 dark:border-gray-500">
        <h2 class="text-xl font-semibold mb-4">Add Book to Library</h2>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.libraries.addBook') }}" method="POST" id="addBookForm">
            @csrf

            <div class="mb-4">
                <label for="library_id" class="block text-gray-700 font-medium mb-2">Select Library</label>
                <select name="library_id" id="library_id" required class="w-full border border-gray-300 rounded px-3 py-2" onchange="onLibraryChange()">
                    <option value="" disabled {{ empty($selectedLibraryId) ? 'selected' : '' }}>Choose a library</option>
                    @foreach($libraries as $library)
                        <option value="{{ $library->id }}" {{ (isset($selectedLibraryId) && $selectedLibraryId == $library->id) ? 'selected' : '' }}>{{ $library->name }}</option>
                    @endforeach
                </select>
                @error('library_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold mb-2">Books Already Added</h3>
                    <div id="addedBooks" class="max-h-80 overflow-y-auto border border-gray-300 rounded p-2">
                        @if(isset($addedBooks) && $addedBooks->count() > 0)
                            @foreach($addedBooks as $book)
                                <div class="flex items-center mb-2">
                                    <input type="checkbox" name="book_id[]" value="{{ $book->id }}" id="book_{{ $book->id }}" class="mr-2" checked onchange="moveBook(this)">
                                    <label for="book_{{ $book->id }}" class="text-gray-800">{{ $book->title }} by {{ $book->author }}</label>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500">No books added yet.</p>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold mb-2">Available Books</h3>
                    <div id="availableBooks" class="max-h-80 overflow-y-auto border border-gray-300 rounded p-2">
                        @if(isset($availableBooks) && $availableBooks->count() > 0)
                            @foreach($availableBooks as $book)
                                <div class="flex items-center mb-2">
                                    <input type="checkbox" value="{{ $book->id }}" id="book_{{ $book->id }}" class="mr-2" onchange="moveBook(this)">
                                    <label for="book_{{ $book->id }}" class="text-gray-800">{{ $book->title }} by {{ $book->author }}</label>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500">No available books.</p>
                        @endif
                    </div>
                </div>
            </div>

            <button type="submit" class="mt-4 px-4 py-2 bg-[#f53003] text-white rounded hover:bg-red-600">Save Changes</button>
        </form>
    </div>

    <script>
        function onLibraryChange() {
            const libraryId = document.getElementById('library_id').value;
            window.location.href = "{{ route('admin.libraries.addBookForm') }}" + "?library_id=" + libraryId;
        }

        function moveBook(checkbox) {
            const bookDiv = checkbox.parentElement;
            const addedBooksDiv = document.getElementById('addedBooks');
            const availableBooksDiv = document.getElementById('availableBooks');

            if (checkbox.checked) {
                // Move to addedBooks
                addedBooksDiv.appendChild(bookDiv);
                checkbox.setAttribute('name', 'book_id[]');
            } else {
                // Move to availableBooks
                availableBooksDiv.appendChild(bookDiv);
                checkbox.removeAttribute('name');
            }
        }
    </script>
</x-layout>
