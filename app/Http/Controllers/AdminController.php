<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Library;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Fetch all libraries with their location data
        $libraries = Library::select('id', 'name', 'latitude', 'longitude', 'address')->get();

        return view('admin.dashboard', compact('libraries'));
    }
}
