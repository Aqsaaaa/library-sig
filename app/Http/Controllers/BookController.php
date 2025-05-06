<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Library;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // Daftar buku (bisa difilter per perpustakaan)
    public function index()
    {
        $books = Book::with('libraries')->get();
        return view('books', compact('books'));
    }

    // Form tambah buku
    public function create()
    {
        $libraries = Library::all();
        return view('books.create', compact('libraries'));
    }

    // Simpan buku
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'published_at' => 'required|date',
            'libraries' => 'required|array',
            'libraries.*' => 'exists:libraries,id',
        ]);

        $book = Book::create($validated);

        $book->libraries()->sync($validated['libraries']);

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    // Tampilkan detail buku
    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }

    // Form edit buku
    public function edit(Book $book)
    {
        $libraries = Library::all();
        return view('books.edit', compact('book', 'libraries'));
    }

    // Update buku
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'published_at' => 'nullable|date',
        ]);

        $book->update($validated);


        return redirect()->route('books.index')->with('success', 'Buku diperbarui.');
    }

    // Hapus buku
    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('books.index')->with('success', 'Buku dihapus.');
    }
}
