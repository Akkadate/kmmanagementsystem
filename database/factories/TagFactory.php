<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();
        $slug = \Illuminate\Support\Str::slug($name);

        // If slug is empty (e.g., Thai characters), use a timestamp-based slug
        if (empty($slug)) {
            $slug = 'tag-' . time() . '-' . uniqid();
        }

        return [
            'name' => ucfirst($name),
            'slug' => $slug,
        ];
    }
}
