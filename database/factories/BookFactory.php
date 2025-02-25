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
        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'description' => $this->faker->paragraph(3),
            'publication_date' => $this->faker->date(),
            'isbn' => $this->faker->randomFloat(0, 1, 10),
            'image' => $this->faker->imageUrl(200, 300, 'books'),
            'genre_id' => rand(1, 5)
        ];
    }
}
