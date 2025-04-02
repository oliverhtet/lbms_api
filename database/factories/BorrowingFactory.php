<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Borrowing>
 */
class BorrowingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $borrowedDate = $this->faker->dateTimeBetween('-30 days', 'now');
        $dueDate = $this->faker->dateTimeBetween($borrowedDate, '+30 days');
        $isReturned = $this->faker->boolean(70);
        $returnedDate = $isReturned ? $this->faker->dateTimeBetween($borrowedDate, 'now') : null;
        
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'borrowed_date' => $borrowedDate,
            'due_date' => $dueDate,
            'returned_date' => $returnedDate,
            'status' => $isReturned ? 'returned' : ($dueDate < now() ? 'overdue' : 'borrowed'),
        ];
    }
}