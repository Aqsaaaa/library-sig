<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Library;
use Illuminate\Http\Request;

class AdminBookController extends Controller
{
    public function index()
    {
        $books = Book::with('libraries')->get();
        return view('admin.books.index', compact('books'));
    }

    public function create()
    {
        $libraries = Library::all();
        return view('admin.books.create', compact('libraries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'published_at' => 'nullable|date',
        ]);

        $book = Book::create($validated);

        return redirect()->route('admin.books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book)
    {
        $libraries = Library::all();
        return view('admin.books.edit', compact('book', 'libraries'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'published_at' => 'nullable|date',
        
        ]);

        $book->update($validated);

        $book->libraries()->sync($validated['libraries']);

        return redirect()->route('admin.books.index')->with('success', 'Buku diperbarui.');
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('admin.books.index')->with('success', 'Buku dihapus.');
    }
}
