<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'isbn',
        'published_year',
        'description',
        'total_copies',
        'available_copies',
        'cover_image',
    ];

    /**
     * Get the authors for the book.
     */
    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    /**
     * Get the categories for the book.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get the borrowings for the book.
     */
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * Check if the book is available for borrowing.
     */
    public function isAvailable()
    {
        return $this->available_copies > 0;
    }
}