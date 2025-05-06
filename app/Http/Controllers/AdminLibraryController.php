<?php

namespace App\Http\Controllers;

use App\Models\Library;
use Illuminate\Http\Request;

class AdminLibraryController extends Controller
{
    public function index()
    {
        $libraries = Library::all();
        return view('admin.libraries.index', compact('libraries'));
    }

    public function create()
    {
        return view('admin.libraries.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'latlong' => 'nullable|string|max:100',
        ]);

        // Parse latlong into latitude and longitude
        $latitude = null;
        $longitude = null;
        if (!empty($validated['latlong'])) {
            $latlongParts = explode(',', $validated['latlong']);
            if (count($latlongParts) === 2) {
                $latitude = floatval(trim($latlongParts[0]));
                $longitude = floatval(trim($latlongParts[1]));
            }
        }

        $data = [
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        Library::create($data);

        return redirect()->route('admin.libraries.index')->with('success', 'Lokasi perpustakaan berhasil ditambahkan.');
    }

    public function edit(Library $library)
    {
        return view('admin.libraries.edit', compact('library'));
    }

    public function update(Request $request, Library $library)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'latlong' => 'nullable|string|max:100',
        ]);

        // Parse latlong into latitude and longitude
        $latitude = null;
        $longitude = null;
        if (!empty($validated['latlong'])) {
            $latlongParts = explode(',', $validated['latlong']);
            if (count($latlongParts) === 2) {
                $latitude = floatval(trim($latlongParts[0]));
                $longitude = floatval(trim($latlongParts[1]));
            }
        }

        $data = [
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        $library->update($data);

        return redirect()->route('admin.libraries.index')->with('success', 'Lokasi perpustakaan diperbarui.');
    }

    public function destroy(Library $library)
    {
        $library->delete();
        return redirect()->route('admin.libraries.index')->with('success', 'Lokasi perpustakaan dihapus.');
    }

    public function addBook(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|array',
            'book_id.*' => 'exists:books,id',
            'library_id' => 'required|exists:libraries,id',
        ]);

        $bookIds = $validated['book_id'];
        $libraryId = $validated['library_id'];

        $library = Library::findOrFail($libraryId);

        // Attach the books to the library, ignoring duplicates
        $library->books()->syncWithoutDetaching($bookIds);

        return redirect()->route('admin.dashboard')->with('success', 'Buku berhasil ditambahkan ke perpustakaan.');
    }

    public function showAddBookForm(Request $request)
    {
        $libraries = Library::all();

        $selectedLibraryId = $request->query('library_id');
        $addedBooks = collect();
        $availableBooks = \App\Models\Book::all();

        if ($selectedLibraryId) {
            $library = Library::find($selectedLibraryId);
            if ($library) {
                $addedBooks = $library->books;
                $availableBooks = \App\Models\Book::whereNotIn('id', $addedBooks->pluck('id'))->get();
            }
        }

        return view('admin.libraries.add-book', compact('libraries', 'addedBooks', 'availableBooks', 'selectedLibraryId'));
    }
}
