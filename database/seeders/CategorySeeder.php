<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Main category: Technical Documentation
        $technical = Category::updateOrCreate(
            ['slug' => 'technical-documentation'],
            [
                'name' => 'Technical Documentation',
                'description' => 'Technical guides and documentation',
                'sort_order' => 1,
            ]
        );

        // Subcategories under Technical Documentation
        Category::updateOrCreate(
            ['slug' => 'api-reference'],
            [
                'name' => 'API Reference',
                'description' => 'API documentation and references',
                'parent_id' => $technical->id,
                'sort_order' => 1,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'getting-started'],
            [
                'name' => 'Getting Started',
                'description' => 'Getting started guides',
                'parent_id' => $technical->id,
                'sort_order' => 2,
            ]
        );

        // Main category: User Guides
        $userGuides = Category::updateOrCreate(
            ['slug' => 'user-guides'],
            [
                'name' => 'User Guides',
                'description' => 'End-user documentation',
                'sort_order' => 2,
            ]
        );

        // Subcategories under User Guides
        Category::updateOrCreate(
            ['slug' => 'tutorials'],
            [
                'name' => 'Tutorials',
                'description' => 'Step-by-step tutorials',
                'parent_id' => $userGuides->id,
                'sort_order' => 1,
            ]
        );

        // Main category: FAQ
        Category::updateOrCreate(
            ['slug' => 'faq'],
            [
                'name' => 'FAQ',
                'description' => 'Frequently asked questions',
                'sort_order' => 3,
            ]
        );

        // Main category: Troubleshooting
        Category::updateOrCreate(
            ['slug' => 'troubleshooting'],
            [
                'name' => 'Troubleshooting',
                'description' => 'Common issues and solutions',
                'sort_order' => 4,
            ]
        );
    }
}
