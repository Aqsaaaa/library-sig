<?php

namespace App\Http\Controllers;

use App\Models\Library;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    // Menampilkan semua perpustakaan
    public function index()
    {
        $libraries = Library::with('books')->get();
        return view('libraries.index', compact('libraries'));
    }

    // Form tambah perpustakaan
    public function create()
    {
        return view('libraries.create');
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Library::create($validated);

        return redirect()->route('libraries.index')->with('success', 'Perpustakaan berhasil ditambahkan!');
    }

    // Tampilkan detail perpustakaan
    public function show(Library $library)
    {
        $library->load('books');
        return view('libraries.show', compact('library'));
    }

    // Form edit
    public function edit(Library $library)
    {
        return view('libraries.edit', compact('library'));
    }

    // Update data
    public function update(Request $request, Library $library)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $library->update($validated);

        return redirect()->route('libraries.index')->with('success', 'Data perpustakaan diperbarui.');
    }

    // Hapus data
    public function destroy(Library $library)
    {
        $library->delete();
        return redirect()->route('libraries.index')->with('success', 'Perpustakaan dihapus.');
    }
}
