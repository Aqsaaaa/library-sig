<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'published_at',
        'library_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function libraries()

    {
        return $this->belongsToMany(Library::class, 'book_library');
    }
}
