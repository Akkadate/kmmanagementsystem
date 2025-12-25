<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $technical = Category::create([
            'name' => 'Technical Documentation',
            'slug' => 'technical-documentation',
            'description' => 'Technical guides and documentation',
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'API Reference',
            'slug' => 'api-reference',
            'description' => 'API documentation and references',
            'parent_id' => $technical->id,
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'Getting Started',
            'slug' => 'getting-started',
            'description' => 'Getting started guides',
            'parent_id' => $technical->id,
            'sort_order' => 2,
        ]);

        $userGuides = Category::create([
            'name' => 'User Guides',
            'slug' => 'user-guides',
            'description' => 'End-user documentation',
            'sort_order' => 2,
        ]);

        Category::create([
            'name' => 'Tutorials',
            'slug' => 'tutorials',
            'description' => 'Step-by-step tutorials',
            'parent_id' => $userGuides->id,
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'FAQ',
            'slug' => 'faq',
            'description' => 'Frequently asked questions',
            'sort_order' => 3,
        ]);

        Category::create([
            'name' => 'Troubleshooting',
            'slug' => 'troubleshooting',
            'description' => 'Common issues and solutions',
            'sort_order' => 4,
        ]);
    }
}
