<x-layout title="Admin Dashboard">
    <div class="flex items-center justify-center">
        <div class="grid grid-cols-2 gap-4 items-center justify-center">
            <div class="max-w-md w-full bg-white p-6 rounded-lg shadow-md text-center space-y-4 border border-gray-300 dark:border-gray-500">
                <div class="flex flex-col space-y-2 items-center">
                    <img src="{{ asset('icon/open-book-icon.svg') }}" alt="Open Book Icon" class="mr-2" />
                    <a href="{{ route('admin.books.index') }}" class="inline-flex items-center px-4 py-2 bg-[#f53003] text-white rounded hover:bg-red-600">Manage Books</a>
                </div>
            </div>
            <div class="max-w-md w-full bg-white p-6 rounded-lg shadow-md text-center space-y-4 border border-gray-300 dark:border-gray-500">
                <div class="flex flex-col space-y-2">
                    <img src="{{ asset('icon/library-building-icon.svg') }}" alt="Library Icon" class=" h-28 mr-2" />
                    <a href="{{ route('admin.libraries.index') }}" class="inline-flex items-center px-4 py-2 bg-[#f53003] text-white rounded hover:bg-red-600">
                        Manage Libraries
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layout>
