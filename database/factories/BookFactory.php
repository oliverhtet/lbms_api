<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalCopies = $this->faker->numberBetween(1, 20);
        
        return [
            'title' => $this->faker->sentence(4),
            'isbn' => $this->faker->unique()->numerify('###-#-###-#####-#'),
            'published_year' => $this->faker->numberBetween(1900, date('Y')),
            'description' => $this->faker->paragraph(),
            'total_copies' => $totalCopies,
            'available_copies' => $totalCopies,
            'cover_image' => null,
        ];
    }
}