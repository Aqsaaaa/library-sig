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
}
