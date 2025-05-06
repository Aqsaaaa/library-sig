<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\AdminBookController;
use App\Http\Controllers\AdminLibraryController;


Route::get('/', [AuthController::class, 'dashboard'])->middleware('auth')->name('dashboard');

//AUTH ROUTES
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


//ADMIN ROUTES
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('books', AdminBookController::class)->except(['show']);
    Route::resource('libraries', AdminLibraryController::class)->except(['show']);
    Route::post('libraries/add-book', [AdminLibraryController::class, 'addBook'])->name('libraries.addBook');
    Route::get('libraries/add-book', [AdminLibraryController::class, 'showAddBookForm'])->name('libraries.addBookForm');
});
