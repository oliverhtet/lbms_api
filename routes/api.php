<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AuthorController;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\BorrowingController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Books
    Route::apiResource('books', BookController::class);
    
    // Authors
    Route::apiResource('authors', AuthorController::class);
    
    // Categories
    Route::apiResource('categories', CategoryController::class);
    
    // Borrowings
    Route::apiResource('borrowings', BorrowingController::class);
    Route::post('/borrowings/{id}/return', [BorrowingController::class, 'returnBook']);
    Route::get('/my-borrowings', [BorrowingController::class, 'userBorrowings']);
    
    // Users (admin only)
    Route::apiResource('users', UserController::class)->middleware('role:admin');
});