<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();
        $slug = \Illuminate\Support\Str::slug($title);

        // If slug is empty (e.g., Thai characters), use a timestamp-based slug
        if (empty($slug)) {
            $slug = 'article-' . time() . '-' . uniqid();
        }

        return [
            'title' => rtrim($title, '.'),
            'slug' => $slug,
            'content' => fake()->paragraphs(5, true),
            'excerpt' => fake()->paragraph(),
            'category_id' => \App\Models\Category::factory(),
            'author_id' => \App\Models\User::factory(),
            'status' => fake()->randomElement(['draft', 'published']),
            'view_count' => fake()->numberBetween(0, 1000),
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }
}
