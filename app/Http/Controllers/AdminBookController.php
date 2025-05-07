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
            'publisher' => 'required|string|max:255',
            'description' => 'required|string',
            'published_at' => 'required|date',
            'image' => 'required|image|max:2048',
            'total_pages' => 'required|integer|min:1',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('book_images', 'public');
            $validated['image'] = $path;
        }

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
            'publisher' => 'required|string|max:255',
            'description' => 'nullable|string',
            'published_at' => 'required|date',
            'image' => 'required|image|max:2048',
            'total_pages' => 'required|integer|min:1',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($book->image && \Storage::disk('public')->exists($book->image)) {
                \Storage::disk('public')->delete($book->image);
            }
            $path = $request->file('image')->store('book_images', 'public');
            $validated['image'] = $path;
        }

        $book->update($validated);

        $book->libraries()->sync($request->input('libraries', []));

        return redirect()->route('admin.books.index')->with('success', 'Buku diperbarui.');
    }

    public function show(Book $book)
    {
        $book->load('libraries');
        return view('admin.books.show', compact('book'));
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('admin.books.index')->with('success', 'Buku dihapus.');
    }
}
