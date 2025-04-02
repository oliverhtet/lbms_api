<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BorrowingRequest;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    

    /**
     * Display a listing of the borrowings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Borrowing::with(['user', 'book']);

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by book
        if ($request->has('book_id')) {
            $query->where('book_id', $request->book_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by overdue
        if ($request->has('overdue') && $request->overdue) {
            $query->whereNull('returned_date')
                  ->where('due_date', '<', now());
        }

        // Paginate results
        $borrowings = $query->paginate($request->per_page ?? 15);

        return response()->json($borrowings);
    }

    /**
     * Store a newly created borrowing in storage.
     *
     * @param  \App\Http\Requests\BorrowingRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BorrowingRequest $request)
    {
        // Check if book is available
        $book = Book::findOrFail($request->book_id);
        if ($book->available_copies <= 0) {
            return response()->json([
                'message' => 'Book is not available for borrowing'
            ], 400);
        }

        // Check if user has reached borrowing limit
        $user = User::findOrFail($request->user_id);
        $activeBorrowings = $user->borrowings()->whereNull('returned_date')->count();
        
        // Assuming a limit of 5 books per user
        if ($activeBorrowings >= 5) {
            return response()->json([
                'message' => 'User has reached the maximum borrowing limit'
            ], 400);
        }

        // Check if user already has this book
        $existingBorrowing = Borrowing::where('user_id', $request->user_id)
            ->where('book_id', $request->book_id)
            ->whereNull('returned_date')
            ->exists();
            
        if ($existingBorrowing) {
            return response()->json([
                'message' => 'User already has this book borrowed'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Create borrowing
            $borrowing = Borrowing::create([
                'user_id' => $request->user_id,
                'book_id' => $request->book_id,
                'borrowed_date' => $request->borrowed_date ?? now(),
                'due_date' => $request->due_date,
                'status' => 'borrowed',
            ]);

            // Update book available copies
            $book->available_copies -= 1;
            $book->save();

            DB::commit();

            // Load relationships
            $borrowing->load(['user', 'book']);

            return response()->json([
                'message' => 'Book borrowed successfully',
                'borrowing' => $borrowing
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to borrow book',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified borrowing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $borrowing = Borrowing::with(['user', 'book'])->findOrFail($id);

        return response()->json($borrowing);
    }

    /**
     * Update the specified borrowing in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $borrowing = Borrowing::findOrFail($id);

        $request->validate([
            'due_date' => 'sometimes|required|date|after:borrowed_date',
            'status' => 'sometimes|required|in:borrowed,returned,overdue',
        ]);

        $borrowing->update($request->only(['due_date', 'status']));

        return response()->json([
            'message' => 'Borrowing updated successfully',
            'borrowing' => $borrowing
        ]);
    }

    /**
     * Return a borrowed book.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function returnBook(Request $request, $id)
    {
        $borrowing = Borrowing::findOrFail($id);

        // Check if book is already returned
        if ($borrowing->returned_date !== null) {
            return response()->json([
                'message' => 'Book is already returned'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Update borrowing
            $borrowing->returned_date = $request->returned_date ?? now();
            $borrowing->status = 'returned';
            $borrowing->save();

            // Update book available copies
            $book = $borrowing->book;
            $book->available_copies += 1;
            $book->save();

            DB::commit();

            // Load relationships
            $borrowing->load(['user', 'book']);

            return response()->json([
                'message' => 'Book returned successfully',
                'borrowing' => $borrowing
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to return book',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified borrowing from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $borrowing = Borrowing::findOrFail($id);

        // Check if book is not returned yet
        if ($borrowing->returned_date === null) {
            return response()->json([
                'message' => 'Cannot delete active borrowing. Return the book first.'
            ], 400);
        }

        $borrowing->delete();

        return response()->json([
            'message' => 'Borrowing deleted successfully'
        ]);
    }

    /**
     * Get borrowings for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function userBorrowings(Request $request)
    {
        $user = $request->user();
        $query = $user->borrowings()->with('book');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by overdue
        if ($request->has('overdue') && $request->overdue) {
            $query->whereNull('returned_date')
                  ->where('due_date', '<', now());
        }

        // Paginate results
        $borrowings = $query->paginate($request->per_page ?? 15);

        return response()->json($borrowings);
    }
}