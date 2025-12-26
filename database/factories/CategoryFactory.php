<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        $slug = \Illuminate\Support\Str::slug($name);

        // If slug is empty (e.g., Thai characters), use a timestamp-based slug
        if (empty($slug)) {
            $slug = 'category-' . time() . '-' . uniqid();
        }

        return [
            'name' => ucwords($name),
            'slug' => $slug,
            'description' => fake()->sentence(),
            'parent_id' => null,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function withParent($parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }
}
