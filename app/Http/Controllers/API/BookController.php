<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;


use App\Http\Requests\BookRequest;

use App\Models\Book;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Book::with(['authors', 'categories']);

        // Filter by title
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Filter by author
        if ($request->has('author_id')) {
            $query->whereHas('authors', function ($q) use ($request) {
                $q->where('authors.id', $request->author_id);
            });
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Filter by availability
        if ($request->has('available') && $request->available) {
            $query->where('available_copies', '>', 0);
        }

        // Paginate results
        $books = $query->paginate($request->per_page ?? 15);

        return response()->json($books);
    }

    /**
     * Store a newly created book in storage.
     *
     * @param  \App\Http\Requests\BookRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BookRequest $request)
    {
        // Handle cover image upload
        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('covers', 'public');
        }

        // Create book
        $book = Book::create([
            'title' => $request->title,
            'isbn' => $request->isbn,
            'published_year' => $request->published_year,
            'description' => $request->description,
            'total_copies' => $request->total_copies,
            'available_copies' => $request->total_copies,
            'cover_image' => $coverImagePath,
        ]);

        // Attach authors and categories
        $book->authors()->attach($request->authors);
        $book->categories()->attach($request->categories);

        // Load relationships
        $book->load(['authors', 'categories']);

        return response()->json([
            'message' => 'Book created successfully',
            'book' => $book
        ], 201);
    }

    /**
     * Display the specified book.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::with(['authors', 'categories'])->findOrFail($id);

        return response()->json($book);
    }

    /**
     * Update the specified book in storage.
     *
     * @param  \App\Http\Requests\BookRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BookRequest $request, $id)
    {
        $book = Book::findOrFail($id);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $coverImagePath = $request->file('cover_image')->store('covers', 'public');
            $book->cover_image = $coverImagePath;
        }

        // Update book
        $book->fill($request->only([
            'title', 'isbn', 'published_year', 'description', 'total_copies'
        ]));

        // Adjust available copies if total copies changed
        if ($request->has('total_copies')) {
            $borrowed = $book->total_copies - $book->available_copies;
            $book->available_copies = max(0, $request->total_copies - $borrowed);
        }

        $book->save();

        // Update authors and categories if provided
        if ($request->has('authors')) {
            $book->authors()->sync($request->authors);
        }

        if ($request->has('categories')) {
            $book->categories()->sync($request->categories);
        }

        // Load relationships
        $book->load(['authors', 'categories']);

        return response()->json([
            'message' => 'Book updated successfully',
            'book' => $book
        ]);
    }

    /**
     * Remove the specified book from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        // Check if book has active borrowings
        if ($book->borrowings()->whereNull('returned_date')->exists()) {
            return response()->json([
                'message' => 'Cannot delete book with active borrowings'
            ], 400);
        }

        // Delete cover image if exists
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        // Delete book
        $book->authors()->detach();
        $book->categories()->detach();
        $book->delete();

        return response()->json([
            'message' => 'Book deleted successfully'
        ]);
    }
}